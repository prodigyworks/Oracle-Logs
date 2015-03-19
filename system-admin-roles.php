<?php 
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove role ?", "removeRole");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<input type="text" id="newRole" name="newRole" value="" />
<input type="button" style="display:inline" value="Add" onclick="call('addRole', { pk1: $('#newRole').val() })"></input>
<div id="userDialog" class="modal">
	<form id="usersForm" name="usersForm" method="post">
		<input type="hidden" id="userrole" name="userrole" />
		<select class="listpicker" name="users[]" multiple="true" id="users" >
			<?php createComboOptions("member_id", "login", "ols_members", "", false); ?>
		</select>
	</form>
</div>

<table width="100%" class="grid list" id="rolelist" width=100% cellspacing=0 cellpadding=0>
<thead>
	<tr>
		<td>Role</td>
		<td width='20px'></td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	
	if (isset($_POST['userrole'])) {
		if (isset($_POST['users'])) {
			$counter = count($_POST['users']);

		} else {
			$counter = 0;
		}
		
		$role = $_POST['userrole'];
		$qry = "DELETE FROM ols_userroles WHERE roleid = '$role'";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError(mysql_error());
		}

		for ($i = 0; $i < $counter; $i++) {
			$memberid = $_POST['users'][$i];
			
			$qry = "INSERT INTO ols_userroles (roleid, memberid) VALUES ('$role', $memberid)";
			$result=mysql_query($qry);
		};
	}
	
	function addRole() {
		$role = $_POST['pk1'];
		
		$qry = "INSERT INTO ols_roles (roleid) VALUES ('$role')";
		$result=mysql_query($qry);
		
		if (! $result) {
			 logError('Invalid query: ' . mysql_error());

		}
		
	} 
	
	function removeRole() {
		$role = $_POST['pk1'];
		
		$qry = "DELETE FROM ols_roles WHERE roleid = '$role'";
		$result=mysql_query($qry);

		if (! $result) {
			 logError('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT * FROM ols_roles ORDER BY roleid";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr><td>";
			echo $member['roleid'];
			echo "</td>";
			
			if ($member['systemrole'] == "Y") {
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				
			} else {
				echo "<td title='User Roles'><img onclick='userRoles(\"" . $member['roleid'] . "\")' src='images/user.png' /></td>";
				echo "<td title='Delete'><img onclick='deleteRole(\"" . $member['roleid'] . "\")' src='images/delete.png' /></td>"; 
			}
			
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
		$("#users").pickList({
				removeText: 'Remove User',
				addText: 'Add User',
				testMode: false
			});
		
		$("#userDialog").dialog({
				autoOpen: false,
				modal: true,
				width: 800,
				title: "Users",
				buttons: {
					Ok: function() {
						$("#usersForm").submit();
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
	});
	
function removeRole() {
	call("removeRole", {
		"pk1": currentRole
	});
}
function deleteRole(role) {
	currentRole = role;
	$("#confirmdialog .confirmdialogbody").html("You are about to remove role <b><i>'"  + role + "'</i></b>.<br>Are you sure ?");
	$("#confirmdialog").dialog("open");
}
function userRoles(role) {
	getJSONData('finduserroles.php?roleid=' + role, "#users", function() {
		$("#userrole").val(role);
		$("#userDialog").dialog("open");
	});
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
