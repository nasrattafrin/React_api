<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$where = ["pay.deleted_at IS NULL"];
if (isset($_GET['parcel_id']) && $_GET['parcel_id'] !== '') {
    $where[] = "pay.parcel_id = " . (int) $_GET['parcel_id'];
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where[] = "pay.status = " . (int) $_GET['status'];
}
if (isset($_GET['method']) && $_GET['method'] !== '') {
    $where[] = "pay.method = " . (int) $_GET['method'];
}
if (!empty($_GET['search'])) {
    $s = $cc->conn->real_escape_string(trim($_GET['search']));
    $where[] = "(p.tracking_id LIKE '%$s%' OR pay.transaction_id LIKE '%$s%')";
}
$res = $cc->common_query("
    SELECT pay.id, pay.parcel_id, pay.amount, pay.method,
           pay.transaction_id, pay.status, pay.created_at,
           p.tracking_id, p.receiver_name,
           s.name AS sender_name,
           u.name AS created_by_name
    FROM payments pay
    JOIN parcels p      ON p.id = pay.parcel_id
    JOIN users s        ON s.id = p.sender_id
    LEFT JOIN users u   ON u.id = pay.created_by
    WHERE " . implode(' AND ', $where) . "
    ORDER BY pay.created_at DESC
");
$rows = $res['status'] ? $res['data'] : [];
foreach ($rows as &$r) {
    $r->method_label = payment_method_label((int) $r->method);
    $r->status_label = payment_status_label((int) $r->status);
}
echo json_encode([
    'status'   => true,
    'data'     => $rows,
    'payments' => $rows,
]);