<?php
// parcel/zones.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/parcel_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    parcel_json(405, ['status' => false, 'message' => 'Method not allowed.']);
}

$res = $cc->common_query("SELECT id, zone_name, description FROM zones WHERE deleted_at IS NULL ORDER BY id ASC");
$zones = $res['status'] ? $res['data'] : [];

parcel_json(200, ['status' => true, 'zones' => $zones]);