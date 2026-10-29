<?php
include __DIR__ . '/../connection.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$res = $cc->common_query("SELECT COUNT(*) as total FROM support_tickets WHERE status = 0 AND deleted_at IS NULL");
$count = 0;
if ($res['status'] && !empty($res['data'])) {
    $count = (int)$res['data'][0]->total;
}
echo json_encode(['status' => true, 'count' => $count]);