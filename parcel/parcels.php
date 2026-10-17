<?php
// parcel/parcels.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/parcel_helper.php';

$method = $_SERVER['REQUEST_METHOD'];

// ================= GET =================
if ($method === 'GET') {

    // ---- Single parcel + timeline ----
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $sql = "SELECT p.*,
                       s.name AS sender_name, s.contact_no AS sender_phone,
                       z.zone_name,
                       d.name AS deliveryman_name, d.contact_no AS deliveryman_phone
                FROM parcels p
                JOIN users s      ON s.id = p.sender_id
                JOIN zones z      ON z.id = p.zone_id
                LEFT JOIN users d ON d.id = p.assigned_to
                WHERE p.id = $id AND p.deleted_at IS NULL
                LIMIT 1";
        $res = $cc->common_query($sql);
        if (!$res['status'] || empty($res['data'])) {
            parcel_json(404, ['status' => false, 'message' => 'Parcel not found.']);
        }
        $parcel = $res['data'][0];

        $logRes = $cc->common_query("
            SELECT l.id, l.status, l.note, l.created_at,
                   u.name AS actor_name
            FROM parcel_logs l
            LEFT JOIN users u ON u.id = l.created_by
            WHERE l.parcel_id = $id AND l.deleted_at IS NULL
            ORDER BY l.created_at ASC, l.id ASC
        ");
        $logs = $logRes['status'] ? $logRes['data'] : [];

        foreach ($logs as &$l) {
            $l->status_label = parcel_status_label((int) $l->status);
        }
        $parcel->status_label = parcel_status_label((int) $parcel->status);

        parcel_json(200, ['status' => true, 'parcel' => $parcel, 'logs' => $logs]);
    }

    // ---- List with filters ----
    $where = ["p.deleted_at IS NULL"];
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $where[] = "p.status = " . (int) $_GET['status'];
    }
    if (isset($_GET['zone_id']) && $_GET['zone_id'] !== '') {
        $where[] = "p.zone_id = " . (int) $_GET['zone_id'];
    }
    if (!empty($_GET['search'])) {
        $s = $cc->conn->real_escape_string(trim($_GET['search']));
        $where[] = "(p.tracking_id LIKE '%$s%' OR p.receiver_phone LIKE '%$s%' OR p.receiver_name LIKE '%$s%')";
    }

    $limit  = min(100, max(1, (int) ($_GET['limit'] ?? 50)));
    $offset = max(0, (int) ($_GET['offset'] ?? 0));

    $sql = "SELECT p.id, p.tracking_id, p.receiver_name, p.receiver_phone,
                   p.weight, p.delivery_charge, p.cod_amount,
                   p.status, p.payment_status, p.created_at,
                   p.assigned_to,
                   s.name AS sender_name,
                   z.zone_name,
                   d.name AS deliveryman_name
            FROM parcels p
            JOIN users s      ON s.id = p.sender_id
            JOIN zones z      ON z.id = p.zone_id
            LEFT JOIN users d ON d.id = p.assigned_to
            WHERE " . implode(' AND ', $where) . "
            ORDER BY p.created_at DESC
            LIMIT $limit OFFSET $offset";

    $res = $cc->common_query($sql);
    $parcels = $res['status'] ? $res['data'] : [];
    foreach ($parcels as &$p) {
        $p->status_label = parcel_status_label((int) $p->status);
    }

    parcel_json(200, ['status' => true, 'parcels' => $parcels, 'limit' => $limit, 'offset' => $offset]);
}

