<?php
	require_once("system-db.php");
	require_once("jobtemplate.php");
	
	$memberid = getLoggedOnMemberID();
	
	job("WHERE C.memberid = $memberid");
?>
