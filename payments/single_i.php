<?php
include '../connection.php';
$id=$_GET['id'];

$result=$cc->common_select('payments','*',['parcel_id'=>$id]);

echo json_encode($result);