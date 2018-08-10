<?php
include('./vendor/autoload.php');
include('./config.php');

use Twilio\Rest\Client;

$twilio = new Client($TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN);

$workspace = $twilio->taskrouter->v1->workspaces($WORKSPACE_SID);
$task_queues = $workspace->taskQueues->read();

//$reservations = $workspace->tasks($taskSid)->reservations->read();



$tasks = $workspace->tasks->read();



function convert_seconds_to_hours($seconds) {
	$t = round($seconds);
	return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}



//print('<div style="color: white;">');

/**
 * https://www.twilio.com/docs/taskrouter/js-sdk/taskqueue
 */

if(false) {
	foreach($task_queues as $queue) {
		print($queue->friendlyName . '<br/>');
		//print($queue->targetWorkers . '<br/>'); // example:   queues HAS "ID_General"

		$all_workers = $workspace->workers->read([
			'TaskQueueName' => $queue->friendlyName
		]);

		$available_workers = $workspace->workers->read([
			'TaskQueueName' => $queue->friendlyName,
			'Available' => true
		]);

		foreach($all_workers as $worker) {
			print($worker->friendlyName . '<br/>');
			print($worker->activityName . '<br/>');
			print($worker->available . '<br/>');
		}
	}
}



if(false) {
	foreach ($tasks as $task) {
		//print($task_sid->sid);
		print($task->reason . '<br/>'); //Call Wrapping up
		print($task->assignmentStatus . '<br/>'); //wrapping
		//print($task_sid->dateCreated);
		print($task->priority . '<br/>'); // 0
		print(convert_seconds_to_hours($task->age) . '<br/>'); // seconds since task was created
		print($task->taskChannelUniqueName . '<br/>'); // voice


		//$task_get = $workspace->tasks($task->sid)->fetch();
		//print($task_get->reason);

		$task_queue = $workspace->taskQueues($task->taskQueueSid)->fetch();
		print($task_queue->friendlyName . '<br/>');
	}
}

//print('</div>');