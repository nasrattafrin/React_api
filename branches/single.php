<?php
include '../connection.php';
$id = $_GET['id'] ?? 0;
if (!$id) {
    echo json_encode([
        'status' => false,
        'message' => 'ID required',
        'data' => []
    ]);
    exit;
}
$result = $cc->common_select('branches', '*', ['id' => $id]);
echo json_encode($result);
?>