<?php
include('./vendor/autoload.php');
//include('./config.php');
//include('./helpers.php');
//include('./functions.php');


/*********************************************
 * Queue wrappers
 *********************************************/

/**
 * Get all the tasks that are not currently being worked on, they may be pending
 * or reserved waiting for a worker to accept them
 *
 * @param $queue_friendly_name string Example: ID General
 * @return array of task objects
 */
function get_all_unaccepted_tasks_from_queue($queue_friendly_name) {
	global $workspace;
	return $workspace->tasks->read([
		'TaskQueueName' => $queue_friendly_name,
		'AssignmentStatus' => ['pending', 'reserved'],
	]);
}

/**
 * Get all tasks from a specified queue
 *
 * @param $queue_friendly_name string Example: ID Orders
 * @return \Twilio\Rest\Taskrouter\V1\Workspace\TaskInstance[]
 */
function get_all_tasks_from_queue($queue_friendly_name) {
	global $workspace;
	return $workspace->tasks->read([
		'TaskQueueName' => $queue_friendly_name
	]);
}

/**
 * Get all workers from a specified queue
 *
 * @param $queue_friendly_name string Example: ID Orders
 * @return array of worker objects
 */
function get_all_workers_from_queue($queue_friendly_name) {
	global $workspace;
	return $workspace->workers->read([
		'TaskQueueName' => $queue_friendly_name
	]);
}