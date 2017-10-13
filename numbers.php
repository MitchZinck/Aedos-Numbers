<?php

class Numbers {

	public $body;
	public $phoneNumber;
	public $repId;

    public function __construct($body, $phoneNumber) {
      $this->body = $body;
      $this->phoneNumber = $phoneNumber;
      $datahandler = new Datahandler();
      $datahandler->init();
      $this->repId = $datahandler->getId($phoneNumber);
    }

	public function doNumbers() {
		$matchedArray;
		preg_match_all('/ ([^\\s]+)\\s([^\\s]+) /x', $this->body, $matchedArray); //formatting
		$recievedArray = array_combine($matchedArray[1], $matchedArray[2]); //combine key and value into recievedArray

		/*
		 * Loop through key and value pairs.
		 * Calculate into rgus, cable, nets, phones, security and installs
		 */
		$i;
		$cable = 0;
		$net = 0;
		$hs = 0;
		$phone = 0;
		$install = 0;
		$apps = 0;
		foreach($recievedArray as $key => $value) {
			if(empty($recievedArray[$key])) {
				continue;
			}
			$app = false;
			$multiplier = $recievedArray[$key];

			if(strpos($key, 'w') !== false) {
				$cable += $multiplier;
				$app = true;
			}
			if(strpos($key, 'hs') !== false) { 
				$hs += $multiplier;
				if(substr_count($key, "s") > 1) {
					$net += $multiplier;
				}
				$app = true;
			}elseif(strpos($key, 's') !== false) { 
				$net += $multiplier;
				$app = true;
			}
		    if(strpos($key, 't') !== false) {
		    	$phone += $multiplier;
		    	$app = true;
			}
			if(strpos($key, 'i') !== false) {
		    	$install += $multiplier;
		    	$app = true;
			}

			if($app === true) {
				$apps += $multiplier;
			}
		}

		$recievedArray['cable'] = $cable;
		$recievedArray['hs'] = $hs;
		$recievedArray['net'] = $net;
		$recievedArray['phone'] = $phone;
		$recievedArray['apps'] = $apps;
		$recievedArray['install'] = $install;
		$recievedArray['rgu'] = $recievedArray['phone'] + $recievedArray['net'] + $recievedArray['cable'] + $recievedArray['hs'];

		$encodedArray = ["repid" => $this->repId, "numbers" => self::encodeArray($recievedArray)];
		self::writeJson($encodedArray);

		$message = "Numbers recieved.\n" . $recievedArray['phone'] . " Talks\n" . $recievedArray['net'] . " Nets\n" . $recievedArray['cable'] . " Cables\n" . $recievedArray['hs'] . " Securities\n" . $recievedArray['apps'] . " Apps\n" . $recievedArray['install'] . " Installs\n" .  $recievedArray['rgu'] . " Rgus\n" . "If any of that is incorrect check your text for errors. Text !help for directions.";	//json_encode($recievedArray)
		return $message;
	}

	public function writeJson($encodedArray) {
		$previous;
		if(file_exists('aedos_today.json')) {
			$previous = file_get_contents('aedos_today.json');
			$previous = rtrim($previous,"]") . ", ";
		} else {
			$previous = "[";
		}
		$toWrite = $previous . json_encode($encodedArray) . "]";
		$fp = fopen('aedos_today.json', 'w');
		fwrite($fp, $toWrite);
		fclose($fp);
	}

	public function encodeArray($arr) {
		$newArray = [$arr['cable'], $arr['dk'], 0, $arr['net'], $arr['phone'], $arr['p'], $arr['c'], $arr['rgu'], $arr['dm'], $arr['apps'], 0, 0, $arr['hs'], 0, $arr['install'], 0];
		return $newArray;
	}

}

?>