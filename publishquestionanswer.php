<?php
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
	
	$answerid = $_GET['id'];
	
	$qry = "SELECT B.id AS questionid, B.title, B.body AS questionbody, " .
			"DATE_FORMAT(B.createddate, '%d/%m/%Y') AS questioncreateddate, C.login, " .
			"A.body, DATE_FORMAT(B.createddate, '%d/%m/%Y') AS createddate, D.login AS questionlogin " .
			"FROM ols_questionanswers A " .
			"INNER JOIN ols_question B " .
			"ON B.id = A.questionid " .
			"INNER JOIN ols_members C " .
			"ON C.member_id = A.memberid " .
			"INNER JOIN ols_members D " .
			"ON D.member_id = B.memberid " .
			"WHERE A.id = $answerid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h2>Title : " . $member['title'] . "</h2>";
			echo "<h4>Author : " . $member['questionlogin'] . "</h4>";
			echo "<h5>Posted on " . $member['questioncreateddate'] . "</h5>";
			echo "<p>" . $member['questionbody']. "</p>";
			echo "<br><hr><br><h4>Answer</h4>";
			echo "<h4>Author : " . $member['login'] . "</h4>";
			echo "<h5>Posted on " . $member['createddate'] . "</h5>";
			?>
			<textarea id="notes" name="notes" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"><?php echo $member['body']; ?></textarea>
			<?php
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "SELECT B.* " .
			"FROM ols_questionanswerdocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.answerid " .
			"WHERE A.answerid = $answerid";
	$result = mysql_query($qry);
	
	if ($result) {
		$first = true;
		
		while (($member = mysql_fetch_assoc($result))) {
			if ($first) {
				$first = false;
				echo "<hr><p>Attached files. Click to view</p>";
			}

			echo "<a target='_new' href='viewdocuments.php?id=" . $member['id'] ."'>" . $member['filename'] . "</a><br>";
		}
	
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
	<form id="publishForm" method="POST" action="publishquestionanswerpersist.php">
		<div id="dummypanel" style="display:none"></div>
	</form>
	
	<div id="dialog" class="modal">
		<div id="publishpanel">
			<input type="hidden" id="answerid" name="answerid" value="<?php echo $_GET['id']; ?>" />
			<input type="hidden" id="answerbody" name="answerbody" />
			<table width='100%'>
				<tr>
					<td>Publish to role</td>
					<td>
						<?php createCombo("roleid", "roleid", "roleid", "ols_roles"); ?>
					</td>
				</tr>
				<tr>
					<td>Show from date</td>
					<td><input type="text" id="publishdate" name="publishdate" class="datepicker" /></td>
				</tr>
				<tr>
					<td>Expiry date</td>
					<td><input type="text" id="expirydate" name="expirydate" class="datepicker" /></td>
				</tr>
			</table>
		</div>
	</div>
	<br>
	<hr>
	<br>
  	<span class="wrapper"><a class='link1 rgap5' href="javascript:publish();"><em><b>Publish</b></em></a></span>
  	<span class="wrapper"><a class='link1' href="javascript:back();"><em><b>Back</b></em></a></span>
  	<script>
		$(document).ready(function() {
				$("#dialog").dialog({
						autoOpen: false,
						modal: true,
						title: "Publish",
						buttons: {
							Ok: function() {
								tinyMCE.triggerSave();

								$(this).dialog("close");
								
								$("#answerbody").val($("#notes").val());
								$("#publishpanel").appendTo("#dummypanel");
								$("#publishForm").submit();
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
			});
		
  		function publish() {
  			$("#dialog").dialog("open");
  		}
  		
  		function back() {
  			window.location.href = "newquestionanswers.php";
  		}
  	</script>
<?php
	include("system-footer.php"); 
?>