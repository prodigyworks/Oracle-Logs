<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$body = mysql_escape_string($_POST['answerbody']);
	$questionid = $_POST['questionid'];
	$published = "N";
	
	if (isUserInRole("CONSULTANT")) {
		$published = "Y";
	}
	
	$qry = "INSERT INTO ols_questionanswers " .
			"(questionid, memberid, createddate, body, published) " .
			"VALUES " .
			"($questionid, $memberid, NOW(), '$body', '$published')";
			
	$result = mysql_query($qry);
	$answerid = mysql_insert_id();
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	
	$qry = "SELECT id FROM ols_documents " .
		   "WHERE sessionid = '" . session_id() . "' " .
		   "ORDER BY id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$qry = "INSERT INTO ols_questionanswerdocuments " .
				"(answerid, documentid, createddate) " .
				"VALUES " .
				"($answerid, " . $member['id'] . ", NOW())";
				
		$itemresult = mysql_query($qry);
		
		if (! $itemresult) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	$qry = "UPDATE ols_documents " .
		   "SET sessionid = NULL " .
		   "WHERE sessionid = '" . session_id() . "'";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	if (! isUserInRole("ADMIN")) {
		$qry = "SELECT title FROM ols_question " .
			   "WHERE id = $questionid";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		while (($member = mysql_fetch_assoc($result))) {
			sendRoleMessage("ADMIN", "Verification request", "<p>Verification required for answer regarding question " . $member['title'] . "</p>");
		}
	}
	
	header("location: answerquestionsave.php?id=$questionid");
?>