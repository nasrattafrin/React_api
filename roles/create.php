<?php 
include '../connection.php';
include '../auth_check.php';
$data = [
    'role_name'  =>trim($_POST['role_name'] ?? ''),
    'created_at' =>date('Y-m-d H:i:s'),
    'created_by' =>$access_user_id ?? 1,
];
$res=['status'=> false,'message' =>'','data'=>[]];
if (empty($data['role_name'])) {
    $res['message'] = 'Role name required';
    echo json_encode($res);
    exit;
}
try {
    $res = $cc->common_insert('roles', $data);
} catch (Exception $e) {
    $res = ['status' => false, 'message' => $e->getMessage(), 'data' => []];
}
echo json_encode($res);
exit;
?>