<?php
	include("system-header.php"); 
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$body = mysql_escape_string($_POST['answerbody']);
	$discussionid = $_POST['discussionid'];
	
	$qry = "INSERT INTO ols_discussionanswers " .
			"(discussionid, memberid, createddate, body, published) " .
			"VALUES " .
			"($discussionid, $memberid, NOW(), '$body', 'Y')";
			
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
		$qry = "INSERT INTO ols_discussionanswerdocuments " .
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
	
	
	$qry = "SELECT title FROM ols_discussion " .
		   "WHERE id = $discussionid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		sendRoleMessage("ADMIN", "Verification request", "<p>Verification required for answer regarding discussion " . $member['title'] . "</p>");
	}
?>
<h4>Answer has been submitted.</h4>
<?php
	include("system-footer.php"); 
?>