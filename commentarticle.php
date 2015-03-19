<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$id = $_GET['id'];
	$body = mysql_escape_string($_POST['comment']);
	$memberid = getLoggedOnMemberID();
	$qry = "INSERT INTO ols_articlecomments (" .
			"articleid, memberid, body, createddate" .
			") VALUES (" .
			"$id, $memberid, '$body', NOW()" .
			")";
	
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_error());
	}
	
	header("location: viewarticle.php?id=$id");
?>