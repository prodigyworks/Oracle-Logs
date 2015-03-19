<?php
	include("system-header.php"); 
?>
<script>
	function showFilter() {
		$("#userFilter").hide();
		$("#siteFilter").hide();
		$("#site").val("0");
		$("#customerFilter").hide();
		$("#ccfFilter").hide();
		$("#user").val("0");
		
		if ($("#filter").find('option:selected').val() == "1") {
			$("#userFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "2") {
			$("#siteFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "3") {
			$("#customerFilter").show();
		}
		
		if ($("#filter").find('option:selected').val() == "4") {
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
		<option value=3>Customer</option>
		<option value=4>CCF</option>
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
		<label>CCF</label>
		<input type="text" value="" id="ccf" name="ccf" style="width:260px" />
		
		<br>
		<input type="submit" value="go" />
	</div>
	<table class='grid list' id="listallquotes" width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td>Job</td>
				<td>Customer</td>
				<td>Cost Code</td>
				<td>Owner</td>
				<td>Site</td>
				<td>Cancelled By</td>
				<td>Date Cancelled</td>
			</tr>
		</thead>
		<?php
			$qry = "SELECT A.*, DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%M:%S') AS createdate, " .
					"DATE_FORMAT(A.cancelleddate, '%d/%m/%Y %H:%m:%S') AS cancelleddate, " .
					"B.firstname, B.lastname, C.name AS sitename, " .
					"D.firstname AS cfirstname, D.lastname AS clastname ". 
					"FROM jomon_quoteheader A " .
					"INNER JOIN jomon_members B " .
					"ON B.member_id = A.createdby " .
					"INNER JOIN jomon_sites C " .
					"ON C.id = A.siteid " .
					"INNER JOIN jomon_members D " .
					"ON D.member_id = A.cancelledby " .
					"WHERE A.status IN ('P') ";
					
			if (isset($_POST['site']) && $_POST['site'] != "0") {
				$siteid = $_POST['site'];
				$qry = $qry . " AND C.id = $siteid ";
			}
					
			if (isset($_POST['user']) && $_POST['user'] != "0") {
				$memberid = $_POST['user'];
				$qry = $qry . " AND B.member_id = $memberid ";
			}
					
			if (isset($_POST['ccf']) && $_POST['ccf'] != "") {
				$ccf = $_POST['ccf'];
				$qry = $qry . " AND A.ccf LIKE '$ccf%' ";
			}
					
			if (isset($_POST['customer']) && $_POST['customer'] != "") {
				$customer = $_POST['customer'];
				$qry = $qry . " AND A.customer LIKE '$customer%' ";
			}
					
			$qry = $qry . " ORDER BY A.id";
			$result = mysql_query($qry);
			
			if (! $result) die("Error: " . mysql_error());
			
			//Check whether the query was successful or not
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<tr>\n";
					echo "<td><a href='viewquote.php?id=" . $member['id'] . "'>" . $member['prefix'] . sprintf("%04d", $member['id']) . "</a></td>\n";
					echo "<td>" . $member['customer'] . "</td>\n";
					echo "<td>" . GetCostCode($member['costcode'])  . "</td>\n";
					echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					echo "<td>" . $member['sitename'] . "</td>\n";
					echo "<td>" . $member['cfirstname'] . " " . $member['clastname'] . "</td>\n";
					echo "<td>" . $member['cancelleddate'] . "</td>\n";
					echo "</tr>\n";
				}
			}
		?>
	</table>
</form>
<?php
	include("system-footer.php"); 
?>