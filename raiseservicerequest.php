<?php
	require_once("system-db.php"); 
	
	start_db();
	initialise_db();
	
	if (! isUserInRole("CONSULTANT")) {
		redirectWithoutRole("PREMIUM", "premiummember.php");
	}
	
	require_once("system-header.php"); 
	require_once("tinymce.php");
	
	function getParentTechnology($id, $array) {
		$qry = "SELECT A.parentid, A.name " .
				"FROM ols_technology A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		$array[count($array)] = $id;
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($member['parentid'] != null) {
					$array = getParentTechnology($member['parentid'], $array);
				}
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		return $array;
	}
?>
<style>
	.previewdiv {
		width: 750px;
		height: 300px;
	}
	
	#versiondiv {
		display: none;
	}
</style>

<form method="POST" class="entryform" id='articleform' action='raiseservicerequestpersist.php<?php if (isset($_GET['id'])) echo "?id=" . $_GET['id']; ?>'>
	<label>Technology</label>
	<?php createCombo("technologyid", "id", "name", "ols_technology", "WHERE parentid IS NULL"); ?>

	<div id="subversioncontainer">	
		<div id="subversions">
		</div>
	</div>
	
	<div id="versiondiv">
		<label>Version</label>
		<select required="true" id="technologyversionid" name="technologyversionid" style="width:200px">
			<option value="0"></option>
		</select>	
	</div>
	
	<label>Operating System</label>
	<?php createCombo("operatingsystemid", "id", "name", "ols_operatingsystem"); ?>
	
	<label>Version</label>
	<select required='true' id="opsystemversionid" name="opsystemversionid" style="width:200px">
		<option value="0"></option>
	</select>	
	
	<label>Architecture</label>
	<SELECT required='true' id='architecture' name='architecture'>
		<OPTION value="32">32 Bit</OPTION>
		<OPTION value="64">64 Bit</OPTION>
	</SELECT>
	
	<label>Title</label>
	<input required='true' type="text" value="" id="title" name="title" style="width:260px" />
	
	<label>Service Request</label>
	<textarea required='true' id="servicerequestbody" name="servicerequestbody" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	
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
		
		if (tinyMCE.get('servicerequestbody').getContent() == "") {
			$("#servicerequestbody_tbl").addClass("invalid");
			isValid = false;
			
		} else {
			$("#servicerequestbody_tbl").removeClass("invalid");
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
				
			<?php 
				if (isset($_GET['id'])) {
					$id = $_GET['id'];
		    		$qry = "SELECT * FROM ols_servicerequest WHERE id = $id";
					$result = mysql_query($qry);
					
					if ($result) {
						while (($member = mysql_fetch_assoc($result))) {
							$index = 1;
							$array = array();
							$array = getParentTechnology($member['technologyid'], $array);
							
							echo "$('#technologyid').val('" . $array[count($array) - 1] . "').trigger('change');\n";
							
							for ($i = count($array) - 2; $i >= 0; $i--) {
								echo "$('#technologysubid" . $index . "').val('" . $array[$i] . "').trigger('change');\n";
								
								$index++;
							}
					
			?>
							$("#technologyversionid").val("<?php echo $member['technologyversionid'];?>").trigger("change");
							$("#operatingsystemid").val("<?php echo $member['opsystemid'];?>").trigger("change");
							$("#opsystemversionid").val("<?php echo $member['opsystemversionid'];?>").trigger("change");
							$("#architecture").val("<?php echo $member['architecture'];?>").trigger("change");
							$("#title").val("<?php echo $member['title'];?>").trigger("change");
							$("#servicerequestbody").val("<?php echo escape_notes($member['body']); ?>").trigger("change");
			<?php
							
							if ($member['memberid'] != getLoggedOnMemberID()) {
			?>
								$("select").each(function() {
									$(this).attr('disabled', true);
								});
			<?php
							}
						}
					}
				}
			?>
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
	
	function createSubTech(index, data) {
		$("#subversions").before("<label>Sub Technology</label><select required='true' id='technologysubid" + index + "' name='technologysubid[]'><option value='0'></option></select>");
		$("#subversioncontainer").show();
		$("#versiondiv").hide();
		
		populateCombo("#technologysubid" + index, data, true);
		
		$("#technologysubid" + index).change(
				function() {
					var node = $(this);
					
					callAjax(
							"findsubtechnology.php", 
							{ 
								parentid: $(node).val() 
							},
							function(data) {
								if (data.length > 0) {
									createSubTech(index + 1, data);
								
								} else {
									$("#versiondiv").show();
								
									getJSONData(
											'findtechnologyversion.php?id=' + $(node).val(), 
											"#technologyversionid", 
											function() {
											}
										);
								}
							},
							false
						);
				}
			);
	}
	
	$("#technologyid").change(
			function() {
				$("#subversioncontainer").html("<div id='subversions' />");
			
				callAjax(
						"findsubtechnology.php", 
						{ 
							parentid: $("#technologyid").val() 
						},
						function(data) {
							if (data.length > 0) {
								createSubTech(1, data);
							
							} else {
								$("#subversioncontainer").hide();
								$("#versiondiv").show();
								
								getJSONData(
										'findtechnologyversion.php?id=' + $("#technologyid").val(), 
										"#technologyversionid", 
										function() {
										}
									);
							}
						},
						false
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