<?php
	include("system-header.php"); 
?>
<script>
	function showFilter() {
		$("#userFilter").hide();
		$("#siteFilter").hide();
		$("#site").val("0");
		$("#user").val("0");
		
		if ($("#filter").find('option:selected').val() == "1") {
			$("#userFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "2") {
			$("#siteFilter").show();
		}
	}
</script>
<form method="POST">
	<label>FILTER BY</label>
	<select id="filter" onchange="showFilter()">
		<option value="0"></option>
		<option value=1>User</option>
		<option value=2>Site</option>
	</select>
	<div id="userFilter" style="display:none">
		<label>User</label>
		<?php createCombo("user", "member_id", "login", "jomon_members"); ?>

		<br>
		<input type="submit" value="go" />
	</div>
	<div id="siteFilter" style="display:none">
		<label>Site</label>
		<?php createCombo("site", "id", "name", "jomon_sites"); ?>
		
		<br>
		<input type="submit" value="go" />
	</div>
	<table class='grid list' id="completedorders" width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td>Job Number</td>
				<td>Customer</td>
				<td>Owner</td>
				<td>Site</td>
				<td>Date Created</td>
				<td>Date Completed</td>
			</tr>
		</thead>
		<?php
			$qry = "SELECT A.*, " .
					"DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%i') AS createdate, " .
					"DATE_FORMAT(A.completeddate, '%d/%m/%Y %H:%i') AS completeddate, " .
					"B.firstname, B.lastname, C.name AS sitename, D.prefix AS jobprefix, D.id AS jobid " .
					"FROM jomon_quoteheader A " .
					"INNER JOIN jomon_members B " .
					"ON B.member_id = A.createdby " .
					"INNER JOIN jomon_jobheader D " .
					"ON D.quoteid = A.id " .
					"INNER JOIN jomon_sites C " .
					"ON C.id = A.siteid " .
					"WHERE status IN ('Q', 'V', 'C') ";
					
			if (isset($_POST['site']) && $_POST['site'] != "0") {
				$siteid = $_POST['site'];
				$qry = $qry . " AND C.id = $siteid ";
			}
					
			if (isset($_POST['user']) && $_POST['user'] != "0") {
				$memberid = $_POST['user'];
				$qry = $qry . " AND B.member_id = $memberid ";
			}
					
			$qry = $qry . " ORDER BY A.id";
			$result = mysql_query($qry);
			
			if (! $result) die("Error: " . mysql_error());
			
			//Check whether the query was successful or not
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<tr>\n";
					echo "<td><a href='viewquote.php?id=" . $member['id'] . "'>" . $member['jobprefix'] . sprintf("%04d", $member['id']) . "</a></td>\n";
					echo "<td>" . $member['customer'] . "</td>\n";
					echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					echo "<td>" . $member['sitename'] . "</td>\n";
					echo "<td>" . $member['createdate'] . "</td>\n";
					echo "<td>" . $member['completeddate'] . "</td>\n";
					echo "</tr>\n";
				}
			}
		?>
	</table>
</form>
<?php
	include("system-footer.php"); 
?>