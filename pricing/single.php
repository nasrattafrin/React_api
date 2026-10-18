<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }
    $res = $cc->common_query("
        SELECT p.id,p.zone_id,z.zone_name,p.min_weight,p.max_weight,p.charge,p.created_at
        FROM pricing p
        JOIN zones z ON z.id = p.zone_id
        WHERE p.id = $id AND p.deleted_at IS NULL
        LIMIT 1
    ");
    if (!$res['status'] || empty($res['data'])) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Pricing rule not found.']);
        exit;
    }

    echo json_encode([
        'status'  => true,
        'data'    => $res['data'][0],
        'pricing' => $res['data'][0],
    ]);
    exit;
}
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($input['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'id is required.']);
        exit;
    }
    $zoneId = (int)   ($input['zone_id']    ?? 0);
    $minW   = (float) ($input['min_weight'] ?? -1);
    $maxW   = (float) ($input['max_weight'] ?? -1);
    $charge = (float) ($input['charge']     ?? -1);
    if ($zoneId <= 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Zone is required.']);
        exit;
    }
    if ($minW < 0 || $maxW <= $minW) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Max weight must be greater than min weight.']);
        exit;
    }
    if ($charge < 0) {
        http_response_code(422);
        echo json_encode(['status' => false, 'message' => 'Charge cannot be negative.']);
        exit;
    }
    $overlap = $cc->common_query("
        SELECT id FROM pricing
        WHERE zone_id = $zoneId AND id != $id AND deleted_at IS NULL
          AND ($minW < max_weight AND $maxW > min_weight)
        LIMIT 1
    ");
    if ($overlap['status'] && !empty($overlap['data'])) {
        http_response_code(409);
        echo json_encode(['status' => false, 'message' => 'This weight range overlaps with an existing rule.']);
        exit;
    }
    $cc->common_update('pricing', [
        'zone_id'    => $zoneId,
        'min_weight' => $minW,
        'max_weight' => $maxW,
        'charge'     => $charge,
        'updated_by' => (int) $access_user_id,
    ], ['id' => $id]);
    echo json_encode(['status' => true, 'message' => 'Pricing rule updated.']);
    exit;
}
http_response_code(405);
echo json_encode(['status' => false, 'message' => 'Method not allowed.']);