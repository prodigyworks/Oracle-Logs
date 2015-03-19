<?php
	require_once("system-db.php"); 
	
	authenticate();
	
	require_once("system-header.php"); 

	if (isset($_GET['id'])) {
		$id = $_GET['id'];
   		$memberid = $_SESSION['SESS_MEMBER_ID'];
		
		$qry = "SELECT B.firstname, B.website, B.lastname, B.imageid, C.id AS applicationid, A.* " .
			   "FROM ols_job A " .
			   "INNER JOIN ols_members B " .
			   "ON B.member_id = A.memberid " .
			   "LEFT OUTER JOIN ols_jobapplications C " .
			   "ON C.memberid = $memberid " .
			   "AND C.jobid = A.id " .
			   "WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				echo "<h4>";
				
				if ($member['imageid'] != null && $member['imageid'] != 0) {
					echo "<img height=32 src='system-imageviewer.php?id=" .$member['imageid'] . "' />&nbsp;";
				}
				
				echo $member['firstname'] . " " . $member['lastname'];
				echo "</h4><br><br>";
?>							
<table width='100%' cellspacing=10 width=100% class='fixed' id="xx" cellspacing=0 cellpadding=0>
<tbody>
<?php
				
				echo "<tr>";
				echo "<td nowrap class='label'><h2>" . $member['title'] . "</h2></td>";
				
				if (! $member['applicationid']) {
					echo "<td align=right><a id='apply' class='link1 fright' href='javascript:void()'><em><b>Apply Now</b></em></a></td>";
					
				} else {
					echo "<td align=right><a class='link1 fright' href='javascript:'><em><b>Applied</b></em></a></td>";
				}
				
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td colspan=2>\n";
				echo "<div id='jobdetail'>" . $member['description'] . "</div>";
				echo "</tr>\n";
				
				echo "<tr>";
				echo "<td colspan=2><hr></td>";
				echo "</tr>\n";
							
				echo "<tr>";
				echo "<td class='label'>Location</td>";
				echo "<td>" . $member['location'] . "</td>";
				echo "</tr>\n";
				
				echo "<tr>";
				echo "<td class='label'>Recruitment Agent</td>";
				echo "<td><a href='" . $member['website'] . "'>" . $member['firstname'] . " " . $member['lastname'];
				
				if ($member['imageid'] != null && $member['imageid'] != 0) {
					echo "</a>&nbsp;<img src='system-imageviewer.php?id=" . $member['imageid'] . "' height=16 />";
				}
				
				echo "</td>";
				echo "</tr>\n";
				
				echo "<tr>";
				echo "<td class='label'>Reference</td>";
				echo "<td>" . $member['reference'] . "</td>";
				echo "</tr>\n";
				
				echo "<tr>";
				echo "<td class='label'>Posted</td>";
				echo "<td>" . $member['createddate'] . "</td>";
				echo "</tr>\n";
				echo "<tr>";
				echo "<td colspan=2><hr></td>";
				echo "</tr>\n";
?>
</tbody>
</table>
<script>
	$(document).ready(function() {
			$("#apply").click(function() {
					window.location.href = 'jobapply.php?id=<?php echo $_GET['id']; ?>';
				});
		});
</script>
<?php
						}
					} else {
						logError($qry . " = " . mysql_error());
		}
	}

	include("system-footer.php"); 
?>