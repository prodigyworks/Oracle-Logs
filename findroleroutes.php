<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$routeid = $_GET['routeid'];
	
	$qry = "SELECT A.roleid " .
			"FROM jomon_roleroutes A " .
			"INNER JOIN jomon_roles B " .
			"ON B.roleid = A.roleid " .
			"WHERE A.routeid = '$routeid' " .
			"ORDER BY A.roleid";
	$result = mysql_query($qry);
	$first = true;
	
	//Check whether the query was successful or not
	echo "[\n";
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if (! $first) {
				echo ",\n";
				
			} else {
				$first = false;
			}
			
			echo "{\"id\": \"" . $member['roleid'] . "\", \"name\": \"" . $member['roleid'] . "\"}";
		}
	}
	
	echo "\n]\n";
?>