<?php
	require_once( 'system-db.php' );
	require_once( 'paypal-digital-goods.class.php' );
	require_once( 'paypal-subscription.class.php' );

	if (! isset($_SESSION)) {
		start_db();
		initialise_db();
	}	
	
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

	$subscription_details = null;
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$qry = "SELECT B.* " .
			"FROM ols_members A " .
			"INNER JOIN ols_paymentgatewayhistory B " .
			"ON B.id = A.paymentgatewayid " .
			"WHERE A.member_id = $memberid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			
			$subscription_details = array(
			    'description'        => $member['description'],
			    'initial_amount'     => $member['initial_amount'],
			    'amount'             => "$" . $member['amount'],
			    'period'             => $member['period'],
			    'frequency'          => $member['frequency'], 
			    'total_cycles'       => $member['total_cycles']
			);
		}
	}
	

	$paypal_subscription = new PayPal_Subscription( $subscription_details );
	$profileid = $paypal_subscription->start_subscription();
	
	$qry = "INSERT INTO ols_userroles " .
		   "(roleid, memberid) " .
		   "VALUES " .
		   "('PREMIUM', $memberid)";
	$result = mysql_query($qry);
	
	$qry = "UPDATE ols_members " .
		   "SET paypalprofileid = '" . $profileid['PROFILEID']. "' " .
		   "WHERE member_id = $memberid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	sendUserMessage($memberid, "Premium Subscription", "<h1>Premium Subscription</h1><p>Welcome to premium membership for Oracle Logs</p>");
	sendRoleMessage("ADMIN", "Premium Subscription", "<h1>Premium Subscription for member " . $_SESSION['SESS_FIRST_NAME'] . " " . $_SESSION['SESS_LAST_NAME'] . "</h1><p>Welcome to premium membership for Oracle Logs</p>");
	
	$_SESSION['ROLES'][count($_SESSION['ROLES'])] = "PREMIUM";
?>
<html>
<head>
<script>
	if (window.parent) {
		window.parent.location.href = "premiummemberconfirm.php";
		
	} else {
		window.location.href = "premiummemberconfirm.php";
	}
</script>
</head>
</html>