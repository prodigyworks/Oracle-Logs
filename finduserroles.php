<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$roleid = $_GET['roleid'];
	
	$qry = "SELECT A.memberid, B.login " .
			"FROM ols_userroles A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.roleid = '$roleid' " .
			"ORDER BY B.login";
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
			
			echo "{\"id\": " . $member['memberid'] . ", \"name\": \"" . $member['login'] . "\"}";
		}
	}
	
	echo "\n]\n";
?>