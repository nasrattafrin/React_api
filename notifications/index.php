<?php
// notifications/index.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$where = ["n.deleted_at IS NULL"];
if (isset($access_user_role) && (int)$access_user_role === 2) {
    $where[] = "n.user_id = " . (int)$access_user_id;
}
$res = $cc->common_query("
    SELECT n.id, n.type, n.message, n.status, n.created_at, u.name AS user_name, u.role_id
    FROM notifications n
    JOIN users u ON u.id = n.user_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY n.id DESC
");
$rows = $res['status'] ? $res['data'] : [];
echo json_encode(['status' => true, 'data' => $rows]);