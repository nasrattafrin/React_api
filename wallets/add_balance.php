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
if ($merchantId <= 0 || $amount <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Merchant and valid amount are required.']);
    exit;
}
$walletRes = $cc->common_query("SELECT id, balance FROM wallets WHERE merchant_id = $merchantId AND deleted_at IS NULL LIMIT 1");

if (!$walletRes['status'] || empty($walletRes['data'])) {
    $walletInsert = $cc->common_insert('wallets', [
        'merchant_id' => $merchantId,
        'balance'     => $amount,
        'created_by'  => (int) $access_user_id
    ]);
    $walletId = $walletInsert['data'];
} else {
    $wallet = $walletRes['data'][0];
    $walletId = $wallet->id;
    $newBalance = (float)$wallet->balance + $amount;
    
    $cc->common_update('wallets', [
        'balance' => $newBalance,
        'updated_by' => (int) $access_user_id
    ], ['id' => $walletId]);
}
$cc->common_insert('wallet_transactions', [
    'wallet_id' => $walletId,
    'type' => 0, // 0 = CREDIT
    'amount' => $amount,
    'reference' => 'ADMIN-DEPOSIT',
    'description' => 'Manual balance added by Admin',
    'created_by' => (int) $access_user_id
]);

echo json_encode(['status' => true, 'message' => 'Balance added successfully.']);