<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$qry = "SELECT * " .
				"FROM ols_technologyversion " .
				"WHERE technologyid = $id " .
				"ORDER BY name";
				
	} else if (isset($_POST['name'])) {
		$name = $_POST['name'];
		$qry = "SELECT * " .
				"FROM ols_technologyversion " .
				"WHERE name = '$name' " .
				"ORDER BY name";
		
	}
	
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