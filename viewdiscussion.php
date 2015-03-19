<?php
	require_once("system-header.php"); 
	require_once("tinymce.php");
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Remove discussion thread ?", "removeDiscussion");
	createConfirmDialog("confirmremovediscussiondialog", "Remove discussion ?", "removeEntireDiscussion");
	
	$discussionid = $_GET['id'];
	$published = "N";
	$publishedrole = "";
	
	function removeDiscussion() {
		$id = $_POST['pk1'];
		$reason = $_POST['pk2'];
		$discussionid = $_GET['id'];
		
		$qry = "SELECT A.body, B.memberid " .
				"FROM ols_discussionanswers A " .
				"INNER JOIN ols_discussion B " .
				"ON B.id = A.discussionid " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage($member['memberid'], "Discussion removal", "<h3>The discussion has been removed</h3><h4>Reason: " . $reason . "</h3><p>" . $member['body'] . "</p>");
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "UPDATE ols_discussionanswers " .
				"SET published = 'X', " .
				"reasonforcancellation = '" . mysql_escape_string($reason) .  "' " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	function removeEntireDiscussion() {
		$id = $_POST['pk1'];
		$reason = $_POST['pk2'];
		$discussionid = $_GET['id'];
		
		$qry = "SELECT A.body, A.memberid " .
				"FROM ols_discussion A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage($member['memberid'], "Discussion removal", "<h3>The discussion has been removed</h3><h4>Reason: " . $reason . "</h3><p>" . $member['body'] . "</p>");
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "UPDATE ols_discussion " .
				"SET published = 'X', " .
				"reasonforcancellation = '" . mysql_escape_string($reason) .  "' " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
			
		header("location: discussions.php");
	}
	
	$qry = "SELECT A.id, A.title, A.body, A.publishedrole, A.published, " .
			"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
			"B.login " .
			"FROM ols_discussion A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.id = $discussionid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$published = $member['published'];
			$publishedrole = $member['publishedrole'];
?>
<form method="POST" class="entryform" id='articleform' action='askdiscussionsave.php'>
	<h2>Title : <?php echo $member['title']; ?></h2>
	<br>
	<br>
	<hr>
	<label>Discussion</label>
	<div class="body">
		<?php echo $member['body']; ?>
	</div>
</form>

<?php
			if (isUserInRole("ADMIN") && $member['published'] != 'X') {
			?>
			   	<br><span class="wrapper "><a id="pickfiles" class='link1' href="javascript:removeEntireThread(<?php echo $member['id']; ?>);"><em><b>Remove</b></em></a></span><br>
		   <?php
			}
		}
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
<?php
	$qry = "SELECT B.* " .
			"FROM ols_discussiondocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.documentid " .
			"WHERE A.discussionid = $discussionid";
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
	
	if ($published == "Y") {
?>
<br>
<hr />
<h4>Answers</h4>
<br>
<?php
	$qry = "SELECT A.*, B.login " .
			"FROM ols_discussionanswers A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.discussionid = $discussionid " .
			"AND A.published = 'Y' " .
			"ORDER BY A.id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h5>Author : " . $member['login'] . "</h5>";
			echo "<h6>Posted : " . $member['createddate'] . "</h6>";
			
			echo "<div class='answer'>" . $member['body'] . "</div><br>";
			
			if (isUserInRole("ADMIN")) {
			?>
			   	<span class="wrapper "><a id="pickfiles" class='link1' href="javascript:removeThread(<?php echo $member['id']; ?>);"><em><b>Remove</b></em></a></span><br><br>
		   <?php
			}

			$qry = "SELECT B.* " .
					"FROM ols_discussionanswerdocuments A " .
					"INNER JOIN ols_documents B " .
					"ON B.id = A.documentid " .
					"WHERE A.answerid = " . $member['id'];
			$itemresult = mysql_query($qry);
			$first = true;
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					if ($first) {
						$first = false;
						echo "<hr><p>Attached files. Click to view</p>";	
					}
					
					echo "<a target='_new' href='viewdocuments.php?id=" . $itemmember['id'] ."'>" . $itemmember['filename'] . "</a><br>";
				}
				
			} else {
				logError($qry . " = " . mysql_error());
			}

			if (! $first) {
				echo "<br><hr><br>";
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}

	if (isAuthenticated()) {
?>
<br>
<hr />
<form method="post"  action='answerdiscussionsave.php' id="answerform">
	<h4>Comment</h4>
	<input type="hidden" id="discussionid" name="discussionid" value="<?php echo $discussionid; ?>" />
	<textarea id="answerbody" name="answerbody" rows="15" cols="60" style="height:240px;width: 340px" class="tinyMCE"></textarea>
	
	<label>Attach File</label>
	<div id="upload_container">
	    <div id="filelist"></div>
	    <br />
	   	<span class="wrapper "><a id="pickfiles" class='link1 rgap5' href="javascript:;"><em><b>Select Files</b></em></a></span>
	   	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:;"><em><b>Upload Files</b></em></a></span>
	   	<br>
	   	<br>
	</div>
	<hr />
	<br>
  	<span class="wrapper"><a id="submitanswer" class='link1' href="javascript:$('#answerform').submit();"><em><b>Submit</b></em></a></span>
</form>
<div class="modal" id="rejectdialog">
	<h2>Reason for removal</h2>
	<textarea id="notes" name="notes" cols=152 rows=10></textarea>
</div>
<div class="modal" id="rejectentiredialog">
	<h2>Reason for removal</h2>
	<textarea id="entirenotes" name="entirenotes" cols=152 rows=10></textarea>
</div>
<script type="text/javascript">
	var currentThread = null;
	
	function removeThread(id) {
		currentThread = id;
		
		$("#rejectdialog").dialog("open");
	}
	
	function removeEntireThread(id) {
		currentThread = id;
		
		$("#rejectentiredialog").dialog("open");
	}
	
	function removeDiscussion() {
		call("removeDiscussion", {pk1: currentThread, pk2: $("#notes").val()});
	}
	
	function removeEntireDiscussion() {
		call("removeEntireDiscussion", {pk1: currentThread, pk2: $("#notes").val()});
	}
	
	$(document).ready(function() {
			$("#confirmdialog .confirmdialogbody").html("You are about to remove this discussion thread.<br>Are you sure ?");
			$("#confirmremovediscussiondialog .confirmdialogbody").html("You are about to remove the entire discussion.<br>Are you sure ?");
			
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
							$("#confirmdialog").dialog("open");
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
			
			$("#rejectentiredialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					open: function(event, ui){
						$("#entirenotes").focus();
					},
					buttons: {
						Ok: function() {
							$("#confirmremovediscussiondialog").dialog("open");
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
		});
	
	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container: 'upload_container',
		max_file_size : '10mb',
		url : 'system-documentupload.php',
		resize : {width : 320, height : 240, quality : 90},
		flash_swf_url : 'js/plupload.flash.swf',
		silverlight_xap_url : 'js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		]
	});
	
	uploader.bind('FilesAdded', function(up, files) {
		for (var i in files) {
			$('#filelist').html($('#filelist').html() + '<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b></div>');
		}
	});
	
	uploader.bind('UploadProgress', function(up, file) {
			$("#" + file.id + " b").html('<span>' + file.percent + "%</span>");
		});
	
	$('#uploadfiles').click(function() {
			uploader.start();
			return false;
		});
	
	uploader.init();
</script>
	
<?php
		}
	}
	
	include("system-footer.php"); 
?>