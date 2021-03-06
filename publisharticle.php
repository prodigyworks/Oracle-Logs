<?php
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
	
	$articleid = $_GET['id'];
	
	$qry = "SELECT A.id, A.title, A.body, A.tags, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login " .
			"FROM ols_article A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.id = $articleid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h2>Title : " . $member['title'] . "</h2>";
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
			"FROM ols_articledocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.documentid " .
			"WHERE A.articleid = $articleid";
	$result = mysql_query($qry);
	$first = true;
	
	if ($result) {
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
	<form id="publishForm" method="POST" action="publisharticlepersist.php" class="entryform">
		<div id="dummypanel" style="display:none"></div>
	</form>
	
	<div id="dialog" class="modal">
		<div id="publishpanel">
			<input type="hidden" id="articleid" name="articleid" value="<?php echo $_GET['id']; ?>" />
			<input type="hidden" id="articlebody" name="articlebody" />
			<table width='100%'>
				<tr>
					<td>Publish to role</td>
					<td>
						<?php createCombo("roleid", "roleid", "roleid", "ols_roles"); ?>
					</td>
				</tr>
				<tr>
					<td>Featured</td>
					<td>
						<SELECT id="featured" name="featured">
							<OPTION value="N">No</OPTION>
							<OPTION value="Y">Yes</OPTION>
						</SELECT>
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
								
								$("#articlebody").val($("#notes").val());
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
  			window.location.href = "newarticles.php";
  		}
  	</script>
<?php
	include("system-footer.php"); 
?>