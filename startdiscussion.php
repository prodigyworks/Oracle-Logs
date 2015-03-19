<?php
	require_once("system-header.php"); 
	require_once("tinymce.php");
?>
<style>
	.previewdiv {
		width: 750px;
		height: 300px;
	}
</style>
<?php 
	if (isset($_GET['requestchat']) && $_GET['requestchat'] == "true") {
?>
<form method="POST" class="entryform" id='articleform' action='requestexpertsetup.php'>
<?php 
	} else {
?>
<form method="POST" class="entryform" id='articleform' action='startdiscussionpersist.php'>
<?php 
	}
?>
<form method="POST" class="entryform" id='articleform' action='startdiscussionpersist.php?requestchat='>
	<label>Title</label>
	<input required='true' type="text" value="" id="title" name="title" style="width:260px" />
	
	<label>Discussion</label>
	<textarea required='true' id="discussionbody" name="discussionbody" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	
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
  	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:if (verify()) $('#articleform').submit();"><em><b>Submit</b></em></a></span>
</form>
<div id="previewdialog" class="modal">
	<div id='previewdiv'></div>
</div>
<script type="text/javascript">
	function verify() {
		var isValid = verifyStandardForm('#articleform');
		
		if (tinyMCE.get('discussionbody').getContent() == "") {
			$("#discussionbody_tbl").addClass("invalid");
			isValid = false;
			
		} else {
			$("#discussionbody_tbl").removeClass("invalid");
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
	include("system-footer.php"); 
?>