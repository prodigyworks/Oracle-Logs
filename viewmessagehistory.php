<?php 
	include("system-header.php"); 
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$qry = "SELECT A.* FROM ols_chatsession A " .
			"WHERE A.requestmemberid = $memberid " .
			"ORDER BY A.id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$qry = "SELECT A.*, B.login FROM ols_chatmessages A " .
					"INNER JOIN ols_members B " .
					"ON B.member_id = A.memberid " .
					"WHERE A.chatsessionid = " . $member['id'] . " " .
					"ORDER BY A.id";
			$itemresult = mysql_query($qry);
			
			echo "<h4>Session request on " . $member['createddate'] . "</h4>";
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					echo "<div>" . $itemmember['createddate'] . " - " . $itemmember['login'] . " : " . $itemmember['message'] . "</div>";
				}
			}
			
			echo "<hr />";
		}
	}
	
	include("system-footer.php"); 
?>
