<?php
	require_once('system-db.php');
	require_once("servicerequesttemplate.php");

	
	$memberid = getLoggedOnMemberID();
	
	serviceRequest(
			"WHERE A.memberid = $memberid ",
			false
		);
?>