// ================= POST (create) =================
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];

    $receiverName  = trim($input['receiver_name']  ?? '');
    $receiverPhone = trim($input['receiver_phone'] ?? '');
    $pickup        = trim($input['pickup_address'] ?? '');
    $delivery      = trim($input['delivery_address'] ?? '');
    $weight        = (float) ($input['weight']    ?? 0);
    $zoneId        = (int)   ($input['zone_id']   ?? 0);
    $codAmount     = (float) ($input['cod_amount'] ?? 0);

    if ($receiverName === '' || $receiverPhone === '' || $delivery === '') {
        parcel_json(422, ['status' => false, 'message' => 'Receiver name, phone, and delivery address are required.']);
    }
    if ($weight <= 0 || $weight > 100) {
        parcel_json(422, ['status' => false, 'message' => 'Weight must be between 0.1 and 100 kg.']);
    }
    if ($zoneId <= 0) {
        parcel_json(422, ['status' => false, 'message' => 'Zone is required.']);
    }

    $charge = parcel_calculate_charge($cc, $zoneId, $weight);
    if ($charge <= 0) {
        parcel_json(422, ['status' => false, 'message' => 'No pricing rule found for this zone/weight.']);
    }

    $trackingId = parcel_generate_tracking_id();

    $insert = $cc->common_insert('parcels', [
        'tracking_id'      => $trackingId,
        'sender_id'        => (int) $access_user_id,
        'receiver_name'    => $receiverName,
        'receiver_phone'   => $receiverPhone,
        'pickup_address'   => $pickup,
        'delivery_address' => $delivery,
        'weight'           => $weight,
        'zone_id'          => $zoneId,
        'delivery_charge'  => $charge,
        'cod_amount'       => $codAmount,
        'status'           => 0,
        'payment_status'   => 0,
        'created_by'       => (int) $access_user_id,
    ]);

    if (!$insert['status']) {
        parcel_json(500, ['status' => false, 'message' => 'Could not create parcel.']);
    }

    $parcelId = (int) $insert['data'];
    parcel_add_log($cc, $parcelId, 0, 'Parcel created', (int) $access_user_id);

    parcel_json(201, [
        'status'  => true,
        'message' => 'Parcel created successfully.',
        'parcel'  => [
            'id'              => $parcelId,
            'tracking_id'     => $trackingId,
            'delivery_charge' => $charge,
        ],
    ]);
}

// ================= PATCH (status / assign) =================
if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($input['id'] ?? 0);
    if ($id <= 0) parcel_json(422, ['status' => false, 'message' => 'id is required.']);

    $res = $cc->common_query("SELECT status FROM parcels WHERE id = $id AND deleted_at IS NULL LIMIT 1");
    if (!$res['status'] || empty($res['data'])) {
        parcel_json(404, ['status' => false, 'message' => 'Parcel not found.']);
    }
    $currentStatus = (int) $res['data'][0]->status;

    if (in_array($currentStatus, [3, 4], true)) {
        parcel_json(409, ['status' => false, 'message' => 'This parcel is already finalised.']);
    }

    if (isset($input['status'])) {
        $newStatus = (int) $input['status'];
        if (!in_array($newStatus, [0, 1, 2, 3, 4], true)) {
            parcel_json(422, ['status' => false, 'message' => 'Invalid status.']);
        }
        $note = trim($input['note'] ?? '');

        $cc->common_update('parcels',
            ['status' => $newStatus, 'updated_by' => (int) $access_user_id],
            ['id' => $id]
        );

        parcel_add_log($cc, $id, $newStatus, $note !== '' ? $note : 'Status updated', (int) $access_user_id);

        if ($newStatus === 3) {
            $cc->common_query("UPDATE parcels SET payment_status = IF(cod_amount > 0, 1, payment_status) WHERE id = $id");
        }
    }

    if (array_key_exists('assigned_to', $input)) {
        if ($input['assigned_to'] === null || $input['assigned_to'] === '') {
            $cc->common_query("UPDATE parcels SET assigned_to = NULL, updated_by = " . (int)$access_user_id . " WHERE id = $id");
            parcel_add_log($cc, $id, $currentStatus, 'Unassigned', (int) $access_user_id);
        } else {
            $assignedTo = (int) $input['assigned_to'];
            $cc->common_update('parcels',
                ['assigned_to' => $assignedTo, 'updated_by' => (int) $access_user_id],
                ['id' => $id]
            );
            parcel_add_log($cc, $id, $currentStatus, 'Assigned to deliveryman #' . $assignedTo, (int) $access_user_id);
        }
    }

    parcel_json(200, ['status' => true, 'message' => 'Parcel updated.']);
}

// ================= DELETE (cancel) =================
if ($method === 'DELETE') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) parcel_json(422, ['status' => false, 'message' => 'id is required.']);

    $res = $cc->common_query("SELECT status FROM parcels WHERE id = $id AND deleted_at IS NULL LIMIT 1");
    if (!$res['status'] || empty($res['data'])) {
        parcel_json(404, ['status' => false, 'message' => 'Parcel not found.']);
    }
    if ((int) $res['data'][0]->status === 3) {
        parcel_json(409, ['status' => false, 'message' => 'Delivered parcel cannot be cancelled.']);
    }

    $cc->common_update('parcels',
        ['status' => 4, 'updated_by' => (int) $access_user_id],
        ['id' => $id]
    );
    parcel_add_log($cc, $id, 4, 'Parcel cancelled', (int) $access_user_id);

    parcel_json(200, ['status' => true, 'message' => 'Parcel cancelled.']);
}

parcel_json(405, ['status' => false, 'message' => 'Method not allowed.']);