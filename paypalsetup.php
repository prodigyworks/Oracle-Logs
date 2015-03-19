<?php
	require_once( 'system-db.php' );
	require_once( 'paypal-digital-goods.class.php' );
	require_once( 'paypal-subscription.class.php' );

	if (! isset($_SESSION)) {
		start_db();
		initialise_db();
	}	
	
	$paypal_subscription_year = null;
	$paypal_subscription_quarter = null;
	$paypal_subscription_month = null;
	$subscription_details_year = null;
	$subscription_details_quarter = null;
	$subscription_details_month = null;
	
	$data = getSiteConfigData();
	
	PayPal_Digital_Goods_Configuration::username( $data->paypaluser );
	PayPal_Digital_Goods_Configuration::password( $data->paypalpassword);
	PayPal_Digital_Goods_Configuration::signature( $data->paypalsignature);
	
//	PayPal_Digital_Goods_Configuration::return_url( 'http://localhost/jomon/premiummembersubscribe.php' );
//	PayPal_Digital_Goods_Configuration::notify_url( 'http://localhost/jomon/premiummembersubscribe.php' );
//	PayPal_Digital_Goods_Configuration::cancel_url( 'http://localhost/jomon/premiummembercancel.php' );
	PayPal_Digital_Goods_Configuration::return_url( 'http://www.prodigyworks.co.uk/jomon/premiummembersubscribe.php' );
	PayPal_Digital_Goods_Configuration::notify_url( 'http://www.prodigyworks.co.uk/jomon/premiummembersubscribe.php' );
	PayPal_Digital_Goods_Configuration::cancel_url( 'http://www.prodigyworks.co.uk/jomon/premiummembercancel.php' );
	
	if ($data->sandbox == 1) {
		PayPal_Digital_Goods_Configuration::environment( "sandbox" );
		
	} else {
		PayPal_Digital_Goods_Configuration::environment( "live" );
	}

	$subscription_details_year = array(
	    'description'        => 'Oracle Logs Subscription: Annual subscription.',
	    'initial_amount'     => '0.00',
	    'amount'             => '$' . $data->annualpremiumcost,
	    'period'             => 'Year',
	    'frequency'          => '1', 
	    'total_cycles'       => '1'
	);
	
	$paypal_subscription_year = new PayPal_Subscription( $subscription_details_year );

	$subscription_details_quarter = array(
	    'description'        => 'Oracle Logs Subscription: Annual subscription (quarterly).',
	    'initial_amount'     => '0.00',
	    'amount'             => '$' . $data->quarterlypremiumcost,
	    'period'             => 'Month',
	    'frequency'          => '3', 
	    'total_cycles'       => '4'
	);
	
	$paypal_subscription_quarter = new PayPal_Subscription( $subscription_details_quarter );
	
	$subscription_details_month = array(
	    'description'        => 'Oracle Logs Subscription: Annual subscription (monthly).',
	    'initial_amount'     => '0.00',
	    'amount'             => '$' . $data->monthlypremiumcost,
	    'period'             => 'Month',
	    'frequency'          => '1', 
	    'total_cycles'       => '12'
	);
	
	$paypal_subscription_month = new PayPal_Subscription( $subscription_details_month );
	
?>
