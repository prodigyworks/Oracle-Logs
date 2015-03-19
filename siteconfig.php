<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
?>

<!--  Start of content -->
<?php
	if (isset($_POST['domainurl'])) {
		$googlemapv2apikey = mysql_escape_string($_POST['googlemapv2apikey']);
		$domainurl = mysql_escape_string($_POST['domainurl']) ;
		$emailfooter = mysql_escape_string($_POST['emailfooter']);
		$jobcreationgreeting = mysql_escape_string($_POST['jobcreationgreeting']);
		$termsandconditions = mysql_escape_string($_POST['termsandconditions']);
		
		$qry = "UPDATE ols_siteconfig SET " .
				"googlemapv2apikey = '$googlemapv2apikey', " .
				"jobcreationgreeting = '$jobcreationgreeting', " .
				"termsandconditions = '$termsandconditions', " .
				"domainurl = '$domainurl', " .
				"emailfooter = '$emailfooter'";
		$result = mysql_query($qry);

	   	if (! $result) {
	   		logError("UPDATE ols_siteconfig:" . $qry . " - " . mysql_error());
	   	}
	   	
	   	unset($_SESSION['SITE_CONFIG']);
	}
	
	$qry = "SELECT * FROM ols_siteconfig";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
?>
<form id="contentForm" name="contentForm" method="post" class="entryform">
	<label>Google Maps Api Key (v2)</label>
	<input required="true" type="text" class="textbox90" id="googlemapv2apikey" name="googlemapv2apikey" value="<?php echo $member['googlemapv2apikey']; ?>" />
	
	<label>Domain URL</label>
	<input required="true" type="text" class="textbox90" id="domainurl" name="domainurl" value="<?php echo $member['domainurl']; ?>" />
	
	<label>Job Creation Greeting</label>
	<textarea required="true" cols=120 rows=5 id="jobcreationgreeting" name="jobcreationgreeting"><?php echo $member['jobcreationgreeting']; ?></textarea>
	
	<label>Terms And Conditions</label>
	<textarea required="true" id="termsandconditions" name="termsandconditions" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	
	<label>E-mail Footer</label>
	<textarea id="emailfooter" name="emailfooter" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	<br>
	<br>
	<span class="wrapper"><a class='link1' href="javascript:if (verifyStandardForm('#contentForm')) $('#contentForm').submit();"><em><b>Update</b></em></a></span>
</form>
<script type="text/javascript">
	$(document).ready(function() {
			$("#emailfooter").val("<?php echo escape_notes($member['emailfooter']); ?>");
			$("#termsandconditions").val("<?php echo escape_notes($member['termsandconditions']); ?>");
		});
</script>
	<?php
			}
		}
	?>
<!--  End of content -->

<?php include("system-footer.php"); ?>
