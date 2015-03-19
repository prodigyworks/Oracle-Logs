<?php
	require_once("system-header.php"); 
	require_once("tinymce.php");
	require_once("confirmdialog.php"); 
	
	createConfirmDialog("removedialog", "Remove contact ?", "removeContact");
	
	function removeContact() {
		$id = $_POST['pk1'];
		
		$qry = "DELETE FROM ols_contacts WHERE id = $id";
		$result = mysql_query($qry);
	
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
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
	function edit(id, firstname, lastname, email, telephone) {
		$("#contactid").val(id);
		$("#firstname").val(firstname);
		$("#lastname").val(lastname);
		$("#email").val(email);
		$("#telephone").val(telephone);
		$("#contactdialog").dialog("open");
	}
	
	function addContact() {
		$("#contactid").val("");
		$("#contactdialog").dialog("open");
	}
	
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

		$("#contactdialog").dialog({
				modal: true,
				autoOpen: false,
				width: 700,
				show:"fade",
				hide:"fade",
				title:"Contact",
				open: function(event, ui){
					
				},
				buttons: {
					Ok: function() {
						$(this).dialog("close");
						
						$("#contactpanel").appendTo("#dummypanel");
						$("#contactform").submit();
					},
					Cancel: function() {
						$(this).dialog("close");
					}
				}
			});

	});
	
	var contactid = 0;
	
	function remove(id) {
		contactid = id;
		
		$("#removedialog .confirmdialogbody").html("You are about to remove this contact.<br>Are you sure ?");
		$("#removedialog").dialog("open");
	}
	
	function removeContact() {
		call("removeContact", {pk1: contactid});
	}
</script>

<form id="contactform" action="addcontact.php" method="POST">
	<input type="hidden" id="contactid" name="contactid" value="" />
	
	<div id="dummypanel" style="display:none"></div>
</form>

<div class="modal" id="contactdialog">
	<div id="contactpanel">
		<table width='100%' cellspacing=5>
			<tr>
				<td>First Name</td>
				<td><input class="textfield60" type="text" id="firstname" name="firstname" /></td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td><input class="textfield60" type="text" id="lastname" name="lastname" /></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input class="textfield90" type="text" id="email" name="email" /></td>
			</tr>
			<tr>
				<td>Telephone</td>
				<td><input class="textfield20" type="text" id="telephone" name="telephone" /></td>
			</tr>
		</table>
	</div>
</div>
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
<form id="deployform" method="POST" action="bulkalertsave.php">
	<input type="hidden" id="deploynotes" name="deploynotes" />
	<input type="hidden" id="deploytitle" name="deploytitle" />
   	<span class="wrapper"><a class='link1 rgap5' href="javascript:addContact()"><em><b>Add Contact</b></em></a></span>
   	<span class="wrapper"><a class='link1' href="javascript:deployAlert()"><em><b>Create Alert</b></em></a></span>
	<br>
	<br>
	
	<table cellspacing=0 cellpadding=0 width='100%' class='grid list' id="articletable">
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td>Last Name</td>
	        <td>First Name</td>
	        <td>Email</td>
	        <td>Telephone</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$qry = "SELECT * FROM ols_contacts A " .
	    				"ORDER BY A.lastname, A.firstname";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						echo "<tr>";
						echo "<td><input type='checkbox' id='checked' name='checked[]' checked value='" .$member['id'] . "' /></td>";
						echo "<td>" . $member['lastname'] . "</td>";
						echo "<td>" . $member['firstname'] . "</td>";
						echo "<td>" . $member['email'] . "</td>";
						echo "<td>" . $member['telephone'] . "</td>";
						
						echo "<td width='20px'><a href='javascript: edit(" . $member['id'] . ", \"" . $member['firstname'] . "\", \"" . $member['lastname'] . "\", \"" . $member['email'] . "\", \"" . $member['telephone'] . "\")'><img src='images/edit.png'  title='Edit' /></a></td>";
						echo "<td width='20px'><a href='javascript: remove(" . $member['id'] . ")'><img src='images/delete.png'  title='Removen' /></a></td>";
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