<?php
include('./vendor/autoload.php');
include('./config.php');
include('./functions.php');

use Twilio\Jwt\TaskRouter\WorkerCapability;

$capability = new WorkerCapability(
	$TWILIO_ACCOUNT_SID,
	$TWILIO_AUTH_TOKEN,
	$WORKSPACE_SID,
	$current_worker->sid);

$capability->allowFetchSubresources();
$capability->allowActivityUpdates(); // allows users to update there status
$capability->allowReservationUpdates(); // allows users to update resevations(tasks)

// default token good for an hour, this one is now good for 8 hours
$token = $capability->generateToken(28800);

// return serialized token and the user's randomly generated ID
header('Content-Type: application/json');
echo json_encode([
	'token' => $token,
]);


