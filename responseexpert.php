<?php 
	include("system-header.php");
	
	$chatsessionid = $_GET['id'];
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$alreadyused = false;
	
	$qry = "SELECT A.id, A.responsesessionid " .
			"FROM ols_chatsession A " .
			"WHERE A.id = $chatsessionid";

	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$chatsessionid = $member['id'];
			
			if ($member['responsesessionid'] == null) {	
				$qry = "UPDATE ols_chatsession " .
						"SET responsememberid = $memberid, " .
						"responsesessionid = '" . session_id() . "' " .
						"WHERE id = $chatsessionid";
						
				$itemresult = mysql_query($qry);
				
				if (! $itemresult) {
					logError($qry . " = " . mysql_error());
				}
				
			} else if ($member['responsesessionid'] != session_id()) {
				echo "<h1>Session already in play</h1>";
				$alreadyused = true;
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	if (! $alreadyused) {
		include("expert.php");
	}
?>
</script>
<!--  End of content -->
<?php include("system-footer.php") ?>
		