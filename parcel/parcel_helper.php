<?php
const PARCEL_MAX_WEIGHT = 15.0;  

const PARCEL_STATUS_LABELS = [
    0 => 'Pending', 1 => 'Picked', 2 => 'In Transit', 3 => 'Delivered', 4 => 'Cancelled',
];
function parcel_generate_tracking_id(): string
{
    return 'TRK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
}
function parcel_calculate_charge($cc, int $zoneId, float $weight): float
{
    $sql = "SELECT charge FROM pricing
            WHERE zone_id = " . (int)$zoneId . "
              AND " . (float)$weight . " BETWEEN min_weight AND max_weight
              AND deleted_at IS NULL
            LIMIT 1";
    $res = $cc->common_query($sql);
    if ($res['status'] && !empty($res['data'])) {
        return (float) $res['data'][0]->charge;
    }
    return 0.0;
}
function parcel_add_log($cc, int $parcelId, int $status, ?string $note, int $userId): void
{
    $cc->common_insert('parcel_logs', [
        'parcel_id'  => $parcelId,
        'status'     => $status,
        'note'       => $note,
        'created_by' => $userId,
    ]);
}
function parcel_status_label(int $s): string
{
    return PARCEL_STATUS_LABELS[$s] ?? 'Unknown';
}
function parcel_validate_weight(float $weight): ?string
{
    if ($weight <= 0) {
        return 'Weight must be greater than 0.';
    }
    if ($weight > PARCEL_MAX_WEIGHT) {
        return 'Weight cannot exceed ' . PARCEL_MAX_WEIGHT . ' kg.';
    }
    return null;
}
function parcel_json($status, $payload)
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}
const PAYMENT_METHOD_LABELS = [
    0 => 'bKash',
    1 => 'Nagad',
    2 => 'Card',
    3 => 'COD',
];
const PAYMENT_STATUS_LABELS = [
    0 => 'Pending',
    1 => 'Success',
    2 => 'Failed',
];
function payment_method_label(int $m): string
{
    return PAYMENT_METHOD_LABELS[$m] ?? 'Unknown';
}
function payment_status_label(int $s): string
{
    return PAYMENT_STATUS_LABELS[$s] ?? 'Unknown';
}