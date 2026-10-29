<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$subject  = trim($input['subject'] ?? '');
$message  = trim($input['message'] ?? '');
$parcelId = !empty($input['parcel_id']) ? (int)$input['parcel_id'] : null;
$userId   = (int)$access_user_id; // Token theke auto ashe
if ($subject === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Subject and message are required.']);
    exit;
}
$insert = $cc->common_insert('support_tickets', [
    'user_id'    => $userId,
    'parcel_id'  => $parcelId,
    'subject'    => $subject,
    'message'    => $message,
    'status'     => 0, // 0 
    'created_by' => $userId
]);
if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create ticket.']);
    exit;
}
echo json_encode(['status' => true, 'message' => 'Ticket created successfully.']);