<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$res = $cc->common_query("
    SELECT id, name, email 
    FROM users 
    WHERE role_id = 2 AND deleted_at IS NULL 
    ORDER BY name ASC
");
$merchants = $res['status'] ? $res['data'] : [];
echo json_encode(['status' => true, 'merchants' => $merchants]);