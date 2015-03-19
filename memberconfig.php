<?php 
	require_once("system-header.php"); 
?>

<!--  Start of content -->
<?php
	if (isset($_POST['annualpremiumcost'])) {
		$annualpremiumcost = $_POST['annualpremiumcost'];
		$annualrecruitmentcost = $_POST['annualrecruitmentcost'];
		$annualadvertisercost = $_POST['annualadvertisercost'];
	    $monthlypremiumcost = $_POST['monthlypremiumcost'];
	    $monthlyrecruitmentcost = $_POST['monthlyrecruitmentcost'];
		$monthlyadvertisercost  = $_POST['monthlyadvertisercost'];
	    $quarterlypremiumcost = $_POST['quarterlypremiumcost'];
	    $quarterlyrecruitmentcost = $_POST['quarterlyrecruitmentcost'];
		$quarterlyadvertisercost = $_POST['quarterlyadvertisercost'];
		
		$qry = "UPDATE ols_siteconfig SET " .
				"annualpremiumcost = $annualpremiumcost, " .
				"annualrecruitmentcost = $annualrecruitmentcost, " .
				"annualadvertisercost = $annualadvertisercost, " .
				"monthlypremiumcost = $monthlypremiumcost, " .
				"monthlyrecruitmentcost = $monthlyrecruitmentcost, " .
				"monthlyadvertisercost = $monthlyadvertisercost, " .
				"quarterlypremiumcost = $quarterlypremiumcost, " .
				"quarterlyrecruitmentcost = $quarterlyrecruitmentcost, " .
				"quarterlyadvertisercost = $quarterlyadvertisercost ";
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
	<table cellspacing=0 cellpadding=10 id="siteconfig" class="grid2">
		<tr>
			<td>&nbsp;</td>
			<td align=right>Annual</td>
			<td align=right>Quarterly</td>
			<td align=right>Monthly</td>
		</tr>
		<tr>
			<td><label>Premium Cost</label></td>
			<td><input required="true" style='text-align:right' type="text" id="annualpremiumcost" name="annualpremiumcost" value="<?php echo $member['annualpremiumcost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="quarterlypremiumcost" name="quarterlypremiumcost" value="<?php echo $member['quarterlypremiumcost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="monthlypremiumcost" name="monthlypremiumcost" value="<?php echo $member['monthlypremiumcost']; ?>" /></td>
		</tr>
		<tr>
			<td><label>Recruiter Cost</label></td>
			<td><input required="true" style='text-align:right' type="text" id="annualrecruitmentcost" name="annualrecruitmentcost" value="<?php echo $member['annualrecruitmentcost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="quarterlyrecruitmentcost" name="quarterlyrecruitmentcost" value="<?php echo $member['quarterlyrecruitmentcost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="monthlyrecruitmentcost" name="monthlyrecruitmentcost" value="<?php echo $member['monthlyrecruitmentcost']; ?>" /></td>
		</tr>
		<tr>
			<td><label>Advertiser Cost</label></td>
			<td><input required="true" style='text-align:right' type="text" id="annualadvertisercost" name="annualadvertisercost" value="<?php echo $member['annualadvertisercost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="quarterlyadvertisercost" name="quarterlyadvertisercost" value="<?php echo $member['quarterlyadvertisercost']; ?>" /></td>
			<td><input required="true" style='text-align:right' type="text" id="monthlyadvertisercost" name="monthlyadvertisercost" value="<?php echo $member['monthlyadvertisercost']; ?>" /></td>
		</tr>
	</table>
	<br>
	<br>
	<span class="wrapper"><a class='link1' href="javascript:if (verifyStandardForm('#contentForm')) $('#contentForm').submit();"><em><b>Update</b></em></a></span>
</form>
	<?php
			}
		}
	?>
<!--  End of content -->

<?php include("system-footer.php"); ?>
