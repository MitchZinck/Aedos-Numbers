<?php

require __DIR__ . '/twilio/Twilio/autoload.php';
use Twilio\Rest\Client;

class Datahandler {
	private $db;

	public function init() {
		include "config.php";
		try {
		    $this->db = new PDO('mysql:host=' . $config['mysql-host'] . ';dbname=' . $config['mysql-db'], $config['mysql-user'], $config['mysql-pass']);
		    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		} catch(PDOException $e) {
		    $error = 'ERROR: ' . $e->getMessage();
			die($error);
		}
	}
	
	public function addId($repId, $phoneNumber) {
		$query = $this->db->prepare('SELECT * FROM `repids` WHERE `repid`=?');
		$query->bindParam(1, $repId, PDO::PARAM_INT);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);

		if(!$row) { //if number doesn't exist
			$query = $this->db->prepare("INSERT INTO `repids` (repid, number) VALUES (:repid, :number)");
			$query->bindParam(':repid', $repId);
			$query->bindParam(':number', $phoneNumber);
			$query->execute();
		} else { //update number
			$id = $row['id'];
			$query = $this->db->prepare("UPDATE `repids` SET `repid` = ?, `number` = ? WHERE `id`");
			$query->execute(array($repId, $phoneNumber));
		}
	}

	public function removeId($repId) {
		$query = $this->db->prepare('DELETE FROM `repids` WHERE `repid` = :repid');
		$query->bindParam(':repid', $repId, PDO::PARAM_INT);
		$query->execute();
	}

	public function getId($phoneNumber) {
		$query = $this->db->prepare('SELECT * FROM `repids` WHERE `number`=?');
		$query->bindParam(1, $phoneNumber, PDO::PARAM_INT);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row['repid'];
	}

	public function broadCast($message) {
		$query = $this->db->prepare('SELECT * FROM `repids`');
		$query->bindParam(1, $repId, PDO::PARAM_INT);
		$query->execute();
		$row = $query->fetchALl();
		foreach($row as $result) {
			self::sendSMS($message, $result['number']);
		}
	}

	function sendSMS($message, $number) {
	    $AccountSid = "AC5dbf0f4ca4e1b30d38aca2c5c72afbaf";
	    $AuthToken = "19fb425f15c3af605ef93a15fd10937c";
        $client = new Client($AccountSid, $AuthToken);

        $sms = $client->account->messages->create(

            // the number we are sending to - Any phone number
            $number,

            array(
                'from' => "+14318004760 ", 
                'body' => $message
            )
        );
    }

}

?>