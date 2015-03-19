<?php
	require_once("system-header.php"); 
	require_once("tinymce.php");
	
	function deploy() {
		$articled = $_POST['pk1'];
		$notes = mysql_escape_string($_POST['pk2']);
		
		$qry = "UPDATE ols_article SET " .
				"reasonforcancellation = '$notes', " .
				"published = 'X', " .
				"cancelleddate = NOW() " .
				"WHERE id = $articled";
		$result = mysql_query($qry); 
		
		if (! $result) {
			die ($qry . " = " . mysql_error());
		}
	}
?>
<script type="text/javascript">
	function deployAlert() {
		$("#deployalert").dialog("open");
	}

	$(document).ready(function() {
		$("#deployalert").dialog({
				modal: true,
				autoOpen: false,
				width: 700,
				show:"fade",
				hide:"fade",
				title:"Alert",
				open: function(event, ui){
					$("#alertnotes").focus();
				},
				buttons: {
					Ok: function() {
						tinyMCE.triggerSave();
						
						if ($("#alertnotes").val() == "") {
							alert("Please enter text");
							return;
						}
						$("#deploynotes").val($("#alertnotes").val());
						$("#deploytitle").val($("#alerttitle").val());
						$("#deployform").submit();
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});

	});
</script>
<div class="modal" id="deployalert">
	<label>Title</label>
	<br />
	<input type="text" id="alerttitle" name="alerttitle" class="textfield90"></input>
	<br />
	<br />
	
	<label>Text</label>
	<br />
	<textarea id="alertnotes" name="alertnotes" cols=152 rows=10 class="tinyMCE" style="height:340px;width: 340px"></textarea>
</div>
<form id="deployform" method="POST" action="sitealertsave.php">
	<input type="hidden" id="deploynotes" name="deploynotes" />
	<input type="hidden" id="deploytitle" name="deploytitle" />
   	<span class="wrapper"><a class='link1' href="javascript:deployAlert()"><em><b>Create Alert</b></em></a></span>
	<br>
	<br>
	
	<table cellspacing=0 cellpadding=0 width='100%' class='grid list' id="articletable">
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td>Last Name</td>
	        <td>First Name</td>
	        <td>Login</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$qry = "SELECT * FROM ols_members A " .
	    				"WHERE A.sitealerts = 1 " .
	    				"ORDER BY A.lastname, A.firstname";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						echo "<tr>";
						echo "<td><input type='checkbox' id='checked' name='checked[]' checked value='" .$member['member_id'] . "' /></td>";
						echo "<td>" . $member['lastname'] . "</td>";
						echo "<td>" . $member['firstname'] . "</td>";
						echo "<td>" . $member['login'] . "</td>";
						echo "</tr>";
					}
				} else {
					logError($qry . " = " . mysql_error());
				}
	    	?>
	    </tbody>
	</table>
</form>
<?php
	include("system-footer.php"); 
?>