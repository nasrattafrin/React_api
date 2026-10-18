<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }
    $res = $cc->common_query("
        SELECT pay.*, p.tracking_id,p.receiver_name,u.name AS created_by_name
        FROM payments pay
        JOIN parcels p    ON p.id = pay.parcel_id
        LEFT JOIN users u ON u.id = pay.created_by
        WHERE pay.id = $id AND pay.deleted_at IS NULL
        LIMIT 1
    ");
    
    if (!$res['status'] || empty($res['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Payment not found.']);
        exit;
    }
    $row = $res['data'][0];
    $row->method_label = payment_method_label((int) $row->method);
    $row->status_label = payment_status_label((int) $row->status);
    echo json_encode(['status' => true, 'data' => $row, 'payment' => $row]);
    exit;
}
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($input['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }
    $updateData = [];
    if (isset($input['status'])) {
        $status = (int) $input['status'];
        if (!in_array($status, [0, 1, 2], true)) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Invalid status.']);
            exit;
        }
        $updateData['status'] = $status;
    }
    if (isset($input['transaction_id'])) {
        $updateData['transaction_id'] = trim($input['transaction_id']);
    }
    if (empty($updateData)) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Nothing to update.']);
        exit;
    }
    $updateData['updated_by'] = (int) $access_user_id;
    $cc->common_update('payments', $updateData, ['id' => $id]);
    echo json_encode(['status' => true, 'message' => 'Payment updated.']);
    exit;
}
http_response_code(405);
echo json_encode(['status' => false, 'message' => 'Method not allowed.']);