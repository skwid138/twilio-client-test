<?php
include('./vendor/autoload.php');
include('./config.php'); // environment specific variables
include('./helpers.php'); // not twilio specific functions
include('./twilio-wrappers.php'); // twilio specific functions

use Twilio\Rest\Client;

$twilio = new Client($TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN);

$workspace = $twilio->taskrouter->v1->workspaces($WORKSPACE_SID);
$task_queues = $workspace->taskQueues->read();

$all_tasks = $workspace->tasks->read();

/**
 * STATUS
 * @TODO - Show current agent status
 * @TODO - Ability to change and update current agent status
 *
 * TASKS and Calls
 * @TODO - Ability to accept and assign tasks
 * @TODO - Ability to pick a call from the queue and connect the caller with the agent
 */


// Get the current user "worker" - This will make this dynamic
$current_worker = $workspace->workers($HUNTER_SID)->fetch();

// Get reservations or tasks automatically assigned to the worker from taskqueue target
$current_worker_reservations = $workspace->workers($current_worker->sid)->reservations->read();

$current_worker_attributes = JSON_Decode($current_worker->attributes); // object


// perhaps we use priorities to best determine which task instead of the age
// if orders are more important than general etc.


// @TODO Accept the reservation and connect the customer to the agent



/**
 * https://www.twilio.com/docs/taskrouter/js-sdk/taskqueue
 */

if(false) {

	foreach($task_queues as $queue) {
		print($queue->friendlyName . '<br/>');
		//print($queue->targetWorkers . '<br/>'); // example:   queues HAS "ID_General"

		// All workers in a specified queue
		$all_workers = $workspace->workers->read([
			'TaskQueueName' => $queue->friendlyName
		]);

		// All Available workers in a specified queue
		$available_workers = $workspace->workers->read([
			'TaskQueueName' => $queue->friendlyName,
			'Available' => true
		]);

		foreach($all_workers as $worker) {
			print($worker->friendlyName . '<br/>'); // name
			print($worker->activityName . '<br/>'); // status
			print($worker->available . '<br/>'); // boolean
		}
	}

	foreach ($tasks as $task) {
		//print($task_sid->sid);
		print($task->reason . '<br/>'); // example: Call Wrapping up
		print($task->assignmentStatus . '<br/>'); // example: wrapping
		//print($task_sid->dateCreated); // date format
		print($task->priority . '<br/>'); // int (defaults to 0)
		print(convert_seconds_to_hours($task->age) . '<br/>'); // seconds since task was created
		print($task->taskChannelUniqueName . '<br/>'); // How the task was created? - calls = voice

		// same properties as above, but returns a single task
		$task_get = $workspace->tasks($task->sid)->fetch();
		//print($task_get->reason);

		// same properties as getting all the queues
		$task_queue = $workspace->taskQueues($task->taskQueueSid)->fetch();
		print($task_queue->friendlyName . '<br/>');
	}
}