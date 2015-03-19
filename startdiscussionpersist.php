<?php
	require_once("system-db.php"); 
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$title = mysql_escape_string($_POST['title']);
	$body = mysql_escape_string($_POST['discussionbody']);
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	$qry = "INSERT INTO ols_discussion " .
			"(title, body, createddate, memberid, published, hot) " .
			"VALUES " .
			"('$title', '$body', NOW(), $memberid, 'Y', 'N')";
			
	$result = mysql_query($qry);
	$discussionid = mysql_insert_id();
	
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
		$qry = "INSERT INTO ols_discussiondocuments " .
				"(discussionid, documentid, createddate) " .
				"VALUES " .
				"($discussionid, " . $member['id'] . ", NOW())";
				
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
	
	sendRoleMessage("ADMIN", "Discussion", "Discussion: " . $title . " started.");
	
	header("location: startdiscussionsave.php");
?>
