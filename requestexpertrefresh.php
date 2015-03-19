<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$chatsessionid = $_POST['chatsessionid'];
	$qry = "SELECT A.status, A.lastaccessdate, B.id, B.message, C.login, C.imageid, B.createddate " .
			"FROM ols_chatsession A " .
			"INNER JOIN ols_chatmessages B " .
			"ON B.chatsessionid = A.id " .
			"INNER JOIN ols_members C " .
			"ON C.member_id = B.memberid " .
			"WHERE A.id = $chatsessionid " .
			"ORDER BY B.id";

	$result = mysql_query($qry);
	$first = true;
	$closed = false;
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	echo "[\n";
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if (! $first) {
				echo ",\n";
			}
			
			if ($member['status'] == "C") {
				$closed = true;
			}
			
			$first = false;
			
			echo "{\"status\": \"" . $member['status'] . "\", \"datestamp\": \"" . $member['createddate'] . "\", \"imageid\": \"" . $member['imageid'] . "\", \"id\": \"" . $member['login'] . "\", \"name\": " . json_encode($member['message']) . "}";
		}
	}
	
	echo "\n]\n";
?>


	
