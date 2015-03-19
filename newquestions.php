<?php
	include("system-header.php"); 
	require_once("confirmdialog.php"); 
	
	createConfirmDialog("confirmrejectdialog", "Question rejection ?", "rejectQuestion");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "publishquestion.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
	
	function reject() {
		$questiond = $_POST['pk1'];
		$notes = mysql_escape_string($_POST['pk2']);
		
		$qry = "UPDATE ols_question SET " .
				"reasonforcancellation = '$notes', " .
				"published = 'X', " .
				"cancelleddate = NOW() " .
				"WHERE id = $questiond";
		$result = mysql_query($qry); 
		
		if (! $result) {
			die ($qry . " = " . mysql_error());
		}
	}
?>
<script>
	var questionID = 0;
	
	function cancelQuestion(id) {
		questionID = id;
		
		$("#rejectdialog").dialog("open");
	}
	
	function rejectQuestion() {
		call("reject", { pk1: questionID, pk2: $("#notes").val() });
	}
	$(document).ready(function() {
			$("#confirmrejectdialog .confirmdialogbody").html("You are about to reject this question.<br>Are you sure ?");
			
			$("#rejectdialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					title: "Rejection Notes",
					open: function(event, ui){
						$("#notes").focus();
					},
					buttons: {
						Ok: function() {
							$("#confirmrejectdialog").dialog("open");
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
				
			$("#rejectdialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					open: function(event, ui){
						$("#notes").focus();
					},
					buttons: {
						Ok: function() {
							$("#confirmrejectdialog").dialog("open");
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
		});
</script>
<div class="modal" id="rejectdialog">
	<h2>Question Rejection</h2>
	<textarea id="notes" name="notes" cols=152 rows=10></textarea>
</div>
<div class='questions'>
	<table cellspacing=0 cellpadding=0 width='100%' class='grid list' id="questiontable">
	    <thead>
	      <tr>
	        <td width='80px'>Date</td>
	        <td width='120px'>Author</td>
	        <td>Title</td>
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
	    		$qry = "SELECT A.id, A.title, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login " .
	    				"FROM ols_question A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"WHERE A.published = 'N' " .
	    				"ORDER BY A.createddate ASC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr><td width='80px'>" . $member['createddate'] . "</td>";
						echo "<td width='120px'>" . $member['login'] . "</td>";
						echo "<td>" . $member['title'] . "</td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'><img title='View question' src='images/view.png' /></a></td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'><img title='Publish question' src='images/publish.png' /></a></td>";
						echo "<td><a href='javascript: cancelQuestion(" . $member['id'] . ")'><img title='Cancel question' src='images/cancel.png' /></a></td></tr>";
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
<?php
	include("system-footer.php"); 
?>