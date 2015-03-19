<?php
	include("system-header.php"); 
	require_once( 'paypal-digital-goods.class.php' );
	require_once( 'paypal-subscription.class.php' );
	
	$data = getSiteConfigData();
	
	PayPal_Digital_Goods_Configuration::username( $data->paypaluser );
	PayPal_Digital_Goods_Configuration::password( $data->paypalpassword);
	PayPal_Digital_Goods_Configuration::signature( $data->paypalsignature);
	PayPal_Digital_Goods_Configuration::return_url( $data->domainurl . '/premiummembersubscribe.php' );
	PayPal_Digital_Goods_Configuration::notify_url( $data->domainurl . '/premiummembersubscribe.php' );
	PayPal_Digital_Goods_Configuration::cancel_url( $data->domainurl . '/premiummembercancel.php' );
	
	if ($data->sandbox == 1) {
		PayPal_Digital_Goods_Configuration::environment( "sandbox" );
		
	} else {
		PayPal_Digital_Goods_Configuration::environment( "live" );
	}
?>
<h3>Terms and conditions</h3>
<p><?php echo getPremiumPaymentTerms(); ?></p>
<h4>Subscription terms</h4>
<table cellspacing=0 cellpadding=1 id="premiumupgrade2" class="grid2">
	<tr>
		<td>Description</td>
		<td align=right><span><?php echo $_POST['description']; ?></span></td>
	</tr>
	<tr>
		<td>Amount</td>
		<td align=right><span><?php echo $_POST['amount'] . " (USD)"; ?></span></td>
	</tr>
	<tr>
		<td>Period of collection</td>
		<td align=right><span><?php echo $_POST['period']; ?></span></td>
	</tr>
	<tr>
		<td>Frequency</td>
		<td align=right><span><?php echo $_POST['frequency']; ?></span></td>
	</tr>
	<tr>
		<td>Cycles</td>
		<td align=right><span><?php echo $_POST['total_cycles']; ?></span></td>
	</tr>
</table>
<br>
<br>
<?php
	
	$subscription_details_year = array(
	    'description'        => $_POST['description'],
	    'initial_amount'     => $_POST['initial_amount'],
	    'amount'             => "$" . $_POST['amount'],
	    'period'             => $_POST['period'],
	    'frequency'          => $_POST['frequency'], 
	    'total_cycles'       => $_POST['total_cycles']
	);
	
	$qry = "INSERT INTO ols_paymentgatewayhistory ( " .
			"description, initial_amount, amount, period, frequency, total_cycles, expirydate, createddate " .
			") VALUES (" .
			"'" . $_POST['description'] . "', " . $_POST['initial_amount'] . ", " . $_POST['amount'] . ", " .
			"'" . $_POST['period'] . "', " . $_POST['frequency'] . ", " . $_POST['total_cycles'] . ", NOW(), NOW()" .
			")";
	
	$result = mysql_query($qry);
	$gatewayid = mysql_insert_id();
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "UPDATE ols_members " .
			"SET paymentgatewayid = $gatewayid " .
			"WHERE member_id = " . $_SESSION['SESS_MEMBER_ID'];
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$paypal_subscription = new PayPal_Subscription( $subscription_details_year );
	$paypal_subscription->print_buy_button(); 
?>
<?php
	include("system-footer.php"); 
?>