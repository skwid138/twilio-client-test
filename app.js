console.log('in app.js');

/**
 * Automatically add the script tag to our index.php
 *
 * @param url string Path to the script
 * @param callback
 */
function loadScript(url, callback) {
	// Adding the script tag to the head as suggested before
	const head = document.getElementsByTagName('head')[0];
	const script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url;

	// Then bind the event to the callback function.
	// There are several events for cross browser compatibility.
	script.onreadystatechange = callback;
	script.onload = callback;

	// Fire the loading
	head.appendChild(script);
}

function scriptLoaded(script) {
	console.log(script, ' script successfully loaded');
}



/* Add this line for each new script */
loadScript('worker.js', scriptLoaded('worker.js'));
//loadScript('workspace.js', scriptLoaded('workspace.js'));