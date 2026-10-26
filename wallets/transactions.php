<?php
// wallets/transactions.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$wallet_id = (int) ($_GET['wallet_id'] ?? 0);
if ($wallet_id <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'wallet_id is required.']);
    exit;
}

// Merchant Security Check
if (isset($access_user_role) && (int)$access_user_role === 2) {
    $checkOwner = $cc->common_query("SELECT id FROM wallets WHERE id = $wallet_id AND merchant_id = $access_user_id");
    if (!$checkOwner['status'] || empty($checkOwner['data'])) {
        http_response_code(403);
        echo json_encode(['status' => false, 'message' => 'Unauthorized access.']);
        exit;
    }
}

$res = $cc->common_query("
    SELECT id, type, amount, reference, description, created_at
    FROM wallet_transactions
    WHERE wallet_id = $wallet_id AND deleted_at IS NULL
    ORDER BY created_at DESC
");

echo json_encode(['status' => true, 'data' => $res['status'] ? $res['data'] : []]);