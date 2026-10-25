<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$merchantId = (int) ($input['merchant_id'] ?? 0);
$amount     = (float) ($input['amount'] ?? 0);
$method     = (int) ($input['method'] ?? 0);
$account    = trim($input['account_number'] ?? '');
if ($merchantId <= 0 || $amount <= 0 || $account === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Merchant, Amount and Account Number are required.']);
    exit;
}
$walletRes = $cc->common_query("SELECT balance FROM wallets WHERE merchant_id = $merchantId AND deleted_at IS NULL LIMIT 1");
if (!$walletRes['status'] || empty($walletRes['data'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Merchant wallet not found or balance is 0.']);
    exit;
}
$walletBalance = (float) $walletRes['data'][0]->balance;
if ($amount > $walletBalance) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => "Insufficient balance. Available: ৳$walletBalance"]);
    exit;
}
$insert = $cc->common_insert('withdraw_requests', [
    'merchant_id'    => $merchantId,
    'amount'         => $amount,
    'method'         => $method,
    'account_number' => $account,
    'status'         => 0, // Pending
    'requested_at'   => date('Y-m-d H:i:s'),
    'created_by'     => (int) $access_user_id
]);
if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create request.']);
    exit;
}
echo json_encode(['status' => true, 'message' => 'Withdraw request created successfully.']);