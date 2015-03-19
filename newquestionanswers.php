<?php
	include("system-header.php"); 
	require_once("confirmdialog.php"); 
	
	createConfirmDialog("confirmrejectdialog", "Answer rejection ?", "rejectAnswer");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "publishquestionanswer.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
	
	
	function reject() {
		$questionanswerid = $_POST['pk1'];
		$notes = mysql_escape_string($_POST['pk2']);
		
		$qry = "UPDATE ols_questionanswers SET " .
				"reasonforcancellation = '$notes', " .
				"published = 'X', " .
				"cancelleddate = NOW() " .
				"WHERE id = $questionanswerid";
		$result = mysql_query($qry); 
		
		if (! $result) {
			die ($qry . " = " . mysql_error());
		}
	}
?>
<script>
	var questionanswersID = 0;
	
	function cancelAnswer(id) {
		questionanswersID = id;
		
		$("#rejectdialog").dialog("open");
	}
	
	function rejectAnswer() {
		call("reject", { pk1: questionanswersID, pk2: $("#notes").val() });
	}
	$(document).ready(function() {
			$("#confirmrejectdialog .confirmdialogbody").html("You are about to reject this answer.<br>Are you sure ?");
			
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
	<h2>Answer Rejection</h2>
	<textarea id="notes" name="notes" cols=152 rows=10></textarea>
</div>
<div class='articles'>
	<table cellspacing=0 cellpadding=0 width='100%' class='grid list' id="articletable">
	    <thead>
	      <tr>
	        <td width='80px'>Date</td>
	        <td>Title</td>
	        <td width='100px'>Author</td>
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
	    		$qry = "SELECT A.id, A.questionid, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login, C.title " .
	    				"FROM ols_questionanswers A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"INNER JOIN ols_question C " .
	    				"ON C.id = A.questionid " .
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
						echo "<td>" . $member['title'] . "</td>";
						echo "<td width='100px'>" . $member['login'] . "</td>";
						echo "<td width='20px'><a href='$viewer&questionid=" . $member['questionid'] .  "&id=" . $member['id'] . "'><img title='View question / answer' src='images/view.png' /></a></td>";
						echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img title='Publish answer' src='images/publish.png' /></a></td>";
						echo "<td><a href='javascript: cancelAnswer(" . $member['id'] . ")'><img title='Cancel answer' src='images/cancel.png' /></a></td></tr>";
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