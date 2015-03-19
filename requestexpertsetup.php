<?php 
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	if (! isAuthenticated()) {
		header("location: system-register.php");
	}
	
	$chatsessionid = 0;
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$opsystemid = $_POST['operatingsystemid'];
	$opsystemversionid = $_POST['opsystemversionid'];
	
	if (isset($_POST['technologysubid'])) {
		$technologyid = $_POST['technologysubid'][count($_POST['technologysubid']) - 1];
		
	} else {
		$technologyid = $_POST['technologyid'];
	}
	
	$technologyversionid = $_POST['technologyversionid'];
	$architecture = $_POST['architecture'];
	$title = $_POST['title'];
	$body = mysql_escape_string($_POST['questionbody']);
	
	$qry = "INSERT INTO ols_question " .
			"(title, body, technologyid, technologyversionid, opsystemid, opsystemversionid, architecture, createddate, memberid, published) " .
			"VALUES " .
			"('$title', '$body', $technologyid, $technologyversionid, $opsystemid, $opsystemversionid, '$architecture', NOW(), $memberid, 'X')";
			
	$result = mysql_query($qry);
	$questionid = mysql_insert_id();
	
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
		$qry = "INSERT INTO ols_questiondocuments " .
				"(questionid, documentid, createddate) " .
				"VALUES " .
				"($questionid, " . $member['id'] . ", NOW())";
				
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
	
	$qry = "SELECT A.id FROM ols_chatsession A " .
			"WHERE A.requestsessionid = '" . session_id() . "' " .
			"AND A.status = 'O' " .
			"AND A.lastaccessdate >= (NOW() - INTERVAL 5 MINUTE)";

	$result = mysql_query($qry);
	$found = false;
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$found = true;
			$chatsessionid = $member['id'];
		}
	}

	if (! $found) {	
		$sessionid = session_id();
		$qry = "INSERT INTO ols_chatsession " .
				"(requestmemberid, requestsessionid, createddate, lastaccessdate, status, questionid) " .
				"VALUES " .
				"($memberid, '$sessionid', NOW(), NOW(), 'O', $questionid)"; 
				
		$result = mysql_query($qry);
		$chatsessionid = mysql_insert_id();
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	header("location: requestexpert.php?id=" . $chatsessionid);
?>
