<?php
	require_once("system-db.php");
	require_once("adverttemplate.php");
	
	$memberid = getLoggedOnMemberID();
	
	advert("WHERE member_id = $memberid", "viewmyadvert.php");
?>
