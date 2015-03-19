<?php
function deleteUser() {
	$id = $_POST['pk1'];
	
	$qry = "DELETE FROM ols_members WHERE member_id = $id";
	$result = mysql_query($qry);
}

function upgrade() {
	$id = $_POST['pk1'];
	
	$qry = "UPDATE ols_members SET accepted = 'Y', paypalprofileid = null WHERE member_id = $id";
	$result = mysql_query($qry);
	
	$qry = "INSERT INTO ols_userroles (roleid, memberid) VALUES ('PREMIUM', $id)";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " - " . mysql_errno());
	}
	
	sendRoleMessage(
			"ADMIN",
			"Package upgrade",
			"<h4>Subscription accepted for member " . GetUserName($id) . "$id</h4>"
		);
	
	sendUserMessage(
			$id,
			"Package upgrade",
			"<h4>Your subscription has been accepted</h4>"
		);
}

function downgrade() {
	$id = $_POST['pk1'];
	$reason = $_POST['pk2'];
	
	$qry = "DELETE FROM ols_userroles " .
			"WHERE roleid = 'PREMIUM' " .
			"AND memberid = $id";
	$result = mysql_query($qry);
	
	sendRoleMessage(
			"ADMIN",
			"Account subscription",
			"<h4>Subscription has been cancelled for member " . GetUserName($id) . "</h4><p>Reason:</p>" . $reason
		);

	sendUserMessage(
			$id,
			"Account subscription",
			"<h4>Your subscription has been cancelled</h4><p>Reason:</p>" . $reason
		);
}

function resetPassword() {
	$id = $_POST['pk1'];
	$pwd = md5($_POST['pk2']);
	
	$qry = "UPDATE ols_members SET passwd = '$pwd' WHERE member_id = $id";
	$result = mysql_query($qry);
	
	sendUserMessage(
			$id,
			"Password reset",
			"<h1>You password has been reset to <i>" . $_POST['pk2'] . "</i>"
		);
}

