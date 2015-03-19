<?php
	require_once('system-db.php');
	require_once("servicerequesttemplate.php");
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	serviceRequest(
			"WHERE A.status IN ('C') " .
	    	"AND A.memberid = $memberid ",
	    	false
		);
?>
