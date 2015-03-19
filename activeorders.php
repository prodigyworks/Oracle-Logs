<?php
	include("system-header.php"); 
?>
<script>
	function showFilter() {
		$("#userFilter").hide();
		$("#siteFilter").hide();
		$("#statusFilter").hide();
		$("#customerFilter").hide();
		$("#ccfFilter").hide();
		$("#site").val("0");
		$("#user").val("0");
		
		if ($("#filter").find('option:selected').val() == "1") {
			$("#userFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "2") {
			$("#siteFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "3") {
			$("#statusFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "4") {
			$("#customerFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "5") {
			$("#ccfFilter").show();
		}
	}
</script>
<form method="POST">
	<label>FILTER BY</label>
	<select id="filter" onchange="showFilter()">
		<option value="0"></option>
		<option value=1>User</option>
		<option value=2>Site</option>
		<option value=3>Status</option>
		<option value=4>Customer</option>
		<option value=5>CCF</option>
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
	<div id="customerFilter" style="display:none">
		<label>Customer</label>
		<input type="text" value="" id="customer" name="customer" style="width:260px" />
		
		<br>
		<input type="submit" value="go" />
	</div>
	<div id="ccfFilter" style="display:none">
		<label>CCF PO</label>
		<input type="text" value="" id="ccf" name="ccf" style="width:260px" />
		
		<br>
		<input type="submit" value="go" />
	</div>
	<div id="statusFilter" style="display:none">
		<label>Status</label>
		<select id="status" name="status">
			<option value="A">Verified</option>
			<option value="I">Scheduled</option>
			<option value="C">Completed</option>
			<option value="Q">QA</option>
			<option value="V">Handed Over</option>
		</select>
		
		<br>
		<input type="submit" value="go" />
	</div>
	<table id="activeorders" class='grid list fixed' width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td width='100px'>Job Number</td>
				<td width='360px'>Customer</td>
				<td width='180px'>Cost Code</td>
				<td width='50px'>Site</td>
				<td>Status</td>
				<td width='100px'>Date Created</td>
				<td>Expected</td>
			</tr>
		</thead>
		<?php
			$qry = "SELECT A.*, DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%i') AS createdate, D.prefix AS jobprefix, D.id AS jobid, " .
					"DATE_FORMAT(A.requiredby, '%d/%m/%Y') AS requiredby, B.firstname, B.lastname, C.name AS sitename " .
					"FROM jomon_quoteheader A " .
					"INNER JOIN jomon_jobheader D " .
					"ON D.quoteid = A.id " .
					"INNER JOIN jomon_members B " .
					"ON B.member_id = A.createdby " .
					"INNER JOIN jomon_sites C " .
					"ON C.id = A.siteid " .
					"WHERE status IN ('A', 'S', 'Q', 'I') ";
					
			if (isset($_POST['site']) && $_POST['site'] != "0") {
				$siteid = $_POST['site'];
				$qry = $qry . " AND C.id = $siteid ";
			}
					
			if (isset($_POST['ccf']) && $_POST['ccf'] != "") {
				$ccf = $_POST['ccf'];
				$qry = $qry . " AND A.ccf LIKE '$ccf%' ";
			}
					
			if (isset($_POST['customer']) && $_POST['customer'] != "") {
				$customer = $_POST['customer'];
				$qry = $qry . " AND A.customer LIKE '$customer%' ";
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
					echo "<td nowrap><a href='viewquote.php?id=" . $member['id'] . "'>" . $member['jobprefix'] . sprintf("%04d", $member['id']) . "</a></td>\n";
					echo "<td>" . $member['customer'] . "</td>\n";
					echo "<td>" . GetCostCode($member['costcode'])  . "</td>\n";
					echo "<td>" . $member['sitename'] . "</td>\n";
					echo "<td>";
					
					if ($member['status'] == 'N') {
						echo "New";
						
					} else if ($member['status'] == 'R') {
						echo "Rejected";
						
					} else if ($member['status'] == 'A') {
						echo "Verified";
						
					} else if ($member['status'] == 'S') {
						echo "Scheduled";
						
					} else if ($member['status'] == 'Q') {
						echo "QA'd";
						
					} else if ($member['status'] == 'I') {
						echo "CE Approved";
						
					} else if ($member['status'] == 'V') {
						echo "Archived";
					}
					
					echo "</td>\n";
					echo "<td>" . $member['createdate'] . "</td>\n";
					echo "<td>" . $member['requiredby'] . "</td>\n";
					echo "</tr>\n";
				}
			}
		?>
	</table>
</form>
<?php
	include("system-footer.php"); 
?>