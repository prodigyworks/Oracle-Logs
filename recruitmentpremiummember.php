<?php
	include("system-header.php"); 

	$data = getSiteConfigData();
?>
<h2>Upgrade to Premium Services for UNLIMITED access.</h2>
<form method="POST" action="premiummemberstage2.php" id="paypalform">
	<input type="hidden" id="description" name="description" />
	<input type="hidden" id="initial_amount" name="initial_amount" />
	<input type="hidden" id="amount" name="amount" />
	<input type="hidden" id="period" name="period" />
	<input type="hidden" id="frequency" name="frequency" />
	<input type="hidden" id="total_cycles" name="total_cycles" />

	<table cellspacing=0 cellpadding=10 id="premiumupgrade" class="grid2">
		<thead>
			<tr>
				<td><img src="images/subscriptions.png" /></td>
				<td align=right>USD</td>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tr>
			<td>Annual Membership - Annually.<br><span>You will be billed today and your subscription will be available immediately.</span></td>
			<td align=right><?php echo number_format($data->annualrecruitmentcost, 2); ?></td>
			
			<td>
			   	<span class="wrapper "><a  class='link1' href="javascript:stage2(1)"><em><b>Pay</b></em></a></span>
			</td>
		</tr>
		<tr>
			<td>Annual Membership - Quarterly.<br><span>You will be billed today and your subscription will be renewed quarterly.</span></td>
			<td align=right><?php echo number_format($data->quarterlyrecruitmentcost, 2); ?></td>
			<td>
			   	<span class="wrapper "><a  class='link1' href="javascript:stage2(2)"><em><b>Pay</b></em></a></span>
			</td>
		</tr>
		<tr>
			<td>Annual Membership - Monthly.<br><span>You will be billed today and your subscription will be renewed monthly.</span></td>
			<td align=right><?php echo number_format($data->monthlyrecruitmentcost, 2); ?></td>
			<td>
			   	<span class="wrapper "><a  class='link1' href="javascript:stage2(3)"><em><b>Pay</b></em></a></span>
			</td>
		</tr>
	</table>
</form>
<script>
	function stage2(type) {
		if (type == 1) {
			$("#description").val("Oracle Logs Subscription: Annual subscription");
			$("#initial_amount").val("0.00");
			$("#amount").val("<?php echo $data->annualrecruitmentcost; ?>");
			$("#period").val("Year");
			$("#frequency").val("1");
			$("#total_cycles").val("1");
			
		} else if (type == 2) {
			$("#description").val("Oracle Logs Subscription: Quarterly subscription");
			$("#initial_amount").val("0.00");
			$("#amount").val("<?php echo $data->quarterlyrecruitmentcost; ?>");
			$("#period").val("Month");
			$("#frequency").val("3");
			$("#total_cycles").val("4");
			
		} else if (type == 3) {
			$("#description").val("Oracle Logs Subscription: Monthly subscription");
			$("#initial_amount").val("0.00");
			$("#amount").val("<?php echo $data->monthlyrecruitmentcost; ?>");
			$("#period").val("Month");
			$("#frequency").val("1");
			$("#total_cycles").val("12");
		}
		
		$("#paypalform").submit();
	}
</script>
<?php
	include("system-footer.php"); 
?>