function usertemplate($type) { 
	require_once("system-header.php");
	require_once("tinymce.php");
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove item ?", "deleteUser");
	createConfirmDialog("resetdialog", "Reset password ?", "resetPassword");
	createConfirmDialog("upgradedialog", "Upgrade ?", "upgradeUser");
	createConfirmDialog("downgradedialog", "Downgrade ?", "downgradeReason");
	
	$viewer = "profile.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
?>

<!--  Start of content -->
	<div id="reasondialog" class="modal">
		<label>Reason</label>
		<textarea id="reason" name="reason" class="tinyMCE" style='width:770px; height: 300px'></textarea>
	</div>
	
	<table width="100%" class="grid list" id="userlist" maxrows=20 width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td>First Name</td>
				<td>Last Name</td>
				<td>Login</td>
				<td>Image</td>
				<td width='16px'></td>
				<td width='16px'></td>
				<td width='16px'></td>
				<td width='16px'></td>
			</tr>
		</thead>
		<?php
			$qry = "";
			
			if ($type != "") {
				$roleid = $type;
				$qry = "SELECT DISTINCT A.*, " .
	    			   "(SELECT COUNT(*) FROM ols_userroles X WHERE X.memberid = A.member_id AND X.roleid = 'PREMIUM') AS premiumcount  " .
					   "FROM ols_members A " .
					   "INNER JOIN ols_userroles B " .
					   "ON B.memberid = A.member_id " .
					   "WHERE B.roleid = '$roleid' " .
					   "ORDER by A.firstname, A.lastname";
				
			} else {
				$qry = "SELECT * FROM ols_members A, " .
	    				"(SELECT COUNT(*) FROM ols_userroles X WHERE X.memberid = A.member_id AND X.roleid = 'PREMIUM') AS premiumcount  " .
					   "ORDER by A.firstname, A.lastname";
			}
			
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<tr>\n";
					echo "<td>" . $member['firstname'] . "</td>\n";
					echo "<td>" . $member['lastname'] . "</td>\n";
					echo "<td>" . $member['login'] . "</td>\n";
					
					if ($member['imageid'] != null && $member['imageid'] != 0) {
						echo "<td title='Image'><img height=20 src='system-imageviewer.php?id=" . $member['imageid'] . "' /></td>\n";
					
					} else {
						echo "<td>&nbsp;</td>\n";
					}
					
					echo "<td width='16px' title='Edit'><img src='images/edit.png' onclick='window.location.href = \"$viewer&id=" . $member['member_id'] . "\";' /></td>\n";
					
					if ($member['systemuser'] == "Y") {
						echo "<td>&nbsp;</td>\n";
						
					} else {
						echo "<td width='16px' title='Delete'><img src='images/delete.png' onclick='removeUser(" . $member['member_id'] . ", \"" . $member['firstname'] . " " . $member['lastname'] . "\")' /></td>\n";
					}
					
					echo "<td width='16px' title='Reset password'><img src='images/password.png' onclick='$(\"#user\").val(\"" . $member['member_id'] . "\"); $(\"#passwordDialog\").dialog(\"open\");' /></td>\n";
					
					if ($member['premiumcount'] > 0) {
						echo "<td width='16px' title='Downgrade'><img src='images/downgrade.png' onclick='downgrade(" . $member['member_id'] . ", \"" . $member['login']  . "\");' /></td>\n";
						
					} else {
						echo "<td width='16px' title='Upgrade'><img src='images/upgrade.png' onclick='upgrade(" . $member['member_id'] . ", \"" . $member['login']  . "\");' /></td>\n";
					}

					echo "</tr>\n";
				}
				
			} else {
				logError($qry . " = " . mysql_error());
			}
		?>
	</table>
	<div id="passwordDialog" class="modal">
		<label>New password</label>
		<input type="hidden" id="user" name="user" />
		<input type="text" id="password" name="password" />
	</div>
	<script>
		var currentUser = null;
		
		function upgradeUser() {
			call("upgrade", {pk1: currentUser });			
		}
		
		function downgradeUser() {
			call("downgrade", {pk1: currentUser, pk2: $("#reason").val() });			
		}
		
		function deleteUser() {
			call("deleteUser", {pk1: currentUser });			
		}
		
		function resetPassword() {
			call("resetPassword", { 
					pk1: $("#user").val(),
					pk2: $("#password").val() 
				});
		}
		
		function downgradeReason() {
			$("#downgradedialog").dialog("close");
			$("#reasondialog").dialog("open");
		}
		
		function downgrade(userID, name) {
			currentUser = userID;
			
			$("#downgradedialog .confirmdialogbody").html("You are about to downgrade user <b><i>'"  + name + "'</i></b>.<br>Are you sure ?");
			$("#downgradedialog").dialog("open");
		}
		
		function upgrade(userID, name) {
			currentUser = userID;
			
			$("#upgradedialog .confirmdialogbody").html("You are about to upgrade user <b><i>'"  + name + "'</i></b>.<br>Are you sure ?");
			$("#upgradedialog").dialog("open");
		}
		
		function removeUser(userID, name) {
			currentUser = userID;
			
			$("#confirmdialog .confirmdialogbody").html("You are about to remove user <b><i>'"  + name + "'</i></b>.<br>Are you sure ?");
			$("#confirmdialog").dialog("open");
		}
		
		$(document).ready(function() {
				$("#passwordDialog").dialog({
						modal: true,
						autoOpen: false,
						title: "Reset password",
						buttons: {
							Ok: function() {
								$("#resetdialog .confirmdialogbody").html("You are about to reset the password for this user.<br>Are you sure ?");
								$("#resetdialog").dialog("open");
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
					
				$("#reasondialog").dialog({
						modal: true,
						autoOpen: false,
						title: "Reason for downgrade",
						width: 800,
						height: 420,
						buttons: {
							Ok: function() {
								tinyMCE.triggerSave();
								
								downgradeUser();
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
			});
		
	</script>
<!--  End of content -->
<?php 
	require_once("system-footer.php"); 
}
?>
		