<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$name = $_POST['name'];
	$qry = "SELECT * " .
			"FROM ols_operatingsystem " .
			"WHERE name = '$name' " .
			"ORDER BY name";
	$result = mysql_query($qry);
	
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