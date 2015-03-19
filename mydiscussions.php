<?php
	require_once("system-db.php");
	require_once("discussiontemplate.php");
	
	$memberid = getLoggedOnMemberID();
	
	discussion("WHERE A.memberid = $memberid");
?>
