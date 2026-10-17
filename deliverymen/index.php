<?php
// deliverymen/index.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$res = $cc->common_query("
    SELECT u.id, u.name, u.contact_no, u.email,
           u.branch_id, u.status, u.created_at,
           b.branch_name
    FROM users u
    LEFT JOIN branches b ON b.id = u.branch_id
    WHERE u.role_id = 3 AND u.deleted_at IS NULL
    ORDER BY u.name ASC
");

$rows = $res['status'] ? $res['data'] : [];

echo json_encode([
    'status'      => true,
    'data'        => $rows,
    'deliverymen' => $rows,
]);