<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$qry = "SELECT * " .
			"FROM jomon_technicianrates " .
			"ORDER BY name";
	$result = mysql_query($qry);
	
	if (! $result) {
		die("Error:" . mysql_error());
	}
	
	//Check whether the query was successful or not
	echo "[\n";
	echo "{\"id\": \"0\", \"name\": \"\"}";
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo ",\n";
			echo "{\"id\": " . $member['id'] . ", \"name\": \"" . $member['name'] . "\"}";
		}
	}
	
	echo "\n]\n";
?>