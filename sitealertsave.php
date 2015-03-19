<?php
	include("system-header.php"); 
	
	if (isset($_POST['checked'])) {
		$counter = count($_POST['checked']);
		$body = $_POST['deploynotes'];
		$title = $_POST['deploytitle'];
	
		$qry = "INSERT INTO ols_sitealert(title, body, createddate) VALUE ('$title', '" . mysql_escape_string($body) . "', NOW())";
		$result = mysql_query($qry);
		$alertid = mysql_insert_id();
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		for ($i = 0; $i < $counter; $i++) {
			$memberid = $_POST['checked'][$i];
			$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribesite.php?id=" . base64_encode($memberid) . "'>Unsubscribe</a></p>";
			
			sendUserMessage(
					$memberid,
					"Oracle Logs Alert",
					$body
				);
				
			$qry = "INSERT INTO ols_sitealertmembers(alertid, memberid) VALUE ($alertid, $memberid)";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
		}

		echo "<h4>Email alert has been sent.</h4>";
	}

	include("system-footer.php"); 
?>