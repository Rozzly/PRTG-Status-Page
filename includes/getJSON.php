<?php

	// Hide warnings just in case return values are not numeric
	error_reporting(E_ERROR | E_PARSE);

	// Initial Variables
	$prtg_object = $_POST["id"];
	$prtg_address = 'FQDN';
	$prtg_username = 'USERNAME';
	$prtg_password = 'PASSWORD';

    // Building string for PRTG calls
	$url = "https://".$prtg_address."/api/table.json?content=sensors&output=json&columns=device,status,sensor,lastvalue&id=".$prtg_object."&username=".$prtg_username."&password=".$prtg_password;	
	
	// Parse the return JSON into an array
	$result = file_get_contents($url);
	$json = json_decode($result);
	$sensors = $json->{'sensors'};	
	
	// Initialize the array variables
	$parent = array();
	$child = array();

	// Loop through each sensor
	foreach($sensors as $index=>$object)
    {
		// Set variables for parsing
		$name = strtoupper($object->device);
		$name = str_replace(' ', '', $name);
		$status = $object->status;
		$sensor = $object->sensor;
		$value = $object->lastvalue;
		
		// Name Cleanup
		if (strpos($name, '(') !== false) {
			$name = substr($name, 0, strpos($name, "("));
		}
		if ($sensor == "Memory Free") {$value = (100-$value); $sensor = "Memory Usage" ;};
		if ($sensor == "Disk Free") {$value = (100-$value); $sensor = "Disk Usage" ;};
		if ($sensor == "CPU Load") {$value = str_replace(' %', '', $value);};	
		if ($sensor == "vmxnet3 Ethernet Adapter") {$value = str_replace(' kbit/s', '', $value); $sensor = "Network";};
		if ($sensor == "Intel[R] 82574L Gigabit Network Connection") {$value = str_replace(' kbit/s', '', $value); $sensor = "Network";};
		if ($sensor == "RDP Sessions") {$value = str_replace(' #', '', $value);};
		if ($sensor == "Application Server") {$value = str_replace(' MByte', '', $value);};	
		
		// Build Item array
		$child['Status']=$status;
		$child['Value']=$value;
		
		// Check if the returned array is for VPNS > Reformat if so.
		if ($name == "FIREWALLAPPLIANCE") {
			if (strpos($sensor, 'VPN Tunnel: ') !== false) {
				$sensor = strtoupper(str_replace('VPN Tunnel: ', '', $sensor));
				$parent[$sensor]=$child;
			}
		} else {
			$parent[$name][$sensor]=$child;
		}
    }   

	// Return the API JSON output
	echo json_encode($parent);
?>