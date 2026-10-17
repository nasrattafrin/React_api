<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/parcel_helper.php';
$input  = json_decode(file_get_contents('php://input'), true) ?: [];
$zoneId = (int) ($input['zone_id'] ?? 0);
$weight = (float) ($input['weight'] ?? 0);
if ($zoneId <= 0 || $weight <= 0) {
    parcel_json(422, ['status' => false, 'message' => 'zone_id and weight are required.']);
}
$charge = parcel_calculate_charge($cc, $zoneId, $weight);
parcel_json(200, [
    'status'    => true,
    'zone_id'   => $zoneId,
    'weight'    => $weight,
    'charge'    => $charge,
    'available' => $charge > 0,
]);