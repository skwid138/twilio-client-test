<?php
include('./vendor/autoload.php');
include('./config.php');

use Twilio\Jwt\ClientToken;

// This would be unique to the agent
$identity = 'CIG_Test';

$capability = new ClientToken($TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN);
$capability->allowClientOutgoing($TWILIO_TWIML_APP_SID);
$capability->allowClientIncoming($identity);

//default token is good for an hour, this makes it good for 8
$token = $capability->generateToken();
//$token = $capability->generateToken(28800);

// return serialized token and the user's randomly generated ID
header('Content-Type: application/json');
echo json_encode(array(
    'identity' => $identity,
    'token' => $token,
));
