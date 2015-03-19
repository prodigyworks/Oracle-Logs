<?php
	include("system-header.php"); 
	
	if (isset($_POST['checked'])) {
		$counter = count($_POST['checked']);
		$body = $_POST['deploynotes'];
		$title = $_POST['deploytitle'];
	
		$qry = "INSERT INTO ols_bulkemail(name, body, createddate) VALUE ('$title', '" . mysql_escape_string($body) . "', NOW())";
		$result = mysql_query($qry);
		$alertid = mysql_insert_id();
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		for ($i = 0; $i < $counter; $i++) {
			$memberid = $_POST['checked'][$i];
			$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribecontact.php?id=" . base64_encode($memberid) . "'>Unsubscribe</a></p>";
			
			sendContactMessage(
					$memberid,
					"Oracle Logs Alert",
					$body,
					$disclaimer
				);
				
			$qry = "INSERT INTO ols_bulkemailcontacts(bulkemailid, contactid) VALUE ($alertid, $memberid)";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
		}

		echo "<h4>Bulk email has been sent.</h4>";
	}

	include("system-footer.php"); 
?>