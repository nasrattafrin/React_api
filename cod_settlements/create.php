<?php
// cod_settlements/create.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$parcelId       = (int)   ($input['parcel_id']      ?? 0);
$deliverymanId  = (int)   ($input['deliveryman_id'] ?? 0);
$merchantId     = (int)   ($input['merchant_id']    ?? 0);
$codAmount      = (float) ($input['cod_amount']     ?? -1);
$serviceCharge  = (float) ($input['service_charge'] ?? 0);

if ($parcelId <= 0)      { http_response_code(422); echo json_encode(['status'=>false,'message'=>'parcel_id is required.']); exit; }
if ($deliverymanId <= 0) { http_response_code(422); echo json_encode(['status'=>false,'message'=>'deliveryman_id is required.']); exit; }
if ($merchantId <= 0)    { http_response_code(422); echo json_encode(['status'=>false,'message'=>'merchant_id is required.']); exit; }
if ($codAmount <= 0)     { http_response_code(422); echo json_encode(['status'=>false,'message'=>'COD amount must be greater than 0.']); exit; }
if ($serviceCharge < 0)  { http_response_code(422); echo json_encode(['status'=>false,'message'=>'Service charge cannot be negative.']); exit; }

// Duplicate check (same parcel already settled?)
$dup = $cc->common_query("SELECT id FROM cod_settlements WHERE parcel_id = $parcelId AND deleted_at IS NULL LIMIT 1");
if ($dup['status'] && !empty($dup['data'])) {
    http_response_code(409);
    echo json_encode(['status' => false, 'message' => 'This parcel is already in COD settlement.']);
    exit;
}

$netAmount = $codAmount - $serviceCharge;

$insert = $cc->common_insert('cod_settlements', [
    'parcel_id'      => $parcelId,
    'deliveryman_id' => $deliverymanId,
    'merchant_id'    => $merchantId,
    'cod_amount'     => $codAmount,
    'service_charge' => $serviceCharge,
    'net_amount'     => $netAmount,
    'status'         => 0,
    'created_by'     => (int) $access_user_id,
]);

if (!$insert['status']) {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Could not create settlement.']);
    exit;
}

echo json_encode([
    'status'  => true,
    'message' => 'COD settlement created.',
    'id'      => (int) $insert['data'],
]);