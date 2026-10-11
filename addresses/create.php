<?php 
include '../connection.php';
include '../auth_check.php';
$data = [
    'user_id'    => $access_user_id ?? 1,   
    'type'       => $_POST['type'] ?? 0,
    'name'       => trim($_POST['name'] ?? ''),
    'phone'      => trim($_POST['phone'] ?? ''),
    'address'    => trim($_POST['address'] ?? ''),
    'created_at' => date('Y-m-d H:i:s'),
];
$uploadDir = '../';
if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $name = rand() . '_' . basename($_FILES['image']['name']);
    $targetPath = 'uploads/' . $name;
    if (!is_dir($uploadDir . 'uploads/')) {
        mkdir($uploadDir . 'uploads/', 0755, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $targetPath)) {
        $data['image'] = $targetPath;
    }
}

$res = ['status' => false, 'message' => '', 'data' => []];
if (empty($data['name'])) {
    $res['message'] = 'Name required';
    echo json_encode($res);
    exit;
}
if (empty($data['phone'])) {
    $res['message'] = 'Phone required';
    echo json_encode($res);
    exit;
}
if (empty($data['address'])) {
    $res['message'] = 'Address required';
    echo json_encode($res);
    exit;
}
try {
    $res = $cc->common_insert('addresses', $data);
} catch (Exception $e) {
    $res = ['status' => false, 'message' => $e->getMessage(), 'data' => []];
}
echo json_encode($res);
exit;
?>