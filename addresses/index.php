<?php
include '../connection.php';
$result=$cc->common_select('addresses');
echo json_encode($result);