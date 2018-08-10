<!DOCTYPE html>
<html>
<head>
    <title>Twilio Client Quickstart</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
<div id="controls">
    <div id="info">
        <p class="instructions">Twilio Client</p>
        <div id="client-name"></div>
        <div id="output-selection">
            <label>Ringtone Devices</label>
            <select id="ringtone-devices" multiple></select>
            <label>Speaker Devices</label>
            <select id="speaker-devices" multiple></select><br/>
            <a id="get-devices">Seeing unknown devices?</a>
        </div>
    </div>
    <div id="call-controls">
        <p class="instructions">Make a Call:</p>
        <input id="phone-number" type="text" placeholder="Enter a phone # or client name"/>
        <button id="button-call">Call</button>
        <button id="button-hangup">Hangup</button>
        <div id="volume-indicators">
            <label>Mic Volume</label>
            <div id="input-volume"></div>
            <br/><br/>
            <label>Speaker Volume</label>
            <div id="output-volume"></div>
        </div>
    </div>
    <div id="log"></div>
</div>


<div style="margin-top: 18rem; margin-left: 7rem; color: white;">
	<?php include('./functions.php'); ?>

    <h1>Queues</h1>

    <div class="table-container">
        <h3>Totals</h3>
        <table>
            <thead>
                <tr>
                    <th>Queue</th>
                    <th>All Calls</th>
                    <th>Calls Waiting</th>
                </tr>
            </thead>
            <tbody>
			<?php foreach ($task_queues as $queue): ?>

				<?php
                $all_tasks = get_all_tasks_from_queue($queue->friendlyName);
                $unanswered_tasks = get_all_unaccepted_tasks_from_queue($queue->friendlyName);
				?>

                <tr>
                    <td><?= $queue->friendlyName ?></td>
                    <td><?= count($all_tasks) ?></td>
                    <td><?= count($unanswered_tasks) ?></td>
                </tr>
			<?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div>
        <h3>Current User: <?= $current_worker->friendlyName ?></h3>

        <ul>
            <?php foreach($current_worker_reservations as $reservation): ?>
            <li>
                Status: <?= $reservation->reservationStatus ?>

            <?php if($reservation->reservationStatus === 'pending') :?>
                <form method="post" action="reservation-form-submit.php">
                    <input name="reservationsid" value="<?= $reservation->sid ?>" hidden />

                    <div>
                        <label for="reservation-accept">Accept?</label>
                        <input name="accept" type="checkbox" value="1" id="reservation-accept" />
                    </div>

                    <button type="submit">Submit</button>
                </form>
            <?php endif; ?>
            </li>

            <?php endforeach; ?>
        </ul>
    </div>

<?php foreach ($task_queues as $queue): ?>
    <div class="queue-tables-container">
        <h3 style="margin-bottom: 0;"><?= $queue->friendlyName ?></h3>
        <div class="table-container">
            <h5>Service Agents</h5>
            <table cellspacing="1" cellpadding="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Activity</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                <?php $all_workers = get_all_workers_from_queue($queue->friendlyName); ?>

                <?php foreach($all_workers as $worker): ?>
                    <tr>
                        <td><?= $worker->friendlyName ?></td>
                        <td><?= $worker->activityName ?></td>
                        <td><?= ((bool)$worker->available ? 'Available' : 'Busy') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
        <div class="table-container">
            <h5>Calls</h5>
            <table cellspacing="1" cellpadding="1">
                <thead>
                <tr>
                    <th>Time In Queue</th>
                    <th>Priority</th>
                    <th>Assigned</th>
                    <th>Reason</th>
                </tr>
                </thead>
                <tbody>

				<?php $all_tasks = get_all_tasks_from_queue($queue->friendlyName); ?>

                <?php if (!empty($all_tasks)) : ?>

                    <?php foreach($all_tasks as $task): ?>
                        <tr>
                            <td><?= convert_seconds_to_hours($task->age) ?></td>
                            <td><?= $task->priority ?></td>
                            <td><?= $task->assignmentStatus ?></td>
                            <td><?= $task->reason ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">NO CALLS</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
<?php endforeach ?>




    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.4/twilio.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="quickstart.js"></script>
</body>
</html>