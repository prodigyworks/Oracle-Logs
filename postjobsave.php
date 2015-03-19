<?php
	include("system-db.php"); 
	
	start_db();
	initialise_db();
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$type = $_POST['type'];
	$location = $_POST['location'];
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];
	$ref = $_POST['ref'];
	$title = $_POST['title'];
	$description = mysql_escape_string($_POST['description']);
	$rate = $_POST['rate'];
	$currency = $_POST['currency'];
	$rateper = $_POST['rateper'];
	$salary = $_POST['salary'];
	
	$qry = "INSERT INTO ols_job " .
			"(jobtype, location, lat, lng, reference, title, description, rate, salary, createddate, memberid, status, currency, rateper) " .
			"VALUES " .
			"('$type', '$location', $lat, $lng, '$ref', '$title', '$description', '$rate', '$salary', NOW(), $memberid, 'O', '$currency', '$rateper')";
			
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
			
	$qry = "SELECT * FROM ols_members A " .
			"WHERE A.jobalerts = 1";
	$itemresult = mysql_query($qry);
	
	if ($itemresult) {
		while (($itemmember = mysql_fetch_assoc($itemresult))) {
			$disclaimer = "<p style='font-size:10px'>Click here to <a href='" . getSiteConfigData()->domainurl . "/unsubscribejob.php?id=" . base64_encode($itemmember['member_id']) . "'>Unsubscribe</a></p>";
			
			sendUserMessage(
					$itemmember['member_id'],
					"New Job Posting",
					"<p>A new job has been posted</p><h4>Title: " . $member['title'] . "</h4><p>Ref: $ref</p><p>$description</p>",
					$disclaimer
				);
		}
		
		sendRoleMessage(
				"ADMIN",
				"New Job Posting",
				"<p>A new job has been posted</p><h4>Title: " . $member['title'] . "</h4><p>Ref: $ref</p><p>$description</p>"
			);
		
	} else {
		logError($qry . " = " . mysql_error());
	}

	header("location: jobs.php");
?>