<?php
	include("system-header.php"); 
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$alertid = $_GET['id'];
?>
<div class='articles'>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='100px'>Login</td>
	        <td width='120px'>First Name</td>
	        <td width='120px'>Last Name</td>
	        <td>Email</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT B.login, B.firstname, B.lastname, B.email " .
						"FROM ols_sitealertmembers A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"WHERE A.alertid = $alertid  " . 
	    				"ORDER BY B.login " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr>\n";
						
						echo "<td width='100px'>" . $member['login'] . "</td>";
						echo "<td width='120px'>" . $member['firstname'] . "</td>";
						echo "<td width='120px'>" . $member['lastname'] . "</td>";
						echo "<td>" . $member['email'] . "</td>";
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
?>