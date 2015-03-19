<?php
	include("system-header.php"); 
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$qry = "SELECT  A.contactid, B.id, B.body " .
				"FROM ols_bulkemailcontacts A " .
				"INNER JOIN ols_bulkemail B " .
				"ON B.id = A.bulkemailid " . 
				"WHERE A.bulkemailid = $id ";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribecontact.php?id=" . base64_encode($member['contactid']) . "'>Unsubscribe</a></p>";
				
				sendContactMessage(
						$member['contactid'],
						"Oracle Logs Alert",
						$member['body'],
						$disclaimer
					);
			}
		} else {
			logError($qry . " = " . mysql_error());
		}

		echo "<h4>Email alert has been resent.</h4>";
	}

	include("system-footer.php"); 
?>