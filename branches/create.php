<?php 
include '../connection.php';
include '../auth_check.php';
$data = [
    'branch_name' => trim($_POST['branch_name'] ?? ''),
    'address'     => trim($_POST['address'] ?? ''),
    'phone'       => trim($_POST['phone'] ?? ''),
    'created_at'  => date('Y-m-d H:i:s'),
    'created_by'  => $access_user_id ?? 1,
];
$res = ['status'=>false,'message' => '','data' =>[]];
// Validation
if (empty($data['branch_name'])) {
    $res['message'] ='Branch name required';
    echo json_encode($res);
    exit;
}
if (empty($data['phone'])) {
    $res['message']='Phone required';
    echo json_encode($res);
    exit;
}
try {
    $res = $cc->common_insert('branches', $data);
}catch (Exception $e) {
    $res = ['status'=>false,'message'=>$e->getMessage(),'data' => []];
}
echo json_encode($res);
exit;
?>