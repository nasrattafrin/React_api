<?php
// withdraw_requests/index.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$where = ["w.deleted_at IS NULL"];


if (isset($access_user_role) && (int)$access_user_role === 2) {
    $where[] = "w.merchant_id = " . (int)$access_user_id;
}

$res = $cc->common_query("
    SELECT w.id, w.amount, w.method, w.account_number, w.status, 
           w.requested_at, w.processed_at, u.name AS merchant_name
    FROM withdraw_requests w
    JOIN users u ON u.id = w.merchant_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY w.id DESC
");

$rows = $res['status'] ? $res['data'] : [];

echo json_encode([
    'status'   => true,
    'data'     => $rows,
    'requests' => $rows 
]);