<?php
include('./vendor/autoload.php');
include('./config.php');

use Twilio\Twiml;

$response = new Twiml;

$response->dial('+12183412264');

header('Content-Type: text/xml');
echo $response;