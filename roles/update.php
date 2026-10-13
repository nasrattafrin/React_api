<?php
include '../connection.php';
include '../auth_check.php';
$id = $_GET['id'] ?? 0;
$data = [
    'role_name'  => trim($_POST['role_name'] ?? ''),
    'updated_at' => date('Y-m-d H:i:s'),
    'updated_by' => $access_user_id ?? 1,
];
$res = ['status' => false, 'message' => '', 'data' => []];
if (!$id) {
    $res['message'] = 'ID required';
    echo json_encode($res);
    exit;
}
if (empty($data['role_name'])) {
    $res['message'] = 'Role name required';
    echo json_encode($res);
    exit;
}
try {
    $res = $cc->common_update('roles', $data, ['id' => $id]);
} catch (Exception $e) {
    $res = ['status' => false, 'message' => $e->getMessage(), 'data' => []];
}
echo json_encode($res);
exit;
?>