<?php
include '../connection.php';
//include '../auth_check.php';
$result=$cc->common_select('roles');
echo json_encode($result);
?>