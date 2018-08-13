console.log('in worker.js');

const oReq = new XMLHttpRequest();
oReq.onload = function() {
	const WORKER_TOKEN = JSON.parse(this.responseText).token;
	//console.log(WORKER_TOKEN);

	// pass backend created token to front end
	const worker = new Twilio.TaskRouter.Worker(WORKER_TOKEN);

	// Worker Activity Dropdown
	const activitySelectElement = document.getElementById('agent-status');

	/**
	 * Retrieves the list of Activities configured in your TaskRouter's Workspace.
	 */
	worker.activities.fetch(function(error, activityList) {
		console.log('activity fetch');

		if(error) {
			console.log(error.code);
			console.log(error.message);
			return;
		}

		const allActivities = activityList.data;

		//@TODO this seems unecesarry, but making another instance of worker inside the fetch callback
		// allowed me to access it's properties
		const newWorker = new Twilio.TaskRouter.Worker(WORKER_TOKEN);
		newWorker.on('ready', function(oNewWorker) {

			allActivities.forEach(function(activity) {
				let option = document.createElement('option');
				option.text = activity.friendlyName;
				option.setAttribute('data-activity-sid', activity.sid);
				//option.id = `${activity.friendlyName}`;

				if(oNewWorker.activitySid === activity.sid) {
					option.selected = true;
					//document.getElementById(`${activity.friendlyName}`).selected = true;
				}

				activitySelectElement.add(option);
			});
		});
	}); // end fetch activities

	/**
	 * Get pending reservations
	 */
	worker.fetchReservations(function(error, oReservation) {
		console.log('in reservation fetch');
		if(error) {
			console.log(error.code);
			console.log(error.message);
			return;
		}

		reservationList = '';

		oReservation.data.forEach(function(res) {
			console.log(res);

			//@TODO display these?

			reservationList += document.getElementById('reservation-list').innerHTML = ``;

		});
	}, {"ReservationStatus":"pending"});


// @TODO maybe the best way to do this is to make one worker and when that's ready do everything else inside it?
	//@todo if/when a second worker is needed make it inside of the first?
	worker.on('ready', function(oWorker) {
		console.log('worker ready!');
		//console.log(worker.sid);             // 'WKxxx...'
		//console.log(worker.friendlyName);    // 'hrancourt'
		//console.log(worker.activityName);    // 'Reserved'
		//console.log(worker.available);       // false
		//console.log(worker.activitySid); // 'WAxxx...'

		document.getElementById('current-activity').innerHTML = oWorker.activityName;
		document.getElementById('current-activity').setAttribute('current-activity-sid', oWorker.activitySid);

		// setTimeout(() => {
		// 	document.getElementById(oWorker.activitySid).selected = true;
		// }, 3000);
	});

	worker.on("reservation.created", function(oReservation) {
		console.log(oReservation.task.attributes);      // {foo: 'bar', baz: 'bang' }
		console.log(oReservation.task.priority);        // 1
		console.log(oReservation.task.age);             // 300
		console.log(oReservation.task.sid);             // WTxxx
		console.log(oReservation.sid);                  // WRxxx

		//@TODO apply listener to document.getElementByID('newReservationAcceptButton')

		const callReservation = function(reservation) {
			console.log(reservation);

			reservation.call(
				"+12402215042", // callFrom
				"http://twimlbin.com/451369ae", // callUrl
				null, // callStatus
				null, // callAccept
				null, // callRecord
				'+12183412264', // callTo
				function(error, reservation) { // resultCallback
					if(error) {
						console.log(error.code);
						console.log(error.message);
						return;
					}
					reservation.accept();
				}
			);
		};
		document.getElementById('newReservationAcceptButton').addEventListener('click', callReservation(oReservation));


	});

	worker.on('activity.update', function(oWorker) {
		console.log('activity updated to ', oWorker.activityName);
		//console.log(worker.sid);             // 'WKxxx'
		//console.log(worker.friendlyName);    // 'Worker 1'
		//console.log(worker.activityName);    // 'Reserved'
		//console.log(worker.available);       // false

		document.getElementById('current-activity').innerHTML = oWorker.activityName;
		document.getElementById('current-activity').setAttribute('current-activity-sid', oWorker.activitySid);
	});


	/**
	 * Update Agent Activity/Status
	 */
	const updateActivity = function(e) {
		e.preventDefault();

		// get the activity SID from the data attribute
		const selectedActivity = activitySelectElement.item(activitySelectElement.selectedIndex);
		const activitySid = selectedActivity.getAttribute('data-activity-sid');

		worker.update('ActivitySid', activitySid, function(error, worker) {
			if(error) {
				console.log(error.code);
				console.log(error.message);
			} else {
				//console.log(worker.activityName);
			}
		});
	};


	// add listener to activity update button
	document.getElementById('update-activity-button').addEventListener('click', updateActivity);
};
oReq.open("get", "worker-token.php", true);
oReq.send();

