<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'id is required.']);
    exit;
}
$sql = "UPDATE cod_settlements 
        SET deleted_at = NOW(), updated_by = " . (int)$access_user_id . " 
        WHERE id = " . (int)$id;
$res = $cc->common_query($sql);

if ($res['status'] === false && strpos($res['message'], 'Error') !== false) {
    echo json_encode(['status' => false, 'message' => 'Failed to delete: ' . $res['message']]);
    exit;
}
echo json_encode(['status' => true, 'message' => 'Settlement deleted.']);