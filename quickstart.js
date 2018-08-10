const isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

$(function () {
	var speakerDevices = document.getElementById('speaker-devices');
	var ringtoneDevices = document.getElementById('ringtone-devices');
	var outputVolumeBar = document.getElementById('output-volume');
	var inputVolumeBar = document.getElementById('input-volume');
	var volumeIndicators = document.getElementById('volume-indicators');

	log('Requesting Capability Token...');

	$.getJSON('/token.php')
		.done(function (data) {

			//console.log('token data ->', data);

			log('Got a token.');
			console.log('Token: ' + data.token);

			/**
			 * Setup Twilio.Device
			 *
			 * The docs recommend binding this to a click event instead as browsers are making this more difficult
			 */
			const setupObject = {
				debug: true,
				closeProtection: true, //dialog will appear if the user is on a call and tries to leave the page
				warnings: true, //console.logs warnings (default is true)
			};
			Twilio.Device.setup(data.token, setupObject);

			// The docs look like the new way is to Twilio.Device.on('ready', function(device) {})
			Twilio.Device.ready(function (device) {
				log('Twilio.Device Ready!');
				document.getElementById('call-controls').style.display = 'block';
			});

			Twilio.Device.error(function (error) {
				log('Twilio.Device Error: ' + error.message);
			});


			/**
			 * Create the Call
 			 */
			// The docs look like the new way is to Twilio.Device.on('connect', function(connection) {})
			Twilio.Device.connect(function (conn) {
				log('Successfully established call!');
				document.getElementById('button-call').style.display = 'none';
				document.getElementById('button-hangup').style.display = 'inline';
				volumeIndicators.style.display = 'block';
				bindVolumeIndicators(conn);
			});

			Twilio.Device.disconnect(function (conn) {
				log('Call ended.');
				document.getElementById('button-call').style.display = 'inline';
				document.getElementById('button-hangup').style.display = 'none';
				volumeIndicators.style.display = 'none';
			});

			Twilio.Device.incoming(function (conn) {
				log('Incoming connection from ' + conn.parameters.From);
				var archEnemyPhoneNumber = '+12099517118';

				if (conn.parameters.From === archEnemyPhoneNumber) {
					conn.reject();
					log('It\'s your nemesis. Rejected call.');
				} else {
					// accept the incoming connection and start two-way audio
					conn.accept();
				}
			});

			setClientNameUI(data.identity);

			Twilio.Device.audio.on('deviceChange', updateAllDevices);

			// Show audio selection UI if it is supported by the browser.
			if (Twilio.Device.audio.isSelectionSupported || isChrome) { // chrome supports, but this wasn't truthy
				document.getElementById('output-selection').style.display = 'block';
			}
		})
		.fail(function () {
			log('Could not get a token from server!');
		});

	// Bind button to make call
	document.getElementById('button-call').onclick = function () {
		// get the phone number to connect the call to
		var params = {
			To: document.getElementById('phone-number').value
		};

		console.log('Calling ' + params.To + '...');
		Twilio.Device.connect(params);
	};

	// Bind button to hangup call
	document.getElementById('button-hangup').onclick = function () {
		log('Hanging up...');
		Twilio.Device.disconnectAll();
	};

	// Browser will ask to access microphone
	document.getElementById('get-devices').onclick = function () {
		navigator.mediaDevices.getUserMedia({audio: true})
			.then(updateAllDevices);
	};

// Ths speaker device is for all sound except an incoming call ring
	// change audio output device
	speakerDevices.addEventListener('change', function () {
		var selectedDevices = [].slice.call(speakerDevices.children)
			.filter(function (node) {
				return node.selected;
			})
			.map(function (node) {
				return node.getAttribute('data-id');
			});

		Twilio.Device.audio.speakerDevices.set(selectedDevices);
	});

// The ringtone device for the incoming call ring only
	ringtoneDevices.addEventListener('change', function () {
		var selectedDevices = [].slice.call(ringtoneDevices.children)
			.filter(function (node) {
				return node.selected;
			})
			.map(function (node) {
				return node.getAttribute('data-id');
			});

		Twilio.Device.audio.ringtoneDevices.set(selectedDevices);
	});

	function bindVolumeIndicators(connection) {
		connection.volume(function (inputVolume, outputVolume) {
			var inputColor = 'red';
			if (inputVolume < .50) {
				inputColor = 'green';
			} else if (inputVolume < .75) {
				inputColor = 'yellow';
			}

			inputVolumeBar.style.width = Math.floor(inputVolume * 300) + 'px';
			inputVolumeBar.style.background = inputColor;

			var outputColor = 'red';
			if (outputVolume < .50) {
				outputColor = 'green';
			} else if (outputVolume < .75) {
				outputColor = 'yellow';
			}

			outputVolumeBar.style.width = Math.floor(outputVolume * 300) + 'px';
			outputVolumeBar.style.background = outputColor;
		});
	}

	function updateAllDevices() {
		updateDevices(speakerDevices, Twilio.Device.audio.speakerDevices.get());
		updateDevices(ringtoneDevices, Twilio.Device.audio.ringtoneDevices.get());
	}
}); //end line one function


/**
 * The below Methods are for the UI only
 */

// Update the available ringtone and speaker devices
function updateDevices(selectEl, selectedDevices) {
	selectEl.innerHTML = '';
	Twilio.Device.audio.availableOutputDevices.forEach(function (device, id) {
		var isActive = (selectedDevices.size === 0 && id === 'default');
		selectedDevices.forEach(function (device) {
			if (device.deviceId === id) {
				isActive = true;
			}
		});

		var option = document.createElement('option');
		option.label = device.label;
		option.setAttribute('data-id', id);
		if (isActive) {
			option.setAttribute('selected', 'selected');
		}
		selectEl.appendChild(option);
	});
}

// Activity log
function log(message) {
	var logDiv = document.getElementById('log');
	logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
	logDiv.scrollTop = logDiv.scrollHeight;
}

// Set the client name in the UI
function setClientNameUI(clientName) {
	var div = document.getElementById('client-name');
	div.innerHTML = 'Your client name: <strong>' + clientName +
		'</strong>';
}
