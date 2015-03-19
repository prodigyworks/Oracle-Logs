<?php
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
	
	function getParentTechnology($id, $path) {
		$qry = "SELECT A.parentid, A.name " .
				"FROM ols_technology A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($path != "") {
					$path = $path . " / " . $member['name'];
					
				} else {
					$path = $member['name'];
				}
				
				if ($member['parentid'] != null) {
					$path = getParentTechnology($member['parentid'], $path);
				}
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		return $path;
	}
	
	$questionid = $_GET['id'];
	
	$qry = "SELECT A.id, A.title, A.body, A.technologyid, A.architecture, A.publishedrole, A.published, " .
			"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
			"B.login, " .
			"C.name AS techname, " .
			"D.name AS techversion, " .
			"E.name AS opname, " .
			"F.name AS opversion " .
			"FROM ols_question A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"INNER JOIN ols_technology C " .
			"ON C.id = A.technologyid " .
			"INNER JOIN ols_technologyversion D " .
			"ON D.id = A.technologyversionid " .
			"INNER JOIN ols_operatingsystem E " .
			"ON E.id = A.opsystemid " .
			"INNER JOIN ols_operatingsystemversion F " .
			"ON F.id = A.opsystemversionid " .
			"WHERE A.id = $questionid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
		?>
		<div class="entryform">
			<h2>Title : <?php echo $member['title']; ?></h2>
			
			<label>Technology</label>
			<input type='text' class="textbox90" readonly value="<?php echo getParentTechnology($member['technologyid'], "");  ?>" />
			
			<label>Version</label>
			<input type='text' class="textbox10" readonly value="<?php echo $member['techversion']; ?>" />
			
			<label>Operating System</label>
			<input type='text' class="textbox70" readonly value="<?php echo $member['opname']; ?>" />
			
			<label>Version</label>
			<input type='text' class="textbox10" readonly value="<?php echo $member['opversion']; ?>" />
			
			<label>Architecture</label>
			<input type='text' class="textbox10" readonly value="<?php echo $member['architecture']; ?>" />
			<br>
			<br>
			<hr>
			<label>Question</label>
			<textarea id="notes" name="notes" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"><?php echo $member['body']; ?></textarea>
		</div>
		<?php
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "SELECT B.* " .
			"FROM ols_questiondocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.documentid " .
			"WHERE A.questionid = $questionid";
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
	<form id="publishForm" method="POST" action="publishquestionpersist.php" class="entryform">
		<div id="dummypanel" style="display:none"></div>
	</form>
	
	<div id="dialog" class="modal">
		<div id="publishpanel">
			<input type="hidden" id="questionid" name="questionid" value="<?php echo $_GET['id']; ?>" />
			<input type="hidden" id="questionbody" name="questionbody" />
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
								
								$("#questionbody").val($("#notes").val());
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
  			window.location.href = "newquestions.php";
  		}
  	</script>
<?php
	include("system-footer.php"); 
?>