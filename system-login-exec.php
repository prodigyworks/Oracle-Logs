<?php
	//Include database connection details
	require_once('system-config.php');
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
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	unset($_SESSION['LOGIN_ERRMSG_ARR']);
	unset($_SESSION['ERR_USER']);
	unset($_SESSION['MENU_CACHE']);
			
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$login = clean($_POST['login']);
	$password = clean($_POST['password']);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//Create query
	$qry = "SELECT DISTINCT A.* " .
		   "FROM ols_members A " .
		   "WHERE A.login = '". $_POST['login'] ."' " .
		   "AND A.passwd = '".md5($_POST['password'])."' " .
		   	"AND A.accepted = 'Y'";
	$result = mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		if(mysql_num_rows($result) == 1) {
			//Login Successful
			session_regenerate_id();
			$member = mysql_fetch_assoc($result);
			$paypalprofileid = $member['paypalprofileid'];
			
			$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member['firstname'];
			$_SESSION['SESS_LAST_NAME'] = $member['lastname'];
			
			$qry = "SELECT * FROM ols_userroles WHERE memberid = " . $_SESSION['SESS_MEMBER_ID'] . "";
			$result=mysql_query($qry);
			$index = 0;
			$status = null;
			
			$arr = array();
			$arr[$index++] = "PUBLIC";
			
			try {
				//Check whether the query was successful or not
				if($result) {
					while($member = mysql_fetch_assoc($result)) {
				
						if ($member['roleid'] == "PREMIUM") {
							if ($paypalprofileid != null) {
								$subscription_details = null;
								$memberid = $_SESSION['SESS_MEMBER_ID'];
								$qry = "SELECT B.* " .
										"FROM ols_members A " .
										"INNER JOIN ols_paymentgatewayhistory B " .
										"ON B.id = A.paymentgatewayid " .
										"WHERE A.member_id = $memberid";
								$itemresult = mysql_query($qry);
								
								if ($itemresult) {
									while (($itemmember = mysql_fetch_assoc($itemresult))) {
										$subscription_details = array(
										    'description'        => $itemmember['description'],
										    'initial_amount'     => $itemmember['initial_amount'],
										    'amount'             => "$" . $itemmember['amount'],
										    'period'             => $itemmember['period'],
										    'frequency'          => $itemmember['frequency'], 
										    'total_cycles'       => $itemmember['total_cycles']
										);
									}
								}
							
								if ($subscription_details != null) {
									$paypal_subscription = new PayPal_Subscription( $subscription_details );
									
									$status = $paypal_subscription->get_profile_details( $paypalprofileid );
									
									if ($status['STATUS'] == "Cancelled" || 
										$status['STATUS'] == "Suspended" || 
										$status['STATUS'] == "Expired") {
										
										sendRoleMessage("ADMIN", "Account subscription", "Account subscription for " . GetUserName($memberid) . " has been suspended due to failed payment");
										sendUserMessage($memberid, "Account subscription", "Account subscription has been suspended due to failed payment");
										
										$qry = "DELETE FROM ols_userroles " .
												"WHERE roleid = 'PREMIUM' " .
												"AND memberid = $memberid";
										$deleteresult = mysql_query($qry);
										
										if (! $deleteresult) {
											logError($qry . " = " . mysql_error());
										}
										
										
									} else {
										$arr[$index++] = $member['roleid'];
									}
									
								} else {
									$arr[$index++] = $member['roleid'];
								}
								
							} else {
								$arr[$index++] = $member['roleid'];
							}
							
						} else {
							$arr[$index++] = $member['roleid'];
						}
					}
					
				} else {
					logError('Failed to connect to server: ' . mysql_error());
				}
			} catch(Exception $e) {
				sendRoleMessage("ADMIN", "Account subscription", $e->getMessage());
			}
			
			
			$_SESSION['ROLES'] = $arr;
			
			$qry = "UPDATE ols_members SET " .
					"lastaccessdate = NOW(), " .
					"status = 'Y' " .
					"WHERE member_id = " . $_SESSION['SESS_MEMBER_ID'] . "";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
				
			} else {
				session_write_close();
				header("location: " . $_POST['callback']);
			}
			
			
			exit();
			
		} else {
			//If there are input validations, redirect back to the login form
			if (! $errflag) {
//				$errmsg_arr[] = "Login not found / Not active.<br>Please register or contact portal support";
				$errmsg_arr[] = "Invalid login";
			}
			
			$_SESSION['LOGIN_ERRMSG_ARR'] = $errmsg_arr;
			
			//Login failed
//		  	header("location: " . $_SERVER['HTTP_REFERER']);
			exit();
		}
	}else {
		logError("Query failed");
	}
?>