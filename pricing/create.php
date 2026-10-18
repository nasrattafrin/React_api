<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
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
    WHERE zone_id = $zoneId AND deleted_at IS NULL
      AND ($minW < max_weight AND $maxW > min_weight)
    LIMIT 1
");
if ($overlap['status'] && !empty($overlap['data'])) {
    http_response_code(409);
    echo json_encode(['status' => false, 'message' => 'This weight range overlaps with an existing rule.']);
    exit;
}
$insert = $cc->common_insert('pricing', [
    'zone_id'    => $zoneId,
    'min_weight' => $minW,
    'max_weight' => $maxW,
    'charge'     => $charge,
    'created_by' => (int) $access_user_id,
]);
if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create pricing rule.']);
    exit;
}
echo json_encode([
    'status'  => true,
    'message' => 'Pricing rule created.',
    'id'      => (int) $insert['data'],
]);