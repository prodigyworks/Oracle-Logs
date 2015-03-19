<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$servicerequestid = $_GET['id'];
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	
	$qry = "UPDATE ols_servicerequest SET " .
			"consultantid = $memberid, " .
			"status = 'O' " .
			"WHERE id = $servicerequestid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	if (isset($_GET['reopen'])) {
		$qry = "SELECT A.memberid, A.title " .
				"FROM ols_servicerequest A " .
				"WHERE A.id = $servicerequestid";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage( $member['memberid'], "Service request re-opened", "Service request has been re-opened for " . $member['login']);
			}
			
		} else {
			logError($qry . " - " . mysql_error());
		}
		
		sendRoleMessage("ADMIN", "Service request re-opened", "Service request : " . $member['title'] . " has been re-opened.");
		
		$qry = "SELECT DISTINCT A.memberid, C.login " .
				"FROM ols_servicerequestanswers A " .
				"INNER JOIN ols_members C " .
				"ON C.member_id = A.memberid " .
				"WHERE A.servicerequestid = $servicerequestid";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage( $member['memberid'], "Service request re-opened", "Service request has been re-opened for " . $member['login']);
			}
		}
		
		sendRoleMessage("ADMIN", "Service request re-opened", "Service request : " . $member['title'] . " has been re-opened.");
	}
 
 	if (isset($_GET['callee'])) {
	 	header("location: viewservicerequest.php?callee=" . $_GET['callee'] . "&id=$servicerequestid");
	 	
 	} else {
	 	header("location: viewservicerequest.php?id=$servicerequestid");
 	}
?>