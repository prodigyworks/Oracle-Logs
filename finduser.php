<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$login = $_POST['login'];
	$qry = "SELECT * " .
			"FROM ols_members " .
			"WHERE login = '$login'";
	$result = mysql_query($qry);
	
	//Check whether the query was successful or not
	echo "[\n";
	echo "{\"id\": \"0\", \"name\": \"\"}";
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo ",\n";
			echo "{\"id\": " . $member['member_id'] . ", \"name\": \"" . $member['firstname'] . "\"}";
		}
	}
	
	echo "\n]\n";
?>