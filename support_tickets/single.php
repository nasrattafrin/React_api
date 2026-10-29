<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
if (isset($access_user_role) && (int)$access_user_role !== 1) {
    http_response_code(403);
    echo json_encode(['status' => false, 'message' => 'Only admins can reply to tickets.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$id = (int) ($input['id'] ?? 0);
$status = (int) ($input['status'] ?? -1);
$adminReply = trim($input['admin_reply'] ?? '');
if ($id <= 0 || !in_array($status, [0, 1, 2])) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Invalid data.']);
    exit;
}
$updateData = [
    'status'     => $status,
    'updated_by' => (int) $access_user_id
];
if ($adminReply !== '') {
    $updateData['admin_reply'] = $adminReply;
}
$cc->common_update('support_tickets', $updateData, ['id' => $id]);
echo json_encode(['status' => true, 'message' => 'Ticket updated successfully.']);