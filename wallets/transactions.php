<?php
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

$res = $cc->common_query("
    SELECT id, type, amount, reference, description, created_at, created_by
    FROM wallet_transactions
    WHERE wallet_id = $wallet_id AND deleted_at IS NULL
    ORDER BY created_at DESC
");

echo json_encode(['status' => true, 'data' => $res['status'] ? $res['data'] : []]);