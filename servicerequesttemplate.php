<?php
function serviceRequest($where, $isadmin = true) {
	include("system-header.php"); 
	
						
	$currentmemberid = $_SESSION['SESS_MEMBER_ID'];
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "viewservicerequest.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
	$reopen = "answerservicerequest.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
?>
<div class='articles'>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='80px'>Created</td>
	        <td width='120px'>Last Updated</td>
	        <td>Title</td>
	        <?php
	        if ($isadmin) {
	        ?>
		        
		        <td width='120px'>Author</td>
		        
	        <?php
	        } else {
	        ?>
	        	
		        <td width='120px'>Consultant</td>
		        
	        <?php
	        }
	        ?>
	        
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT DISTINCT A.id, A.closurenotes, A.title, A.status, A.memberid, A.consultantid, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"DATE_FORMAT(A.lastmodifieddate, '%d/%m/%Y %T') AS lastmodifieddate, " .
	    				"B.login, " .
	    				"D.login AS consultantlogin, " .
						"(SELECT COUNT(*) FROM ols_servicerequestanswers X  WHERE X.servicerequestid = A.id) AS answered " .
						"FROM ols_servicerequest A " . 
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"LEfT OUTER JOIN ols_members D " .
	    				"ON D.member_id = A.consultantid " .
	    				"$where " .
	    				"ORDER BY A.id DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr>\n";
						
						if ($member['status'] == "O") {
							echo "<td><img src='images/open.png' title='Open' /></td>";
							
						} else if ($member['status'] == "N") {
							echo "<td><img src='images/new.png' title='New' /></td>";
							
						} else {
							echo "<td><img src='images/closed.png' title='" . ($member['closurenotes']) . "' /></td>";
						}
						
						if ($member['answered'] > 0) {
							echo "<td><img src='images/answered.png' title='Answered' /></td>";
							
						} else {
							echo "<td><img src='images/unanswered.png' /></td>";
						}
						
						echo "<td>" . $member['createddate'] . "</td>";
						echo "<td>" . $member['lastmodifieddate'] . "</td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						
						if ($isadmin) {
							echo "<td width='120px'>" . $member['login'] . "</td>";
							
						} else {
							echo "<td width='120px'>" . $member['consultantlogin'] . "</td>";
						}
						
						if ($member['status'] == "N") {
							echo "<td width='20px'><a href='raiseservicerequest.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF'])) . "&id=" . $member['id'] . "'><img src='images/edit.png'  title='View service request' /></a></td>";
							
						} else {
							echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img src='images/view.png'  title='View service request' /></a></td>";
						}

						if ($member['status'] == "C") {
							echo "<td width='20px'><a href='$reopen&id=" . $member['id'] . "'><img src='images/reopen.png'  title='Reopen' /></a></td>";
							
						} else if ($member['status'] == "N") {
							echo "<td width='20px'><a href='$reopen&id=" . $member['id'] . "'><img src='images/accept.png'  title='Accept' /></a></td>";
							
						} else if (($currentmemberid == $member['memberid'] || 
									$currentmemberid ==  $member['consultantid'] || 
									isUserInRole("ADMIN"))) {
							echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img src='images/allocate.png'  title='View / Respond' /></a></td>";
										
						} else {
							echo "<td width='20px'><img src='images/lock.png'  title='Already allocated to " . $member['consultantlogin'] ."' /></td>";
						}
						echo "</tr>\n";
					}
				} else {
					logError($qry . " = " . mysql_error());
				}
	    	?>
	    </tbody>
	</table>
	
	<?php
		if ($prevpage) {
			echo "<img src='images/previouspage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow - $pagesize) . "&to=" . ($torow - $pagesize) . "\"' />";
		}
		
		if ($nextpage) {
			echo "<img src='images/nextpage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow + $pagesize) . "&to=" . ($torow + $pagesize) . "\"' />";
		}
	?>
</div>
<?php
	include("system-footer.php"); 
}
?>	
