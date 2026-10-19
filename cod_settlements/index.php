<?php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
include __DIR__ . '/../parcel/parcel_helper.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$where = ["cs.deleted_at IS NULL"];
if (isset($_GET['deliveryman_id']) && $_GET['deliveryman_id'] !== '') {
    $where[] = "cs.deliveryman_id = " . (int) $_GET['deliveryman_id'];
}
if (isset($_GET['merchant_id']) && $_GET['merchant_id'] !== '') {
    $where[] = "cs.merchant_id = " . (int) $_GET['merchant_id'];
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where[] = "cs.status = " . (int) $_GET['status'];
}
if (!empty($_GET['search'])) {
    $s = $cc->conn->real_escape_string(trim($_GET['search']));
    $where[] = "(p.tracking_id LIKE '%$s%' OR d.name LIKE '%$s%' OR m.name LIKE '%$s%')";
}

$res = $cc->common_query("
    SELECT cs.id, cs.parcel_id, cs.deliveryman_id, cs.merchant_id,
           cs.cod_amount, cs.service_charge, cs.net_amount,
           cs.status, cs.settled_at, cs.created_at,
           p.tracking_id,
           d.name AS deliveryman_name,
           m.name AS merchant_name
    FROM cod_settlements cs
    JOIN parcels p  ON p.id = cs.parcel_id
    JOIN users d    ON d.id = cs.deliveryman_id
    JOIN users m    ON m.id = cs.merchant_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY cs.created_at DESC
");
$rows=$res['status']?$res['data']:[];
$labels = [
    0 => 'Collected',
    1 => 'Received',
    2 => 'Settled',
];
foreach ($rows as &$r) {
    $r->status_label = $labels[(int) $r->status]??'Unknown';
}

/* total percel count of cod amount */
$cod_total = $cc->common_query("
    SELECT sum(cod_amount) AS total_cod_amount,
           sum(delivery_charge) AS total_delivery_charge
    FROM parcels
");
$cod_status_total = $cc->common_query("
    SELECT count(*) as total_cod_status_count, status
    FROM parcels where status in (0, 3) group by status
");

$totals=$cod_total['status']?$cod_total['data'][0]:['total_cod_amount'=>0,'total_delivery_charge'=>0];
if($cod_status_total['status'] && !empty($cod_status_total['data'])){
    foreach($cod_status_total['data'] as $status_row){
        $totals->{'status_'.$status_row->status} = $status_row->total_cod_status_count;
    }
}
echo json_encode([
    'status'         => true,
    'data'           => $rows,
    'settlements'    => $rows,
    'totals'         => $totals,
]);