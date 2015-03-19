<?php
	require_once('system-db.php');
	require_once("questiontemplate.php");

	
	$memberid = getLoggedOnMemberID();
	
	question("WHERE A.memberid = $memberid AND A.published = 'Y'");
?>
