<?php
if (!isset($cc)) {
    include __DIR__ . '/connection.php';
}
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {

    $accessToken = $matches[1];
    $authresult = $cc->common_select('users', '*', ['remember_token' => $accessToken]);

    if (!empty($authresult['data']) && count($authresult['data']) > 0) {
        $access_user_id = $authresult['data'][0]->id;
        $access_user_role = $authresult['data'][0]->role_id; 
    } else {
        echo json_encode([
            "message" => "Token is not available in database 2.",
            "status" => false
        ]);
        exit;
    }
} else {
    echo json_encode([
        "message" => "Token is not available in database 1.",
        "status" => false
    ]);
    exit;
}
?>