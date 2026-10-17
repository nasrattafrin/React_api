<?php
// parcel/parcel_track.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/parcel_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    parcel_json(405, ['status' => false, 'message' => 'Method not allowed.']);
}

$trackingId = trim($_GET['tracking_id'] ?? '');
if ($trackingId === '') {
    parcel_json(422, ['status' => false, 'message' => 'tracking_id is required.']);
}

$tid = $cc->conn->real_escape_string($trackingId);

$sql = "SELECT p.tracking_id, p.receiver_name, p.delivery_address,
               p.status, p.weight, p.delivery_charge, p.cod_amount,
               p.created_at, z.zone_name
        FROM parcels p
        JOIN zones z ON z.id = p.zone_id
        WHERE p.tracking_id = '$tid' AND p.deleted_at IS NULL
        LIMIT 1";
$res = $cc->common_query($sql);
if (!$res['status'] || empty($res['data'])) {
    parcel_json(404, ['status' => false, 'message' => 'Parcel not found.']);
}
$parcel = $res['data'][0];
$parcel->status_label = parcel_status_label((int) $parcel->status);

$logRes = $cc->common_query("
    SELECT l.status, l.note, l.created_at
    FROM parcel_logs l
    WHERE l.parcel_id = (SELECT id FROM parcels WHERE tracking_id = '$tid' LIMIT 1)
      AND l.deleted_at IS NULL
    ORDER BY l.created_at ASC
");
$logs = $logRes['status'] ? $logRes['data'] : [];
foreach ($logs as &$l) {
    $l->status_label = parcel_status_label((int) $l->status);
}

parcel_json(200, ['status' => true, 'parcel' => $parcel, 'logs' => $logs]);