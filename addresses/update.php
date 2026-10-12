<?php
include '../connection.php';
$id=$_GET['id'];

$res=$cc->common_update('addresses',$_POST,['id'=>$id]);

echo json_encode($res);
