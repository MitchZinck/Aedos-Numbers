<?php
header('Access-Control-Allow-Origin: *');
if(empty($_REQUEST['Body'])) {
	die();
}

include "numbers.php";
include "datahandler.php";
require_once '/twilio/Twilio/autoload.php'; 
use Twilio\Twiml;

$response = new Twiml;
$body = strtolower($_REQUEST['Body']);
$message;

if(strpos($body, '!help') !== false) {
	$message = "Each app needs a space between itself and the value. An example text which includes watch + surf, watch + surf, and watch + surf + talk + home security is:\n\ndk 50 dm 30 p 15 c 5 wsi 2 wstihs 1\n\nMake sure you include an \"i\" for every app otherwise it won't count install!";
} elseif(strpos($body, '!signup') !== false) {
	$body = explode(" ", $body);
	$repId = $body[1];
	$phoneNumber = $_REQUEST['From'];
	$datahandler = new Datahandler();
	$datahandler->init();
	$datahandler->addId($repId, $phoneNumber);
	$message = "You have been signed up. A text will be sent to you at 9:30pm every night to put in your numbers. Have them in by midnight.\nRepid: " . $repId . "\nPhone number: " . $phoneNumber;
} elseif(strpos($body, '!remove') !== false) {
	$body = explode(" ", $body);
	$repId = $body[1];
	$datahandler = new Datahandler();
	$datahandler->init();
	$datahandler->removeId($repId);
	$message = $repId . " removed successfully!";
} elseif(strpos($body, '!unlink') !== false) {
	unlink('aedos_today.json');
	$message = "File unlinked successfully.";
} elseif(strpos($body, '!broadcast') !== false) {
	$body = explode(" ", $body);
	if(isset($body[1])) {
		$message = str_replace('!broadcast ', '', $_REQUEST['Body']);
	} else {
		$message = "Please send in your numbers as soon as possible - Aedos";
	}
	$datahandler = new Datahandler();
	$datahandler->init();
	$datahandler->broadCast($message);
	$message = "Your message " . $message . " was sent successfully!";
} elseif (strpos($body, 'dk') !== false) {
	$numbers = new Numbers($body, $_REQUEST['From']);
	$message = $numbers->doNumbers();
}

sendResponse($response, $message);

function sendResponse($response, $message) {
	$response->message($message);
	print $response;
}

?>