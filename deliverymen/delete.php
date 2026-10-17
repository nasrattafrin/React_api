<?php
// deliverymen/delete.php
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

// Ensure this user is a deliveryman
$check = $cc->common_query("SELECT id FROM users WHERE id = $id AND role_id = 3 AND deleted_at IS NULL LIMIT 1");
if (!$check['status'] || empty($check['data'])) {
    http_response_code(404);
    echo json_encode(['status' => false, 'message' => 'Deliveryman not found.']);
    exit;
}

$cc->common_query("UPDATE users SET deleted_at = NOW() WHERE id = $id");

echo json_encode(['status' => true, 'message' => 'Deliveryman removed.']);