<?php
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	require_once("jobtemplate.php");
	
	if (isUserInRole("ADMIN")) {
		job("");
		
	} else {
		$memberid = getLoggedOnMemberID();
		
		job("WHERE C.memberid = $memberid");
	}
?>
