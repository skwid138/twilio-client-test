<?php
include('./vendor/autoload.php');
include('./config.php');
include('./functions.php');

use Twilio\Jwt\TaskRouter\WorkspaceCapability;

$capability = new WorkspaceCapability(
	$TWILIO_ACCOUNT_SID,
	$TWILIO_AUTH_TOKEN,
	$WORKSPACE_SID);

$capability->allowFetchSubresources();
$capability->allowUpdatesSubresources();
$capability->allowDeleteSubresources();
$worker_token = $capability->generateToken(28800);

// return serialized token and the user's randomly generated ID
header('Content-Type: application/json');
echo json_encode([
	'token' => $worker_token,
]);