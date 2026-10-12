<?php
include 'connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$response = [
    'status' => false,
    'message' => '',
    'data' => null,
    'jwt' => null
];
if (empty($data['email']) || empty($data['password'])) {
    $response['message'] = 'Email and password required';
    echo json_encode($response);
    exit;
}
$email = strtolower(trim($data['email']));

$inputPasswordHash = sha1(trim($data['password']));

$result = $cc->common_select("users", '*', ['email' => $email]);

if ($result['status'] !== true || count($result['data']) === 0) {
    $response['message'] = 'Invalid email or password';
    echo json_encode($response);
    exit;
}

$user = $result['data'][0];
if ($user->password !== $inputPasswordHash) {
    $response['message'] = 'Invalid email or password';
    echo json_encode($response);
    exit;
}

$remember_token = rand(1111, 9999) . time() . rand(111111, 999999);

$cc->common_update(
    "users",
    ['remember_token' => $remember_token],
    ['id' => $user->id]
);
unset($user->password);

$response['status'] = true;
$response['message'] = 'Successful login.';
$response['jwt'] = $remember_token;
$response['data'] = $user;

echo json_encode($response);
exit;
?>