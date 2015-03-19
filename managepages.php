<?php 
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove role ?", "removeRole");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<input type="text" id="newRole" name="newRole" value="" />
<input type="button" style="display:inline" value="Add" onclick="call('addRole', { pk1: $('#newRole').val() })"></input>
<div id="roleDialog" class="modal">
	<form id="rolesForm" name="rolesForm" method="post">
		<input type="hidden" id="pageid" name="pageid" />
		<select class="listpicker" name="roles[]" multiple="true" id="roles" >
			<?php createComboOptions("roleid", "roleid", "ols_roles", "", false); ?>
		</select>
	</form>
</div>

<table width="100%" class="grid list" id="rolelist" width=100% cellspacing=0 cellpadding=0>
<thead>
	<tr>
		<td>Page</td>
		<td>Application</td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	
	if (isset($_POST['pageid'])) {
		if (isset($_POST['roles'])) {
			$counter = count($_POST['roles']);

		} else {
			$counter = 0;
		}
		
		$pageid = $_POST['pageid'];
		$qry = "DELETE FROM ols_pageroles WHERE pageid = $pageid";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError(mysql_error());
		}

		for ($i = 0; $i < $counter; $i++) {
			$roleid = $_POST['roles'][$i];
			
			$qry = "INSERT INTO ols_pageroles (pageid, roleid) VALUES ($pageid, '$roleid')";
			$result = mysql_query($qry);
		};
	}
	
	$qry = "SELECT * FROM ols_pages ORDER BY label";
			
	$result = mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr>";
			
			echo "<td>";
			echo $member['label'];
			echo "</td>";
			echo "<td>";
			echo $member['pagename'];
			echo "</td>";
			
			echo "<td title='User Roles'><img onclick='pageRoles(\"" . $member['pageid'] . "\")' src='images/user.png' /></td>";
			
			echo "</tr>";
		} 			
		
	} else {
		 logError('Invalid query: ' . mysql_error());

	}
?>
</table>
<script>
	var currentRole = null;
	
	$(document).ready(function() {
		$("#roles").pickList({
				removeText: 'Remove Role',
				addText: 'Add Role',
				testMode: false
			});
		
		$("#roleDialog").dialog({
				autoOpen: false,
				modal: true,
				width: 800,
				title: "Roles",
				buttons: {
					Ok: function() {
						$("#rolesForm").submit();
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
	});
	
function pageRoles(pageid) {
	getJSONData('findpageroles.php?pageid=' + pageid, "#roles", function() {
		$("#pageid").val(pageid);
		$("#roleDialog").dialog("open");
	});
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
