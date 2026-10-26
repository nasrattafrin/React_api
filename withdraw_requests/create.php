<?php
// withdraw_requests/create.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
if (!isset($access_user_role) || (int)$access_user_role !== 2) {
    http_response_code(403);
    echo json_encode(['status' => false, 'message' => 'Only merchants can create withdraw requests.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$merchantId = (int) $access_user_id; 
$amount     = (float) ($input['amount'] ?? 0);
$method     = (int) ($input['method'] ?? 0);
$account    = trim($input['account_number'] ?? '');

if ($amount <= 0 || $account === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Amount and Account Number are required.']);
    exit;
}

$walletRes = $cc->common_query("SELECT id, balance FROM wallets WHERE merchant_id = $merchantId AND deleted_at IS NULL LIMIT 1");
if (!$walletRes['status'] || empty($walletRes['data'])) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Wallet not found.']);
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
    'status'         => 0, // 0 = Pending
    'requested_at'   => date('Y-m-d H:i:s'),
    'created_by'     => (int) $access_user_id
]);

if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create request.']);
    exit;
}

echo json_encode(['status' => true, 'message' => 'Withdraw request submitted successfully.']);