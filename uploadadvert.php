<?php
	require_once("system-db.php"); 
	
	start_db();
	initialise_db();
	
	if (! isUserInRole("CONSULTANT")) {
		redirectWithoutRole("PREMIUM", "advertpremiummember.php");
	}
	
	require_once("system-header.php"); 
?>
<form method="POST" enctype="multipart/form-data" class="entryform" id='adform' action='uploadadvertsaveconfirm.php' >
	<label>Group</label>
	<?php createCombo("groupid", "id", "name", "ols_advertgroup"); ?>
	
	<label>Title</label>
	<input required="true" type="text" value="" id="title" name="title" style="width:260px" />
	
	<label>URL</label>
	<input required="true" type="text" value="" id="url" name="url" style="width:660px" />
	
	<label>Image</label>
	<input required="true" type="file" value="" id="image" name="image" style="width:460px" />
	
	<label>Show From</label>
	<input required="true" type="text" value="" id="showfrom" name="showfrom" class="datepicker" />
	
	<label>Show To</label>
	<input required="true" type="text" value="" id="showto" name="showto" class="datepicker" />
	<br>
	<br>
   	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:if (verifyStandardForm('#adform')) $('#adform').submit();"><em><b>Submit</b></em></a></span>
</form>
<?php
	include("system-footer.php"); 
?>