console.log('in workspace.js');

const oWorkspaceReq = new XMLHttpRequest();
oWorkspaceReq.onload = function() {
	const workerToken = JSON.parse(this.responseText).token;

	const workspace = new Twilio.TaskRouter.Workspace(workerToken);


	workspace.on("ready", function(oWorkspace) {
		console.log('workspace ready');
		console.log(oWorkspace.sid); // 'WSxxx'
		console.log(oWorkspace.friendlyName); // 'Workspace 1'
		console.log(oWorkspace.prioritizeQueueOrder); // 'FIFO'
		console.log(oWorkspace.defaultActivityName); // 'Offline'


		/**
		 * Workspace is ready, lets make a new task
		 */
		const createTask = function() {
			const attributes = "{\"Ticket\":\"Gold\"}";

			const params = {"WorkflowSid":`${oWorkspace.sid}`, "Attributes":attributes};

			// "TaskChannel":"voice",

			const newWorkspace = new Twilio.TaskRouter.Workspace(workerToken);



			newWorkspace.tasks.create(params,
				function(error, task) {
					if(error) {
						console.log(error.code);
						console.log(error.message);
						return;
					}
					console.log("TaskSid: "+task.sid);

					// @TODO show on UI with accept button
				}
			);
		};

		//@todo make a listener to create a new task from the frontend for easier testing
		document.getElementById('newTaskButton').addEventListener('click', createTask);


	});



};
oWorkspaceReq.open("get", "worker-token.php", true);
oWorkspaceReq.send();
