<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$chatsessionid = $_POST['chatsessionid'];
	$message = mysql_escape_string($_POST['message']);
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	$qry = "INSERT INTO ols_chatmessages " .
			"(chatsessionid, memberid,  message, createddate) " .
			"VALUES " .
			"($chatsessionid, $memberid, '$message', NOW())";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	
	if (isset($_POST['command']) && $_POST['command'] == "END") {
		$qry = "UPDATE ols_chatsession " .
				"SET lastaccessdate = NOW(), " .
				"status = 'C' " .
				"WHERE id = $chatsessionid";
				
	} else {
		$qry = "UPDATE ols_chatsession " .
				"SET lastaccessdate = NOW() " .
				"WHERE id = $chatsessionid";
	}
	
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	require_once("requestexpertrefresh.php");
?>
