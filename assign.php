<?php
include('./vendor/autoload.php');
include('./config.php');

// Agent Number From the defined worked attributes
//$number = json_decode($_POST->form['WorkerAttributes'])['phone_number'];

//$instruction = new StdClass();
//$instruction->instruction = 'dequeue';
//$instruction->to = '+12183412264';
//$instruction->to = $number;
//$instruction->from = $ID_GENERAL_NUM;


header('Content-Type: application/json');
echo json_encode([
	'instruction' => 'dequeue',
	'to' => '+12183412264',
	'from' => $ID_GENERAL_NUM
]);
