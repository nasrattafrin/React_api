<?php 
include '../connection.php';
$data=json_decode(file_get_contents('php://input'),true);
$res=[];
if($data['name']){
    $res=$cc->common_insert('addresses',$data);
}

echo json_decode($res);







?>