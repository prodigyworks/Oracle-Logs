<?php
	include("system-header.php"); 
	include("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove Throw Away Quote ?", "removeThrowAwayQuote");
	
	function removeQuote() {
		$header = new QuotationHeader();
		$header->loadThrowAway($_POST['pk1']);
		$header->deleteThrowAwayQuote();
	}
?>
<script>
	var selectedID = null;
	
	function removeQuote(id, name) {
		selectedID = id;
		
		$("#confirmdialog .confirmdialogbody").html("You are about to remove the throw away quote " + name + ".<br>Are you sure ?");
		$("#confirmdialog").dialog("open");
	}
	
	function removeThrowAwayQuote() {
		call("removeQuote", {pk1: selectedID});
	}
	
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
	<table class='grid list' id="listallquotes" maxrows=18 width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td>Quote Number</td>
				<td>Customer</td>
				<td>Cost Code</td>
				<td>Site</td>
				<td>Cancelled By</td>
				<td>Date Cancelled</td>
				<td width='24px'>&nbsp</td>
			</tr>
		</thead>

		<?php
			$qry = "SELECT A.*, DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%M:%S') AS createdate, " .
					"B.firstname, B.lastname, C.name AS sitename " .
					"FROM jomon_cancelledquoteheader A " .
					"INNER JOIN jomon_members B " .
					"ON B.member_id = A.createdby " .
					"INNER JOIN jomon_sites C " .
					"ON C.id = A.siteid " .
					"WHERE A.createdby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"AND (A.throwaway = 'Y' OR A.throwaway = '1') ";
					
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
					echo "<td><a href='viewthrowawayquote.php?id=" . $member['id'] . "'>" . $member['prefix'] . sprintf("%04d", $member['id']) . "</a></td>\n";
					echo "<td>" . $member['customer'] . "</td>\n";
					echo "<td>" . GetCostCode($member['costcode'])  . "</td>\n";
					echo "<td>" . $member['sitename'] . "</td>\n";
					echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					echo "<td>" . $member['createddate'] . "</td>\n";
					echo "<td><img title='Remove Throw Away Quote' src='images/delete.png' onclick='removeQuote(" . $member['id'] . ", \"" . $member['prefix'] . sprintf("%04d", $member['id']) . "\")' /></td>\n";
					echo "</tr>\n";
				}
			}
		?>
	</table>
</form>
<?php
	include("system-footer.php"); 
?>