<?php
include('./vendor/autoload.php');

/**
 * converts seconds to hh:mm:ss
 * @param $seconds int
 * @return string (hh:mm:ss)
 */
function convert_seconds_to_hours($seconds) {
	$t = round($seconds);
	return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}