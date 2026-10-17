<?php
// deliverymen/single.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

// ================= GET — single deliveryman =================
if ($method === 'GET') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }

    $res = $cc->common_query("
        SELECT u.id, u.name, u.contact_no, u.email,
               u.branch_id, u.address, u.status, u.created_at,
               b.branch_name
        FROM users u
        LEFT JOIN branches b ON b.id = u.branch_id
        WHERE u.id = $id AND u.role_id = 3 AND u.deleted_at IS NULL
        LIMIT 1
    ");

    if (!$res['status'] || empty($res['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Deliveryman not found.']);
        exit;
    }

    echo json_encode([
        'status'      => true,
        'data'        => $res['data'][0],
        'deliveryman' => $res['data'][0],
    ]);
    exit;
}

// ================= PUT — update =================
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($input['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }

    // Ensure this user is a deliveryman
    $check = $cc->common_query("SELECT id FROM users WHERE id = $id AND role_id = 3 AND deleted_at IS NULL LIMIT 1");
    if (!$check['status'] || empty($check['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Deliveryman not found.']);
        exit;
    }

    $updateData = [];
    if (isset($input['name']))       $updateData['name']       = trim($input['name']);
    if (isset($input['contact_no'])) $updateData['contact_no'] = trim($input['contact_no']);
    if (isset($input['branch_id']))  $updateData['branch_id']  = (int) $input['branch_id'];
    if (isset($input['address']))    $updateData['address']    = trim($input['address']);
    if (isset($input['status']))     $updateData['status']     = (int) $input['status'];

    if (!empty($input['password'])) {
        if (strlen($input['password']) < 6) {
            http_response_code(422);
            echo json_encode(['status' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }
        $updateData['password'] = sha1($input['password']);
    }

    if (empty($updateData)) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Nothing to update.']);
        exit;
    }

    $updateData['updated_at'] = date('Y-m-d H:i:s');
    $cc->common_update('users', $updateData, ['id' => $id]);

    echo json_encode(['status' => true, 'message' => 'Deliveryman updated.']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => false, 'message' => 'Method not allowed.']);