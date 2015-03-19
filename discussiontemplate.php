<?php
	
function hot() {
	$id = $_POST['pk1'];
	$qry = "UPDATE ols_discussion SET hot = 'Y' WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}
	
function discussion($where) {
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "viewdiscussion.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
	
	createConfirmDialog("confirmdialog", "Make hot ?", "makeHot");
?>
<script>
	var discussionID = null;
		
	function makeHot() {
		call("hot", {pk1: discussionID});
	}
	
	function hotDiscussion(id) {
		discussionID = id;
		
		$("#confirmdialog").dialog("open");
	}
	
	$(document).ready(function() {
			$("#confirmdialog .confirmdialogbody").html("You are about to make this discussion hot.<br>Are you sure ?");
		});
		
</script>
<a class='link1' href="startdiscussion.php"><em><b>Start Discussion</b></em></a>
<br>
<br>
<div class='articles'>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='80px'>Date</td>
	        <td>Title</td>
	        <td width='120px'>Author</td>
	        <td width='20px'>&nbsp;</td>
	        <?php
        	if (isUserInRole("ADMIN")) {
	        ?>
	        <td width='20px'>&nbsp;</td>
	        <?php
        	}
	        ?>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT DISTINCT A.id, A.title, A.hot, A.published, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"B.login, " .
						"(SELECT COUNT(*) FROM ols_discussionanswers X  WHERE X.discussionid = A.id AND published = 'Y') AS answered " .
						"FROM ols_discussion A " . 
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"$where " .
	    				"ORDER BY A.hot, A.id DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						if ($member['hot'] == "Y") {
							echo "<tr class='featured'>";
							echo "<td><img src='images/hot.png' /></td>";
							
						} else {
							echo "<tr>\n";
							echo "<td>&nbsp;</td>";
						}
						
						if ($member['published'] == "Y") {
							echo "<td><img src='images/published.png' /></td>";
						
						} else if ($member['published'] == "X") {
							echo "<td width='20px'><img src='images/cancel.png'  title='Cancelled' /></td>";
							
						} else {
							echo "<td width='20px'><img src='images/unpublished.png' title='Awaiting Publish' /></td>";
						}
						
						if ($member['answered'] > 0) {
							echo "<td><img src='images/answered.png' title='Answered' /></td>";
							
						} else {
							echo "<td><img src='images/unanswered.png' /></td>";
						}
						
						echo "<td>" . $member['createddate'] . "</td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "<td width='120px'>" . $member['login'] . "</td>";
						echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img src='images/view.png'  title='View article' /></a></td>";

			        	if (isUserInRole("ADMIN")) {
			        		if ($member['hot'] == "Y") {
								echo "<td width='20px'>&nbsp;</td>";
			        			
			        		} else {
								echo "<td width='20px'><a href='javascript: hotDiscussion(" . $member['id'] . ")'><img src='images/hot.png'  title='Mark as hot' /></a></td>";
			        		}
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