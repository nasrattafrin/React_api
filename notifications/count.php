<?php

include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$count = 0;
if (isset($access_user_role) && (int)$access_user_role === 2) {
    // Merchant er unread notification count
    $res = $cc->common_query("SELECT COUNT(*) as total FROM notifications WHERE user_id = $access_user_id AND is_read = 0 AND deleted_at IS NULL");
    if ($res['status'] && !empty($res['data'])) {
        $count = (int)$res['data'][0]->total;
    }
}

echo json_encode(['status' => true, 'count' => $count]);