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
	$published = "N";
	$publishedrole = "";
	
	$qry = "SELECT A.id, A.title, A.body, A.technologyid, A.architecture, A.reasonforcancellation, " .
			"A.publishedrole, A.published, " .
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
			$published = $member['published'];
			$publishedrole = $member['publishedrole'];
?>
<form method="POST" class="entryform" id='articleform' action='askquestionpersist.php'>
	<h2>Title : <?php echo $member['title']; ?></h2>
	
	<label>Status</label>
	<input type='text' class="textbox70" readonly value="<?php 
	if ($member['published'] == "Y") {
		echo "Published";
		
	}  else if ($member['published'] == "N") {
		echo "Pending";
		
	}  else if ($member['published'] == "X") {
		echo "Cancelled";
		
	}  else if ($member['published'] == "W") {
		echo "Withdrawn";
	} 
	
	?>" />
	
	<?php
		if ($member['published'] == "X") {
	?>
			<label>Reason For Cancellation</label>
			<textarea readonly cols=80 rows=5><?php echo $member['reasonforcancellation']; ?></textarea>
	<?php
		}
	?>
	
	<label>Technology</label>
	<input type='text' class="textbox90" readonly value="<?php echo getParentTechnology($member['technologyid'], ""); ?>" />
	
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
	<div class="body">
		<?php echo $member['body']; ?>
	</div>
</form>

<?php
		}
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
<?php
	$qry = "SELECT B.* " .
			"FROM ols_questiondocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.documentid " .
			"WHERE A.questionid = $questionid";
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
<br>
<h4>Answers</h4>
<br>
<?php
	$qry = "SELECT A.*, B.login " .
			"FROM ols_questionanswers A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.questionid = $questionid " .
			"AND A.published = 'Y' " .
			"ORDER BY A.id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h5>Author : " . $member['login'] . "</h5>";
			echo "<h6>Posted : " . $member['createddate'] . "</h6>";
			
			echo "<div class='answer'>" . $member['body'] . "</div>";

			$qry = "SELECT B.* " .
					"FROM ols_questionanswerdocuments A " .
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
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}

	if (isAuthenticated() && (isUserInRole($publishedrole) || isUserInRole("CONSULTANT"))) {
?>
<br>
<hr />
<br>
<form method="post"  action='answerquestionpersist.php' id="answerform">
	<h4>Comment</h4>
	<input type="hidden" id="questionid" name="questionid" value="<?php echo $questionid; ?>" />
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
  	<span class="wrapper"><a id="submitanswer" class='link1' href="javascript:if (verify()) $('#answerform').submit();"><em><b>Submit</b></em></a></span>
</form>
<script type="text/javascript">
	function verify() {
		var isValid = verifyStandardForm('#answerform');
		
		if (tinyMCE.get('answerbody').getContent() == "") {
			$("#answerbody_tbl").addClass("invalid");
			isValid = false;
			
		} else {
			$("#answerbody_tbl").removeClass("invalid");
		}
		
		return isValid;
	}
	
	function preview() {
		$("#previewdiv").html($("#articlebody").html());
		$("#previewdialog").dialog("open");
	}
	
	$(document).ready(function() {
			$("#previewdialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					title: "Preview",
					show:"fade",
					hide:"fade",
					buttons: {
						Ok: function() {
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
	
	$("#technologyid").change(
			function() {
				getJSONData(
						'findtechnologyversion.php?id=' + $("#technologyid").val(), 
						"#technologyversionid", 
						function() {
						}
					);
			}
		);
	
	$("#operatingsystemid").change(
			function() {
				getJSONData(
						'findopsystemversion.php?id=' + $("#operatingsystemid").val(), 
						"#opsystemversionid", 
						function() {
						}
					);
			}
		);
</script>
	
<?php
		}
	}
	
	include("system-footer.php"); 
?>