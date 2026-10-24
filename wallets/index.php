<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$res = $cc->common_query("
    SELECT w.id, w.merchant_id, w.balance, w.created_at, w.updated_at,
           u.name AS merchant_name, u.contact_no
    FROM wallets w
    JOIN users u ON u.id = w.merchant_id
    WHERE w.deleted_at IS NULL
    ORDER BY w.balance DESC
");

echo json_encode(['status' => true, 'data' => $res['status'] ? $res['data'] : []]);