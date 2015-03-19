<?php 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove route ?", "confirmCancellation");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<input type="text" id="newRoute" name="newRoute" value="" />
<input type="button" style="display:inline" value="Add" onclick="call('addRoute', { pk1: $('#newRoute').val() })"></input>
<div id="userDialog" class="modal">
	<form id="rolesForm" name="rolesForm" method="post">
		<input type="hidden" id="routerole" name="routerole" />
		<select class="listpicker" name="roles[]" multiple="true" id="roles" >
			<?php createComboOptions("roleid", "roleid", "jomon_roles", "", false); ?>
		</select>
	</form>
</div>

<table width="100%" class="grid">
<thead>
	<tr>
		<td>Route Name</td>
		<td width='20px'></td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	if (isset($_POST['routerole'])) {
		if (isset($_POST['roles'])) {
			$counter = count($_POST['roles']);

		} else {
			$counter = 0;
		}
		
		$routerole = mysql_escape_string($_POST['routerole']);
		$qry = "DELETE FROM jomon_roleroutes WHERE roleid = '$routerole'";
		$result = mysql_query($qry);
		
		if (! $result) {
			die(mysql_error());
		}

		for ($i = 0; $i < $counter; $i++) {
			$role = mysql_escape_string($_POST['roles'][$i]);
			
			$qry = "INSERT INTO jomon_roleroutes (routeid, roleid) VALUES ('$routerole', '$role')";
			$result=mysql_query($qry);
		};
	}
	
	function addRoute() {
		$role = mysql_escape_string($_POST['pk1']);
		
		$qry = "INSERT INTO jomon_cancellationroute (id) VALUES ('$role')";
		$result=mysql_query($qry);
		
		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
		
	} 
	
	function removeRoute() {
		$role = mysql_escape_string($_POST['pk1']);
		$qry = "DELETE FROM jomon_cancellationroute WHERE id = '$role'";
		$result=mysql_query($qry);

		if (! $result) {
			 die('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT * FROM jomon_cancellationroute";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr><td>";
			echo $member['id'];
			echo "<td title='Role Routes'><img onclick='userRoutes(\"" . htmlentities ( $member['id'], ENT_QUOTES) . "\")' src='images/user.png' /></td>";
			echo "<td title='Delete'><img onclick='deleteRoute(\"" . htmlentities ( $member['id'], ENT_QUOTES) . "\")' src='images/delete.png' /></td>";
			echo "</td></tr>";
		} 			
		
	} else {
		 die('Invalid query: ' . mysql_error());

	}
?>
</table>
<script>
	var currentRoute = null;
	
	function confirmCancellation() {
		call("removeRoute", {
				"pk1": currentRoute
			});
	}
	
	$(document).ready(function() {
		
		$("#roles").pickList({
				removeText: 'Remove Role',
				addText: 'Add Role',
				testMode: false
			});
			
		
		$("#userDialog").dialog({
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
function deleteRoute(route) {
	currentRoute = route;
	
	$("#confirmdialog .confirmdialogbody").html("You are about to remove the cancellation route <i>'" + currentRoute + "'</i>.<br>Are you sure ?");
	$("#confirmdialog").dialog("open");
}
function userRoutes(route) {
	getJSONData('findroleroutes.php?routeid=' + route, "#roles", function() {
		$("#routerole").val(route);
		$("#userDialog").dialog("open");
	});
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
