<?php 
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove operatingSystem ?", "removeOperatingSystem");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<form class="entryform">
	<input required="true" type="text" id="newOperatingSystem" name="newOperatingSystem" value="" />
	
	<input type="button" style="display:inline" value="Add" onclick="if (verify()) call('addOperatingSystem', { pk1: $('#newOperatingSystem').val() })"></input>
</form>
<script>
	function verify() {
		var node = $("#newOperatingSystem");
		var returnValue = true;
		
		callAjax(
				"findopsystem.php", 
				{ 
					name: $("#newOperatingSystem").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Operating system already exists.");
						
						returnValue = false;
						
					} else {
						$(node).removeClass("invalid");
						$(node).next().css("visibility", "hidden");
						$(node).next().attr("title", "Required field.");
						
						returnValue = true;
					}
				},
				false
			);
			
		return returnValue;
	}
</script>

<table width="100%" class="grid list" id="operatingSystemlist" width=100% cellspacing=0 cellpadding=0>
<thead>
	<tr>
		<td>Operating System</td>
		<td width='20px'></td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	function addOperatingSystem() {
		$operatingSystem = $_POST['pk1'];
		
		$qry = "INSERT INTO ols_operatingsystem (name) VALUES ('$operatingSystem')";
		$result=mysql_query($qry);
	} 
	
	function removeOperatingSystem() {
		$id = $_POST['pk1'];
		
		$qry = "DELETE FROM ols_operatingsystem WHERE id = $id";
		$result=mysql_query($qry);

		if (! $result) {
			 logError('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT * FROM ols_operatingsystem ORDER BY name";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr><td>";
			echo $member['name'];
			echo "</td>";
			
			echo "<td title='Version Operating System'><img onclick='versionOperatingSystem(\"" . $member['id'] . "\")' src='images/version.png' /></td>";
			echo "<td title='Delete'><img onclick='deleteOperatingSystem(" . $member['id'] . ", \"" . $member['name'] . "\")' src='images/delete.png' /></td>"; 
			
			echo "</tr>";
		} 			
		
	} else {
		 logError('Invalid query: ' . mysql_error());

	}
?>
</table>
<script>
	var currentOperatingSystem = null;
	
	$(document).ready(function() {
		$("#versions").pickList({
				removeText: 'Remove Version',
				addText: 'Add Version',
				testMode: false
			});
		
		$("#versionDialog").dialog({
				autoOpen: false,
				modal: true,
				width: 800,
				title: "Versions",
				buttons: {
					Ok: function() {
						$("#versionsForm").submit();
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});
	});
	
function removeOperatingSystem() {
	call("removeOperatingSystem", {
		"pk1": currentOperatingSystem
	});
}

function deleteOperatingSystem(id, operatingSystem) {
	currentOperatingSystem = id;
	
	$("#confirmdialog .confirmdialogbody").html("You are about to remove operatingSystem <b><i>'"  + operatingSystem + "'</i></b>.<br>Are you sure ?");
	$("#confirmdialog").dialog("open");
}

function versionOperatingSystem(id) {
	window.location.href = "manageopsystemversion.php?id=" + id;
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
