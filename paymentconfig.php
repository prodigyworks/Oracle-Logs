<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
?>

<!--  Start of content -->
<?php
	if (isset($_POST['paypaluser'])) {
		$paypaluser = $_POST['paypaluser'];
		$paypalpassword = $_POST['paypalpassword'];
		$paypalsignature = $_POST['paypalsignature'];
		$sandbox = $_POST['sandbox'];
		$terms = mysql_escape_string($_POST['terms']);
		
		$qry = "UPDATE ols_siteconfig SET " .
				"paypaluser = '$paypaluser', " .
				"paypalpassword = '$paypalpassword', " .
				"paypalsignature = '$paypalsignature', " .
				"terms = '$terms', " .
				"sandbox = $sandbox";
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
	<label>Paypal User Name</label>
	<input type="text" required="true" class="textbox70" id="paypaluser" name="paypaluser" value="<?php echo $member['paypaluser']; ?>" />
	
	<label>Paypal Password</label>
	<input type="text" required="true" class="textbox70" id="paypalpassword" name="paypalpassword" value="<?php echo $member['paypalpassword']; ?>" />
	
	<label>Paypal Signature</label>
	<input type="text" required="true" class="textbox90" id="paypalsignature" name="paypalsignature" value="<?php echo $member['paypalsignature']; ?>" />
	
	<label>Paypal Sandbox</label>
	<select required="true" id="sandbox" name="sandbox">
		<option value=0>No</option>
		<option value=1>Yes</option>
	</select>
	
	<label>Terms and Condition (Premium Payment)</label>
	<textarea id="terms" name="terms" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
	<br>
	<br>
	<span class="wrapper"><a class='link1' href="javascript:if (verifyStandardForm('#contentForm')) $('#contentForm').submit();"><em><b>Update</b></em></a></span>
</form>
<script type="text/javascript">
	$(document).ready(function() {
			$("#sandbox").val("<?php echo $member['sandbox']; ?>");
			$("#terms").val("<?php echo escape_notes($member['terms']); ?>");
		});
</script>
	<?php
			}
		}
	?>
<!--  End of content -->

<?php include("system-footer.php"); ?>
