<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$body = mysql_escape_string($_POST['answerbody']);
	$servicerequestid = $_POST['servicerequestid'];
	
	$qry = "INSERT INTO ols_servicerequestanswers " .
			"(servicerequestid, memberid, createddate, body, published) " .
			"VALUES " .
			"($servicerequestid, $memberid, NOW(), '$body', 'N')";
			
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
		$qry = "INSERT INTO ols_servicerequestanswerdocuments " .
				"(answerid, documentid, createddate) " .
				"VALUES " .
				"($answerid, " . $member['id'] . ", NOW())";
				
		$itemresult = mysql_query($qry);
		
		if (! $itemresult) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	$qry = "UPDATE ols_servicerequest " .
		   "SET lastmodifieddate = NOW() " .
		   "WHERE id = $servicerequestid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "UPDATE ols_documents " .
		   "SET sessionid = NULL " .
		   "WHERE sessionid = '" . session_id() . "'";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	
	$qry = "SELECT consultantid, memberid, title FROM ols_servicerequest " .
		   "WHERE id = $servicerequestid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		sendUserMessage($member['consultantid'], "Service Request Response ", "<p>Response for service request " . $member['title'] . "</p>");
		sendUserMessage($member['memberid'], "Service Request Response ", "<p>Response for service request " . $member['title'] . "</p>");
	}
	
	header("location: answerservicerequestsave.php?id=$servicerequestid");
?>