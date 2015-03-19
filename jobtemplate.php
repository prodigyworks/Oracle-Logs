<?php
function job($where) {
	include("system-header.php"); 
	
	$data = getSiteConfigData();
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "jobdetail.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
?>
<div class='jobs'>
	<table cellspacing=10 width=100% class='fixed' id="xx" cellspacing=0 cellpadding=0>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$memberid = $_SESSION['SESS_MEMBER_ID'];
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		
				$qry = "SELECT " .
					   "DATE_FORMAT(C.createddate, '%d/%m/%Y') AS applieddate, " .
					   "DATE_FORMAT(A.createddate, '%d/%m/%Y') AS posteddate, " .
					   "B.firstname, B.website, B.lastname, B.imageid, A.* FROM ols_job A " .
					   "INNER JOIN ols_members B " .
					   "ON B.member_id = A.memberid " .
					   "LEFT OUTER JOIN ols_jobapplications C " .
					   "ON C.jobid = A.id " .
					   "$where " .
	    			   "LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						};
						echo "<tr>";
						echo "<td class='label'>&nbsp</td>";
						echo "<td>&nbsp;</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td colspan=2 class='title'><a href='$viewer&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "</tr>\n";
					
						echo "<tr>";
						echo "<td class='label'>Applied For On</td>";
						echo "<td>" . $member['applieddate'] . "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Location</td>";
						echo "<td>" . $member['location'] . "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Recruitment Agent</td>";
						echo "<td>";
						echo "<a href='" . $member['website'] . "'>" . $member['firstname'] . " " . $member['lastname'] . "</a>&nbsp;";
						
						if ($member['imageid'] != null && $member['imageid'] != 0) {
							echo "<img src='system-imageviewer.php?id=" . $member['imageid'] . "' height=16 />";
						}
						
						echo "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Reference</td>";
						echo "<td>" . $member['reference'] . "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Posted</td>";
						echo "<td>" . $member['posteddate'] . "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Type</td>";
						echo "<td>" . ($member['jobtype'] == "P" ? "Permanent" : "Contract") . "</td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td class='label'>Currency</td>";
						echo "<td>" . $member['currency'] . "</td>";
						echo "</tr>\n";
						
						if ($member['rateper'] && $member['rateper'] != "") {
							echo "<tr>";
							echo "<td class='label'>Rate Per</td>";
							echo "<td>";
							?>
							<SELECT id="rateper" name="rateper" class="hidden" value="<?php echo $member['rateper']; ?>">
								<OPTION value="HOUR">Hour</OPTION>
								<OPTION value="WEEK">Week</OPTION>
								<OPTION value="MONTH">Month</OPTION>
								<OPTION value="YEAR">Year</OPTION>
							</SELECT>
							<DIV class="textvalue"></DIV>
							<?php
							echo "</tr>\n";
						}
						
						if ($member['rate'] && $member['rate'] != "") {
							echo "<tr>";
							echo "<td class='label'>Rate</td>";
							echo "<td>";
							?>
							<SELECT id="rate" name="rate" class="hidden" value="<?php echo $member['rate']; ?>">
								<OPTION value="ZEROTOTEN">0 - 10</OPTION>
								<OPTION value="TENTOTWENTY">10 - 20</OPTION>
								<OPTION value="TWENTYTOTHIRTY">20 - 30</OPTION>
								<OPTION value="THIRTYTOFORTY">30 - 40</OPTION>
								<OPTION value="FORTYTOFIFTY">40 - 50</OPTION>
								<OPTION value="FIFTYTOHUNDRED">50 - 100</OPTION>
								<OPTION value="HUNDREDPLUS">100+</OPTION>
							</SELECT>
							<DIV class="textvalue"></DIV>
							<?php
							echo "</td>";
							echo "</tr>\n";
						}
						
						if ($member['salary'] && $member['salary'] != "") {
							echo "<tr>";
							echo "<td class='label'>Salary Range</td>";
							echo "<td>";
							?>
							<SELECT id="salary" name="salary" class="hidden" value="<?php echo $member['salary']; ?>">
								<OPTION value="ZEROTOTEN">0 - 10,000</OPTION>
								<OPTION value="TENTOTWENTY">10,000 - 20,000</OPTION>
								<OPTION value="TWENTYTOTHIRTY">20,000 - 30,000</OPTION>
								<OPTION value="THIRTYTOFORTY">30,000 - 40,000</OPTION>
								<OPTION value="FORTYTOFIFTY">40,000 - 50,000</OPTION>
								<OPTION value="FIFTYTOHUNDRED">50,000 - 100,000</OPTION>
								<OPTION value="HUNDREDPLUS">100,000+</OPTION>
							</SELECT>
							<DIV class="textvalue"></DIV>
							<?php
							echo "</td>";
							echo "</tr>\n";
						}
						
						echo "<tr>";
						echo "<td colspan=2><div>" . $member['description'] . "</div><br><a href='$viewer&id=" . $member['id'] . "'>More</a></td>";
						echo "</tr>\n";
						
						echo "<tr>";
						echo "<td colspan=2><hr></td>";
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
<script>
	$(document).ready(function() {
			$(".textvalue").each(function() {
					$(this).prev().val($(this).prev().attr("value"));
					$(this).html($(this).prev().find("option:selected").text());
				});
		});
</script>
<?php
	include("system-footer.php");
} 
?>