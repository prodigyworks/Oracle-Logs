<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$questionid = $_POST['questionid'];	
	$expirydate = $_POST['expirydate'];	
	$publisheddate = $_POST['publishdate'];	
	$publishedrole = $_POST['roleid'];	
	$body = mysql_escape_string($_POST['questionbody']);	
	$mysql_publisheddate = substr($publisheddate, 6, 4 ) . "-" . substr($publisheddate, 3, 2 ) . "-" . substr($publisheddate, 0, 2 );
	$mysql_expirydate = substr($expirydate, 6, 4 ) . "-" . substr($expirydate, 3, 2 ) . "-" . substr($expirydate, 0, 2 );
	
	$qry = "UPDATE ols_question SET " .
		   "expirydate = '$mysql_expirydate', " .
		   "publisheddate = '$mysql_publisheddate', " .
		   "published = 'Y', " .
		   "body = '$body', " .
		   "publishedrole = '$publishedrole' " .
		   "WHERE id = $questionid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "SELECT A.memberid, A.title " .
			"FROM ols_question A " .
			"WHERE A.id = $questionid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			sendUserMessage($member['memberid'], "Question publication", "<h2>Article: " . $member['title'] . "</h2><p>Has been published</p>");
			sendRoleMessage("ADMIN", "Question publication", "<h2>Article: " . $member['title'] . "</h2><p>Has been published</p>");
			
    		$qry = "SELECT * FROM ols_members A " .
    				"WHERE A.forumalerts = 1";
			$itemresult = mysql_query($qry);
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribeforum.php?id=" . base64_encode($itemmember['member_id']) . "'>Unsubscribe</a></p>";
			
					sendUserMessage(
							$itemmember['member_id'],
							"New Forum Alert",
							"<p>A new forum has been published</p><h3>Title: " . $member['title'] . "</h3>",
							$disclaimer
						);
				}
				
			} else {
				logError($qry . " = " . mysql_error());
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	header("location: publishquestionsave.php?id=$questionid");
?>