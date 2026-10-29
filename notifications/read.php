<?php
// notifications/read.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}


if (isset($access_user_id)) {
    $cc->common_query("UPDATE notifications SET is_read = 1 WHERE user_id = $access_user_id AND is_read = 0");
}

echo json_encode(['status' => true, 'message' => 'Notifications marked as read.']);