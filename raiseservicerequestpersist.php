<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$opsystemid = $_POST['operatingsystemid'];
	$opsystemversionid = $_POST['opsystemversionid'];
	$technologyid = $_POST['technologysubid'][count($_POST['technologysubid']) - 1];
	$technologyversionid = $_POST['technologyversionid'];
	$architecture = $_POST['architecture'];
	$title = $_POST['title'];
	$body = mysql_escape_string($_POST['servicerequestbody']);
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	if (isset($_GET['id'])) {
		$servicerequestid = $_GET['id'];
		$qry = "UPDATE ols_servicerequest SET " .
				"title = '$title', " .
				"body = '$body', " .
				"technologyid = $technologyid, " .
				"technologyversionid = $technologyversionid, " .
				"opsystemid = $opsystemid, " .
				"opsystemversionid = $opsystemversionid, " .
				"architecture = '$architecture', " .
				"lastmodifieddate = NOW() " .
				"WHERE id = $servicerequestid";
		$result = mysql_query($qry);
		
	} else {
		$qry = "INSERT INTO ols_servicerequest " .
				"(title, body, technologyid, technologyversionid, opsystemid, opsystemversionid, architecture, createddate, memberid, published, status, lastmodifieddate) " .
				"VALUES " .
				"('$title', '$body', $technologyid, $technologyversionid, $opsystemid, $opsystemversionid, '$architecture', NOW(), $memberid, 'N', 'N', NOW())";
		$result = mysql_query($qry);
		$servicerequestid = mysql_insert_id();
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
		$qry = "INSERT INTO ols_servicerequestdocuments " .
				"(servicerequestid, documentid, createddate) " .
				"VALUES " .
				"($servicerequestid, " . $member['id'] . ", NOW())";
				
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
	
	sendRoleMessage("CONSULTANT", "Assistance required", "Consultant required for service request " . $title);
	sendUserMessage($memberid, "Verification requested", "Service request for " . $title);
	
	header("location: raiseservicerequestsave.php?id=$servicerequestid");
?>