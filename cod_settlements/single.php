<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';
$method=$_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false,'message' =>'id is required.']);
        exit;
    }
    $res = $cc->common_query("
        SELECT cs.*, p.tracking_id,
               d.name AS deliveryman_name,
               m.name AS merchant_name
        FROM cod_settlements cs
        JOIN parcels p ON p.id = cs.parcel_id
        JOIN users d   ON d.id = cs.deliveryman_id
        JOIN users m   ON m.id = cs.merchant_id
        WHERE cs.id = $id AND cs.deleted_at IS NULL
        LIMIT 1
    ");
    if (!$res['status'] || empty($res['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Settlement not found.']);
        exit;
    }
    $row = $res['data'][0];
    $labels = [0 => 'Collected', 1 => 'Received',2 =>'Settled'];
    $row->status_label = $labels[(int) $row->status]??'Unknown';
    echo json_encode(['status' => true, 'data' => $row,'settlement' => $row]);
    exit;
}
if ($method === 'PUT') {
    $input=json_decode(file_get_contents('php://input'), true) ?:[];
    $id=(int) ($input['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }
    $status = (int) ($input['status'] ?? -1);
    if (!in_array($status, [0, 1, 2], true)) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Invalid status.']);
        exit;
    }
    $updateData = [
        'status'     => $status,
        'updated_by' => (int) $access_user_id,
    ];
    if ($status === 2) {
        $updateData['settled_at'] = date('Y-m-d H:i:s');
    }
    $cc->common_update('cod_settlements', $updateData, ['id' => $id]);
    echo json_encode(['status' => true, 'message' => 'Settlement updated.']);
    exit;
}
http_response_code(405);
echo json_encode(['status' => false, 'message' => 'Method not allowed.']);