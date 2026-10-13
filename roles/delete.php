<?php
include '../connection.php';
include '../auth_check.php';
$id = $_GET['id'] ?? 0;
$res = ['status'=>false,'message' =>'','data' => []];
if (!$id) {
    $res['message']='ID required';
    echo json_encode($res);
    exit;
}
try {
    $data = ['deleted_at' => date('Y-m-d H:i:s'),];
    $res = $cc->common_update('roles', $data, ['id' => $id]);
} catch (Exception $e) {
    $res = ['status' => false, 'message' => $e->getMessage(), 'data' => []];
}
echo json_encode($res);
exit;
?>