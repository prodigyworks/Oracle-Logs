<?php
	require_once('system-db.php');
	require_once("articletemplate.php");

	$memberid = getLoggedOnMemberID();
	
	article("WHERE A.memberid = $memberid AND published = 'X'");
?>
