<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
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
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	if (isset($_GET['id'])) {
		$questionid = $_GET['id'];
		$qry = "UPDATE ols_question SET " .
				"title = '$title', " .
				"body = '$body', " .
				"technologyid = $technologyid, " .
				"technologyversionid = $technologyversionid, " .
				"opsystemid = $opsystemid, " .
				"opsystemversionid = $opsystemversionid, " .
				"architecture = '$architecture' " .
				"WHERE id = $questionid";
		$result = mysql_query($qry);
		
	} else {
		$qry = "INSERT INTO ols_question " .
				"(title, body, technologyid, technologyversionid, opsystemid, opsystemversionid, architecture, createddate, memberid, published) " .
				"VALUES " .
				"('$title', '$body', $technologyid, $technologyversionid, $opsystemid, $opsystemversionid, '$architecture', NOW(), $memberid, 'N')";
		$result = mysql_query($qry);
		$questionid = mysql_insert_id();
	}
			
	
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
	
	sendRoleMessage("ADMIN", "Verification required", "Verification required for question " . $title);
	sendUserMessage($memberid, "Verification requested", "Verification requested for question " . $title);
	
	header("location: askquestionsave.php?id=$questionid");
?>