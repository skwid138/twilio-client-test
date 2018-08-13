<?php
include('./vendor/autoload.php');
include('./functions.php');

// https://www.twilio.com/docs/taskrouter/api/reservations#reservations-list-resource

// https://www.twilio.com/docs/taskrouter/api/worker-reservations

//@TODO this has not been tested

/**
 *
 * @return
 */
function accept_reservation() {
	global $current_worker, $workspace;

	$reservation_sid = $_POST['reservationsid'];

	$agent_phone_number = JSON_Decode($current_worker->attributes)->phone_number;

	//@TODO A valid TwiML URI that is executed on the answering Worker's leg.
	$call_url = 'https://3d34a968.ngrok.io/call.php';

	return $workspace->workers($current_worker->sid)->reservations($reservation_sid)->update(
		[
			'reservationStatus' => 'accepted',
			'instruction' => 'call',
			'callFrom' => '+12402215042', // what appears on the caller ID
			'callTo' => $agent_phone_number, // this is optional
			'callUrl' => $call_url,
			//'callStatusCallbackUrl' => 'http://example.com/agent_answer_status_callback',
			'callAccept' => 'true'
		]
	);
}

if(isset($_POST['submit'])) {
	accept_reservation();
}