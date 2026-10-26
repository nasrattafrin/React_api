<?php
// dashboard/stats.php
include __DIR__ . '/../connection.php';
include __DIR__ . '/../auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed.']);
    exit;
}
$isMerchant = isset($access_user_role) && (int)$access_user_role === 2;
$merchantId = (int) $access_user_id;
$parcelCondition = "p.deleted_at IS NULL";
if ($isMerchant) {
    $parcelCondition .= " AND p.sender_id = $merchantId";
}
$res = $cc->common_query("SELECT COUNT(*) as total FROM parcels p WHERE $parcelCondition");
$totalParcels = $res['status'] ? (int)$res['data'][0]->total : 0;
$res = $cc->common_query("SELECT COUNT(*) as total FROM parcels p WHERE p.status IN (0,1,2) AND $parcelCondition");
$pendingParcels = $res['status'] ? (int)$res['data'][0]->total : 0;
if ($isMerchant) {
    $walletRes = $cc->common_query("SELECT balance FROM wallets w WHERE w.merchant_id = $merchantId AND w.deleted_at IS NULL LIMIT 1");
    $totalRevenue = $walletRes['status'] && !empty($walletRes['data']) ? (float)$walletRes['data'][0]->balance : 0;
} else {
    $revParcel = $cc->common_query("SELECT SUM(delivery_charge) as total FROM parcels p WHERE p.deleted_at IS NULL");
    $revCOD = $cc->common_query("SELECT SUM(service_charge) as total FROM cod_settlements c WHERE c.deleted_at IS NULL");
    $totalRevenue = 0;
    if ($revParcel['status'] && !empty($revParcel['data'])) $totalRevenue += (float)$revParcel['data'][0]->total;
    if ($revCOD['status'] && !empty($revCOD['data'])) $totalRevenue += (float)$revCOD['data'][0]->total;
}
$totalMerchants = 0;
if (!$isMerchant) {
    $res = $cc->common_query("SELECT COUNT(*) as total FROM users u WHERE u.role_id = 2 AND u.deleted_at IS NULL");
    $totalMerchants = $res['status'] ? (int)$res['data'][0]->total : 0;
}
$statusData = [
    ['name' => 'Pending', 'value' => 0, 'color' => '#ffc107'],
    ['name' => 'Picked', 'value' => 0, 'color' => '#17a2b8'],
    ['name' => 'In Transit', 'value' => 0, 'color' => '#007bff'],
    ['name' => 'Delivered', 'value' => 0, 'color' => '#28a745'],
    ['name' => 'Cancelled', 'value' => 0, 'color' => '#dc3545'],
];
$res = $cc->common_query("SELECT p.status, COUNT(*) as count FROM parcels p WHERE $parcelCondition GROUP BY p.status");
if ($res['status']) {
    foreach ($res['data'] as $row) {
        $st = (int)$row->status;
        if (isset($statusData[$st])) {
            $statusData[$st]['value'] = (int)$row->count;
        }
    }
}
$revenueTrend = [];
if (!$isMerchant) {
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime($date));
        
        $q1 = $cc->common_query("SELECT SUM(delivery_charge) as total FROM parcels p WHERE DATE(p.created_at) = '$date' AND p.deleted_at IS NULL");
        $deliveryCharge = ($q1['status'] && !empty($q1['data'][0]->total)) ? (float)$q1['data'][0]->total : 0;
        
        $q2 = $cc->common_query("SELECT SUM(service_charge) as total FROM cod_settlements c WHERE DATE(c.settled_at) = '$date' AND c.status = 2 AND c.deleted_at IS NULL");
        $serviceCharge = ($q2['status'] && !empty($q2['data'][0]->total)) ? (float)$q2['data'][0]->total : 0;
        
        $revenueTrend[] = [
            'day' => $dayName,
            'date' => $date,
            'amount' => $deliveryCharge + $serviceCharge
        ];
    }
}


$topDeliverymen = [];
if (!$isMerchant) {
    $res = $cc->common_query("
        SELECT u.name, COUNT(p.id) as total_delivered
        FROM parcels p
        JOIN users u ON u.id = p.assigned_to
        WHERE p.status = 3 AND p.deleted_at IS NULL
        GROUP BY p.assigned_to
        ORDER BY total_delivered DESC
        LIMIT 5
    ");
    $topDeliverymen = $res['status'] ? $res['data'] : [];
}


$res = $cc->common_query("
    SELECT p.id, p.tracking_id, p.receiver_name, p.status, p.created_at, z.zone_name
    FROM parcels p
    JOIN zones z ON z.id = p.zone_id
    WHERE $parcelCondition
    ORDER BY p.id DESC LIMIT 5
");
$recentParcels = $res['status'] ? $res['data'] : [];


echo json_encode([
    'status' => true,
    'data' => [
        'kpi' => [
            'total_parcels' => $totalParcels,
            'pending_parcels' => $pendingParcels,
            'total_revenue' => $totalRevenue,
            'total_merchants' => $totalMerchants
        ],
        'chart_data' => $statusData,
        'revenue_trend' => $revenueTrend,
        'top_deliverymen' => $topDeliverymen,
        'recent_parcels' => $recentParcels
    ]
]);