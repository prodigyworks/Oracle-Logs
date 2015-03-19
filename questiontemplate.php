<?php

function withdrawQuestion() {
	$id = $_POST['pk1'];
	$reasonforcancellation = $_POST['pk2'];
	$qry = "SELECT title, memberid FROM ols_question WHERE id = $id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$title = $member['title'];
			sendRoleMessage(
					"ADMIN",
					"Question withdrawal",
					"<h4>Question $title has been withdrawn from publication</h4><p>Reason:</p>" . $reasonforcancellation
				);
			
			sendUserMessage(
					$member['memberid'],
					"Question withdrawal",
					"<h4>Question $title has been withdrawn from publication</h4><p>Reason:</p>" . $reasonforcancellation
				);
		}
	}
	
	$reasonforcancellation = mysql_escape_string($reasonforcancellation);
	$qry = "UPDATE ols_question SET " .
			"published = 'W', " .
			"cancelleddate = NOW(), " .
			"reasonforcancellation = '$reasonforcancellation' " .
			"WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}

function repostQuestion() {
	$id = $_POST['pk1'];
	$qry = "SELECT title, memberid FROM ols_question WHERE id = $id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$title = $member['title'];
			sendRoleMessage("CONSULTANT", "Verification required", "Verification required for question " . $title);
			sendUserMessage($member['memberid'], "Verification requested", "Verification requested for question " . $title);
		}
	}
	
	$qry = "UPDATE ols_question SET " .
			"published = 'N' " .
			"WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}

function removeQuestion() {
	$id = $_POST['pk1'];
	
	$qry = "SELECT title, memberid FROM ols_question WHERE id = $id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$title = $member['title'];
			sendRoleMessage(
					"ADMIN",
					"Question removal",
					"<h4>Question $title has been removed</h4>"
				);
			
			sendUserMessage(
					$member['memberid'],
					"Question removal",
					"<h4>Question $title has been removed</h4>"
				);
		}
	}
	
	$qry = "DELETE FROM ols_questiondocuments WHERE questionid = $id";
	$result = mysql_query($qry);

	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "DELETE FROM ols_questionanswers WHERE questionid = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "DELETE FROM ols_question WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}

function question($where) {
	require_once("system-header.php"); 
	require_once("confirmdialog.php"); 
	require_once("tinymce.php"); 
	
	createConfirmDialog("removedialog", "Remove question ?", "deleteQuestion");
	createConfirmDialog("withdrawdialog", "Withdraw question ?", "withdrawReason");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "viewquestion.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
?>
<div id="reasondialog" class="modal">
	<label>Reason</label>
	<textarea id="reason" name="reason" class="tinyMCE" style='width:770px; height: 300px'></textarea>
</div>

<div class='questions'>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='80px'>Date</td>
	        <td>Title</td>
	        <td width='120px'>Author</td>
	        <td width='120px'>Consultant</td>
	        <td>Technology</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT DISTINCT A.id, A.memberid, A.title, A.published, A.reasonforcancellation, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"B.login, " .
	    				"C.name, " .
	    				"(SELECT C.login from ols_questionanswers B INNER JOIN ols_members C ON C.member_id = B.memberid where B.questionid ORDER BY B.id DESC LIMIT 1) AS consultantlogin, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered " .
	    				"FROM ols_question A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"INNER JOIN ols_technology C " .
	    				"ON C.id = A.technologyid " .
	    				"$where  " .
	    				"ORDER BY A.id DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}

						echo "<tr>\n";
						
						if ($member['answered'] > 0) {
							echo "<td><img src='images/answered.png' title='Answered' /></td>";
							
						} else {
							echo "<td><img src='images/unanswered.png' /></td>";
						}
						
						if ($member['published'] == "Y") {
							echo "<td><img src='images/published.png' title='Published' /></td>";
							
						} else if ($member['published'] == "X") {
							echo "<td><img src='images/cancel.png' title='" . ($member['reasonforcancellation']) . "' /></td>";
							
						} else {
							echo "<td><img src='images/unpublished.png' title='Awaiting publication'  /></td>";
						}
						
						echo "<td>" . $member['createddate'] . "</td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "<td width='120px'>" . $member['login'] . "</td>";
						echo "<td width='120px'>" . $member['consultantlogin'] . "</td>";
						echo "<td>" . $member['name'] . "</td>";
						
						if ($member['published'] == "N" && $member['memberid'] == getLoggedOnMemberID()) {
								echo "<td width='20px'><a href='askquestion.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF'])) . "&id=" . $member['id'] . "'><img src='images/edit.png'  title='View question' /></a></td>";
							
						} else {
							echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img src='images/view.png'  title='View question' /></a></td>";
						}
						
			        	if (isUserInRole("CONSULTANT") || $member['memberid'] == getLoggedOnMemberID()) {
							echo "<td width='20px'><a href='javascript: remove(" . $member['id'] . ")'><img src='images/delete.png'  title='Delete question and contents' /></a></td>";
							
							if ($member['published'] != 'W') {
								echo "<td width='20px'><a href='javascript: withdraw(" . $member['id'] . ")'><img src='images/unpublish.png'  title='Withdraw question from publication' /></a></td>";
								
							} else {
								echo "<td width='20px'><a href='javascript: repost(" . $member['id'] . ")'><img src='images/repost.png'  title='Request publication' /></a></td>";
							}
							
			        	} else {
					        echo "<td width='20px'>&nbsp;</td>\n";
					        echo "<td width='20px'>&nbsp;</td>\n";
			        	}
			        	
						echo "</tr>\n";
					}
				} else {
					logError($qry . " = " . mysql_error());
				}
	    	?>
	    </tbody>
	</table>
	
	<?php
		if ($prevpage) {
			echo "<img src='images/previouspage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow - $pagesize) . "&to=" . ($torow - $pagesize) . "\"' />";
		}
		
		if ($nextpage) {
			echo "<img src='images/nextpage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow + $pagesize) . "&to=" . ($torow + $pagesize) . "\"' />";
		}
	?>
</div>
<script>
	var questionid = null;
	
	$(document).ready(function() {
			$("#reasondialog").dialog({
					modal: true,
					autoOpen: false,
					title: "Reason for withdrawal",
					width: 800,
					height: 420,
					buttons: {
						Ok: function() {
							tinyMCE.triggerSave();
							
							withdrawQuestion();
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
		});
		
	function withdrawReason() {
		$("#withdrawdialog").dialog("close");
		$("#reasondialog").dialog("open");
	}
	
	function withdraw(id) {
		questionid = id;
		
		$("#withdrawdialog .confirmdialogbody").html("You are about to withdraw this question from public access.<br>Are you sure ?");
		$("#withdrawdialog").dialog("open");
	}
	
	function repost(id) {
		call("repostQuestion", {pk1: id });
	}
	
	function withdrawQuestion(id) {
		call("withdrawQuestion", {pk1: questionid, pk2: $("#reason").val() });
	}
	
	function remove(id) {
		questionid = id;
		
		$("#removedialog .confirmdialogbody").html("You are about to remove this question.<br>Are you sure ?");
		$("#removedialog").dialog("open");
	}
	
	function deleteQuestion(id) {
		call("removeQuestion", {pk1: questionid});
	}
</script>
<?php
	include("system-footer.php");
} 
?>