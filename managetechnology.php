<?php 
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove technology ?", "removeTechnology");
?>

<script src='js/jquery.picklists.js' type='text/javascript'></script>

<!--  Start of content -->
<form class="entryform">
	<input required="true" type="text" id="newTechnology" name="newTechnology" value="" />
	<input type="button" style="display:inline" value="Add" onclick="if (verify()) call('addTechnology', { pk1: $('#newTechnology').val() })"></input>
</form>
<script>
	function verify() {
		var node = $("#newTechnology");
		var returnValue = true;
		
		callAjax(
				"findtechnology.php", 
				{ 
					name: $("#newTechnology").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Technology already exists.");
						
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
		<td>Technology</td>
		<td width='20px'></td>
		<td width='20px'></td>
		<td width='20px'></td>
	</tr>
</thead>
<?php
	
	function addTechnology() {
		$technology = $_POST['pk1'];
		
		if (isset($_GET['id'])) {
			$qry = "INSERT INTO ols_technology (name, parentid) VALUES ('$technology', " . $_GET['id'] . ")";
		
		} else {
			$qry = "INSERT INTO ols_technology (name) VALUES ('$technology')";
		}
		
		$result=mysql_query($qry);
		
	} 
	
	function removeTechnology() {
		$id = $_POST['pk1'];
		
		$qry = "DELETE FROM ols_technology WHERE id = $id";
		$result = mysql_query($qry);

		if (! $result) {
			 logError('Invalid query: ' . mysql_error());
		}
	}
			
	if (isset($_GET['id'])) {
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

		$qry = "SELECT A.*, " .
				"(SELECT COUNT(*) FROM ols_technologyversion B WHERE B.technologyid = A.id) AS versioncount, " .
				"(SELECT COUNT(*) FROM ols_technology C WHERE C.parentid = A.id) AS subcount " .
				"FROM " .
				"ols_technology A " .
				"WHERE A.parentid = " . $_GET['id'] . " " .
				"ORDER BY A.name";
		
		echo "<tr>";
		echo "<td>";
		echo "<img src='images/up.png' onclick='window.location.href=\"$parent\";' />";
		echo "</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		
	} else {
		$qry = "SELECT A.*, " .
				"(SELECT COUNT(*) FROM ols_technologyversion B WHERE B.technologyid = A.id) AS versioncount, " .
				"(SELECT COUNT(*) FROM ols_technology C WHERE C.parentid = A.id) AS subcount " .
				"FROM " .
				"ols_technology A " .
				"WHERE A.parentid IS NULL " .
				"ORDER BY A.name";
	}
			
	$result=mysql_query($qry);
	$rowNumber = 0;
	
	//Check whether the query was successful or not
	if($result) {
		while($member = mysql_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>";
			echo $member['name'];
			echo "</td>";
			
			if ($member['subcount'] > 0 || $member['versioncount'] == 0) {
				echo "<td title='Sub Technology'><img onclick='subTechnology(\"" . $member['id'] . "\")' src='images/sub.png' /></td>";

			} else {
				echo "<td>&nbsp;</td>";
			}
			
			if ($member['versioncount'] > 0 || $member['subcount'] == 0) {
				echo "<td title='Version Technology'><img onclick='versionTechnology(\"" . $member['id'] . "\")' src='images/version.png' /></td>";
				
			} else {
				echo "<td>&nbsp;</td>";
			}
			
			if ($member['versioncount'] == 0 && $member['subcount'] == 0) {
				echo "<td title='Delete'><img onclick='deleteTechnology(" . $member['id'] . ", \"" . $member['name'] . "\")' src='images/delete.png' /></td>";
				
			} else {
				echo "<td>&nbsp;</td>";
			}
			
			echo "</tr>";
		} 			
		
	} else {
		 logError('Invalid query: ' . mysql_error());

	}
?>
</table>
<script>
	var currentTechnology = null;
	
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
	
function removeTechnology() {
	call("removeTechnology", {
		"pk1": currentTechnology
	});
}

function deleteTechnology(id, technology) {
	currentTechnology = id;
	
	$("#confirmdialog .confirmdialogbody").html("You are about to remove technology <b><i>'"  + technology + "'</i></b>.<br>Are you sure ?");
	$("#confirmdialog").dialog("open");
}

function versionTechnology(id) {
	window.location.href = "managetechnologyversion.php?id=" + id;
}

function subTechnology(id) {
	window.location.href = "managetechnology.php?id=" + id;
}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
