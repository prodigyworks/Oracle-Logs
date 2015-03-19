<?php 
	require_once("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove technology ?", "removeVersion");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<form class="entryform">
	<input required="true" type="text" id="newVersion" name="newVersion" value="" />
	<input type="button" style="display:inline" value="Add" onclick="if (verify()) call('addVersion', { pk1: $('#newVersion').val() })"></input>
</form>
<script>
	function verify() {
		var node = $("#newVersion");
		var returnValue = true;
		
		callAjax(
				"findtechnologyversion.php", 
				{ 
					name: $("#newVersion").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Technology version already exists.");
						
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

<table width="100%" class="grid list" id="technologylist" width=100% cellspacing=0 cellpadding=0>
<thead>
	<tr>
		<td>Version</td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	
	function addVersion() {
		$technologyid = $_GET['id'];
		$technology = $_POST['pk1'];
		
		$qry = "INSERT INTO ols_technologyversion (technologyid, name) VALUES ($technologyid, '$technology')";
		$result=mysql_query($qry);
		
	} 
	
	function removeVersion() {
		$id = $_POST['pk1'];
		
		$qry = "DELETE FROM ols_technologyversion WHERE id = $id";
		$result=mysql_query($qry);

		if (! $result) {
			 logError('Invalid query: ' . mysql_error());

		}
	}
	
	$qry = "SELECT parentid " .
			"FROM ols_technology " .
			"WHERE id = " . $_GET['id'];

	$parent = "managetechnology.php";
	$result = mysql_query($qry);

	if ($result) {
		while($member = mysql_fetch_assoc($result)) {
			if ($member['parentid'] != null) {
				$parent = "managetechnology.php?id=" . $member['parentid'];
			}
		}

	} else {
		logError($qry . " = " . mysql_error());
	}

	echo "<tr>";
	echo "<td>";
	echo "<img src='images/up.png' onclick='window.location.href=\"$parent\";' />";
	echo "</td>";
	echo "<td>&nbsp;</td>";
	
	
	$technologyid = $_GET['id'];
	$qry = "SELECT * FROM ols_technologyversion " .
			"WHERE technologyid = $technologyid " .
			"ORDER BY name";
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr><td>";
			echo $member['name'];
			echo "</td>";
			
			echo "<td title='Delete'><img onclick='deleteVersion(" . $member['id'] . ", \"" . $member['name'] . "\")' src='images/delete.png' /></td>"; 
			
			echo "</tr>";
		} 			
		
	} else {
		 logError('Invalid query: ' . mysql_error());
	}
?>
</table>
<script>
	var currentVersion = null;
	var currentVersionName = null;
	
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
	
function removeVersion() {
	call("removeVersion", {
		"pk1": currentVersion
	});
}

function deleteVersion(technology, name) {
	currentVersion = technology;
	currentVersionName = name;
	
	$("#confirmdialog .confirmdialogbody").html("You are about to remove technology <b><i>'"  + name + "'</i></b>.<br>Are you sure ?");
	$("#confirmdialog").dialog("open");
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
