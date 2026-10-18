<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$parcelId      = (int)   ($input['parcel_id']      ?? 0);
$amount        = (float) ($input['amount']         ?? -1);
$method        = (int)   ($input['method']         ?? -1);
$transactionId = trim($input['transaction_id'] ?? '');
$status        = (int)   ($input['status']         ?? 0);
if ($parcelId <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'parcel_id is required.']);
    exit;
}
if ($amount <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Amount must be greater than 0.']);
    exit;
}
if (!in_array($method, [0, 1, 2, 3], true)) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Invalid payment method.']);
    exit;
}
if (!in_array($status, [0, 1, 2], true)) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Invalid status.']);
    exit;
}
$check = $cc->common_query("SELECT id FROM parcels WHERE id = $parcelId AND deleted_at IS NULL LIMIT 1");
if (!$check['status'] || empty($check['data'])) {
    http_response_code(404);
    echo json_encode(['status' => false, 'message' => 'Parcel not found.']);
    exit;
}
if ($transactionId !== '') {
    $t = $cc->conn->real_escape_string($transactionId);
    $dup = $cc->common_query("SELECT id FROM payments WHERE transaction_id = '$t' AND deleted_at IS NULL LIMIT 1");
    if ($dup['status'] && !empty($dup['data'])) {
        http_response_code(409);
        echo json_encode(['status' => false, 'message' => 'This transaction ID already exists.']);
        exit;
    }
}
$insert = $cc->common_insert('payments', [
    'parcel_id'      => $parcelId,
    'amount'         => $amount,
    'method'         => $method,
    'transaction_id' => $transactionId !== '' ? $transactionId : null,
    'status'         => $status,
    'created_by'     => (int) $access_user_id,
]);
if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create payment.']);
    exit;
}
if ($status === 1) {
    $dueRes = $cc->common_query("
        SELECT (p.delivery_charge + p.cod_amount) AS total_due,
               COALESCE(SUM(CASE WHEN pay.status = 1 THEN pay.amount ELSE 0 END), 0) AS total_paid
        FROM parcels p
        LEFT JOIN payments pay ON pay.parcel_id = p.id AND pay.deleted_at IS NULL
        WHERE p.id = $parcelId
        GROUP BY p.id
    ");
    if ($dueRes['status'] && !empty($dueRes['data'])) {
        $row = $dueRes['data'][0];
        if ((float)$row->total_paid >= (float)$row->total_due) {
            $cc->common_query("UPDATE parcels SET payment_status = 1 WHERE id = $parcelId");
        }
    }
}
echo json_encode([
    'status'  => true,
    'message' => 'Payment recorded.',
    'id'      => (int) $insert['data'],
]);