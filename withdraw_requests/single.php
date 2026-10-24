<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($input['id'] ?? 0);
    $newStatus = (int) ($input['status'] ?? -1); // 1=Approved, 2=Paid, 3=Rejected

    if ($id <= 0 || !in_array($newStatus, [1, 2, 3])) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Invalid data.']);
        exit;
    }

    $reqRes = $cc->common_query("SELECT * FROM withdraw_requests WHERE id = $id AND deleted_at IS NULL LIMIT 1");
    if (!$reqRes['status'] || empty($reqRes['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Request not found.']);
        exit;
    }
    $req = $reqRes['data'][0];

    if ($req->status != 0) {
        http_response_code(409);
        echo json_encode(['status' => false, 'message' => 'This request is already processed.']);
        exit;
    }

    if ($newStatus === 1) {
        // APPROVED: Check Wallet Balance & Debit
        $walletRes = $cc->common_query("SELECT id, balance FROM wallets WHERE merchant_id = {$req->merchant_id} AND deleted_at IS NULL LIMIT 1");
        if (!$walletRes['status'] || empty($walletRes['data'])) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Wallet not found for this merchant.']);
            exit;
        }
        $wallet = $walletRes['data'][0];

        if ($wallet->balance < $req->amount) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Insufficient wallet balance.']);
            exit;
        }

        // Debit Wallet
        $newBalance = $wallet->balance - $req->amount;
        $cc->common_update('wallets', [
            'balance' => $newBalance,
            'updated_by' => (int) $access_user_id
        ], ['id' => $wallet->id]);

        // Log Transaction
        $cc->common_insert('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 1, // DEBIT
            'amount' => $req->amount,
            'reference' => 'WITHDRAW-' . $id,
            'description' => 'Withdraw approved (Pending Payment)',
            'created_by' => (int) $access_user_id
        ]);
    }

    // Update Request Status
    $updateData = [
        'status' => $newStatus, 
        'processed_at' => date('Y-m-d H:i:s'),
        'updated_by' => (int) $access_user_id
    ];
    $cc->common_update('withdraw_requests', $updateData, ['id' => $id]);

    echo json_encode(['status' => true, 'message' => 'Withdraw request updated successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => false, 'message' => 'Method not allowed.']);