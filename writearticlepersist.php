<?php
	include("system-db.php"); 
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$title = mysql_escape_string($_POST['title']);
	$categoryid = $_POST['categoryid'];
	$tags = mysql_escape_string($_POST['tags']);
	$body = mysql_escape_string($_POST['articlebody']);
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	if (isUserInRole("CONSULTANT")) {
		$expirydate = $_POST['expirydate'];	
		$publisheddate = $_POST['publishdate'];	
		$publishedrole = $_POST['roleid'];	
		$featured = $_POST['featured'];	
		$mysql_publisheddate = substr($publisheddate, 6, 4 ) . "-" . substr($publisheddate, 3, 2 ) . "-" . substr($publisheddate, 0, 2 );
		$mysql_expirydate = substr($expirydate, 6, 4 ) . "-" . substr($expirydate, 3, 2 ) . "-" . substr($expirydate, 0, 2 );
		
		$qry = "INSERT INTO ols_article " .
				"(title, body, tags, createddate, categoryid, viewcount, memberid, featured, published, expirydate, publisheddate, publishedrole) " .
				"VALUES " .
				"('$title', '$body', '$tags', NOW(), $categoryid, 0, $memberid, '$featured', 'Y', '$mysql_expirydate', '$mysql_publisheddate', '$publishedrole')";
		
	} else {
		$qry = "INSERT INTO ols_article " .
				"(title, body, tags, createddate, categoryid, viewcount, memberid, featured, published) " .
				"VALUES " .
				"('$title', '$body', '$tags', NOW(), $categoryid, 0, $memberid, 'N', 'N')";
	}
	
			
	$result = mysql_query($qry);
	$articleid = mysql_insert_id();
	
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
		$qry = "INSERT INTO ols_articledocuments " .
				"(articleid, documentid, createddate) " .
				"VALUES " .
				"($articleid, " . $member['id'] . ", NOW())";
				
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
	
	if (! isUserInRole("CONSULTANT")) {
		sendRoleMessage("ADMIN", "Verification required", "Verification required for article " . $title);
	}
	
	sendUserMessage($memberid, "Article submitted", "Article: " . $title . " has been submitted");
	sendRoleMessage("CONSULTANT", "Article submitted", "Article: " . $title . " has been submitted");
	
	header("location: writearticlesave.php");
?>