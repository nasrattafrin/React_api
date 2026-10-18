<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$where = ["p.deleted_at IS NULL"];
if (isset($_GET['zone_id']) && $_GET['zone_id'] !== '') {
    $where[] = "p.zone_id = " . (int) $_GET['zone_id'];
}
if (!empty($_GET['search'])) {
    $s = $cc->conn->real_escape_string(trim($_GET['search']));
    $where[] = "(p.charge LIKE '%$s%' OR p.min_weight LIKE '%$s%' OR p.max_weight LIKE '%$s%')";
}
$res = $cc->common_query("
    SELECT p.id, p.zone_id, z.zone_name,
           p.min_weight, p.max_weight, p.charge,
           p.created_at
    FROM pricing p
    JOIN zones z ON z.id = p.zone_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY p.zone_id ASC, p.min_weight ASC
");
$rows = $res['status'] ? $res['data'] : [];
echo json_encode([
    'status'  => true,
    'data'    => $rows,
    'pricing' => $rows,
]);