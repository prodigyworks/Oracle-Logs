<?php
	include("system-header.php"); 
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$qry = "SELECT  A.memberid, B.id, B.body " .
				"FROM ols_sitealertmembers A " .
				"INNER JOIN ols_sitealert B " .
				"ON A.id = B.alertid " . 
				"WHERE A.alertid = $id ";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribesite.php?id=" . base64_encode($member['memberid']) . "'>Unsubscribe</a></p>";
			
				sendUserMessage(
						$member['memberid'],
						"Oracle Logs Alert",
						$member['body']
					);
			}
		}

		echo "<h4>Email alert has been resent.</h4>";
	}

	include("system-footer.php"); 
?>