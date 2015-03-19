<?php 
	include("system-header.php");
	include("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove item ?", "deleteSite");
	createConfirmDialog("resetdialog", "Reset password ?", "resetPassword");
?>

<!--  Start of content -->
	<?php
		function deleteSite() {
			$id = $_POST['pk1'];
			
			$qry = "DELETE FROM jomon_sites WHERE member_id = $id";
			$result = mysql_query($qry);
		}
	?>
	<table width="100%" class="grid list" id="sitelist" maxrows=20 width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td>Name</td>
				<td>Street</td>
				<td>Town</td>
				<td>City</td>
				<td>County</td>
				<td>Post code</td>
				<td>Tel</td>
				<td>Fax</td>
				<td width='16px'></td>
				<td width='16px'></td>
			</tr>
		</thead>
		<?php
			$memberid = $_SESSION['SESS_MEMBER_ID'];
			$qry = "SELECT * FROM jomon_sites where memberid = $memberid ORDER by name";
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<tr>\n";
					echo "<td>" . $member['name'] . "</td>\n";
					echo "<td>" . $member['address1'] . "</td>\n";
					echo "<td>" . $member['address2'] . "</td>\n";
					echo "<td>" . $member['address3'] . "</td>\n";
					echo "<td>" . $member['address4'] . "</td>\n";
					echo "<td>" . $member['postcode'] . "</td>\n";
					echo "<td>" . $member['telephone'] . "</td>\n";
					echo "<td>" . $member['fax'] . "</td>\n";
					
					echo "<td width='16px' title='Edit'><img src='images/edit.png' /></td>\n";
					echo "<td width='16px' title='Delete'><img src='images/delete.png' onclick='removeSite(" . $member['id'] . ", \"" . $member['name'] . "\")' /></td>\n";
					echo "</tr>\n";
				}
			}
		?>
	</table>
	<script>
		var currentSite = null;
		
		function deleteSite() {
			call("deleteSite", {pk1: currentSite });			
		}
		
		function resetPassword() {
			call("resetPassword", { 
					pk1: $("#site").val(),
					pk2: $("#password").val() 
				});
		}
		
		function removeSite(siteID, name) {
			currentSite = siteID;
			
			$("#confirmdialog .confirmdialogbody").html("You are about to remove site <b><i>'"  + name + "'</i></b>.<br>Are you sure ?");
			$("#confirmdialog").dialog("open");
		}
		
		
	</script>
<!--  End of content -->
<?php include("system-footer.php") ?>
		