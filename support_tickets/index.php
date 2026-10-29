<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$where = ["st.deleted_at IS NULL"];
if (isset($access_user_role) && (int)$access_user_role === 2) {
    $where[] = "st.user_id = " . (int)$access_user_id;
}
$res = $cc->common_query("
    SELECT st.id, st.subject, st.message, st.admin_reply, st.status, st.created_at,
           u.name AS merchant_name, p.tracking_id
    FROM support_tickets st
    JOIN users u ON u.id = st.user_id
    LEFT JOIN parcels p ON p.id = st.parcel_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY st.id DESC
");
$rows = $res['status'] ? $res['data'] : [];
echo json_encode([
    'status'   => true,
    'data'     => $rows,
    'tickets'  => $rows
]);