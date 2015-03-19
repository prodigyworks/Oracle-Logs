<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$articleid = $_POST['articleid'];	
	$expirydate = $_POST['expirydate'];	
	$articlebody = mysql_escape_string($_POST['articlebody']);	
	$publisheddate = $_POST['publishdate'];	
	$publishedrole = $_POST['roleid'];	
	$featured = $_POST['featured'];	
	$mysql_publisheddate = substr($publisheddate, 6, 4 ) . "-" . substr($publisheddate, 3, 2 ) . "-" . substr($publisheddate, 0, 2 );
	$mysql_expirydate = substr($expirydate, 6, 4 ) . "-" . substr($expirydate, 3, 2 ) . "-" . substr($expirydate, 0, 2 );
	
	$qry = "UPDATE ols_article SET " .
		   "expirydate = '$mysql_expirydate', " .
		   "publisheddate = '$mysql_publisheddate', " .
		   "published = 'Y', " .
		   "body = '$articlebody', " .
		   "featured = '$featured', " .
		   "publishedrole = '$publishedrole' " .
		   "WHERE id = $articleid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "SELECT A.memberid, A.title " .
			"FROM ols_article A " .
			"WHERE A.id = $articleid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			sendUserMessage($member['memberid'], "Article publication", "<h2>Article: " . $member['title'] . "</h2><p>Has been published</p>");
			sendRoleMessage("ADMIN", "Article publication", "<h2>Article: " . $member['title'] . "</h2><p>Has been published</p>");
			
    		$qry = "SELECT * FROM ols_members A " .
    				"WHERE A.articlealerts = 1";
			$itemresult = mysql_query($qry);
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribearticle.php?id=" . base64_encode($itemmember['member_id']) . "'>Unsubscribe</a></p>";
					
					sendUserMessage(
							$itemmember['member_id'],
							"New Discussion Alert",
							"<p>A new discussion has been published</p><h3>Title: " . $member['title'] . "</h3>"
						);
				}
				
			} else {
				logError($qry . " = " . mysql_error());
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	header("location: publisharticlesave.php?id=$articleid");
?>