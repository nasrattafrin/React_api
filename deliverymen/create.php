<?php
// deliverymen/create.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$name      = trim($input['name'] ?? '');
$contactNo = trim($input['contact_no'] ?? '');
$email     = strtolower(trim($input['email'] ?? ''));
$password  = $input['password'] ?? '';
$branchId  = (int) ($input['branch_id'] ?? 0);
$address   = trim($input['address'] ?? '');

// Validation
if ($name === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Name is required.']);
    exit;
}
if ($contactNo === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Contact number is required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Valid email is required.']);
    exit;
}
if (strlen($password) < 6) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}
if ($branchId <= 0) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Branch is required.']);
    exit;
}

// Duplicate email check
$check = $cc->common_select('users', 'id', ['email' => $email]);
if ($check['status'] && count($check['data']) > 0) {
    http_response_code(409);
    echo json_encode(['status' => false, 'message' => 'An account with this email already exists.']);
    exit;
}

// Insert user with role_id = 3 (Deliveryman)
$insert = $cc->common_insert('users', [
    'name'       => $name,
    'contact_no' => $contactNo,
    'email'      => $email,
    'password'   => sha1($password),   // tomar login.php SHA1 use kore
    'role_id'    => 3,
    'branch_id'  => $branchId,
    'address'    => $address,
    'status'     => 1,
    'created_at' => date('Y-m-d H:i:s'),
]);

if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create deliveryman.']);
    exit;
}

echo json_encode([
    'status'  => true,
    'message' => 'Deliveryman created successfully.',
    'id'      => (int) $insert['data'],
]);