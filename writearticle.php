<?php
	require_once("system-db.php"); 
	
	start_db();
	initialise_db();
	
	if (! isUserInRole("CONSULTANT")) {
		redirectWithoutRole("PREMIUM", "premiummember.php");
	}
	
	require_once("system-header.php"); 
	require_once("tinymce.php");
?>
<style>
	.previewdiv {
		width: 750px;
		height: 300px;
	}
</style>


<form method="POST" class="entryform" id='articleform' action='writearticlepersist.php'>
	<label>Category</label>
	<?php createCombo("categoryid", "id", "name", "ols_questioncategory"); ?>
	
	<label>Title</label>
	<input required="true" type="text" value="" id="title" name="title" style="width:260px" />
	
	<label>Article Body</label>
	<textarea id="articlebody" name="articlebody" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	
	<label>Attach File</label>
	<div id="upload_container">
	    <div id="filelist"></div>
	    <br />
	   	<span class="wrapper "><a id="pickfiles" class='link1 rgap5' href="javascript:;"><em><b>Select Files</b></em></a></span>
	   	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:;"><em><b>Upload Files</b></em></a></span>
	   	<br>
	   	<br>
	</div>
	
	<label>Tags</label>
	<input type="text" value="" id="tags" name="tags" style="width:460px" />
<?php
	if (isUserInRole("CONSULTANT")) {
?>	
	<label>Publish to role</label>
	<?php createCombo("roleid", "roleid", "roleid", "ols_roles"); ?>
	
	<label>Featured</label>
	<SELECT required="true" id="featured" name="featured">
		<OPTION value="N">No</OPTION>
		<OPTION value="Y">Yes</OPTION>
	</SELECT>
	
	<label>Show from date</label>
	<input required="true" type="text" id="publishdate" name="publishdate" class="datepicker" />
	
	<label>Expiry date</label>
	<input required="true" type="text" id="expirydate" name="expirydate" class="datepicker" />
<?php
	}
?>	
	<br>
	<br>
	
   	<span class="wrapper "><a id="pickfiles" class='link1 rgap5' href="javascript:preview()"><em><b>Preview</b></em></a></span>
   	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:if (verify()) $('#articleform').submit();"><em><b>Submit</b></em></a></span>
</form>
<div id="previewdialog" class="modal">
	<div id='previewdiv'></div>
</div>
<script type="text/javascript">
	function verify() {
		var isValid = verifyStandardForm('#articleform');
		
		if (tinyMCE.get('articlebody').getContent() == "") {
			$("#articlebody_tbl").addClass("invalid");
			isValid = false;
			
		} else {
			$("#articlebody_tbl").removeClass("invalid");
		}
		
		return isValid;
	}
	
	function preview() {
		$("#previewdiv").html($("#articlebody").text());
		
		envelopeCode("#previewdiv pre");
		
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
</script>
<?php
	include("system-footer.php"); 
?>