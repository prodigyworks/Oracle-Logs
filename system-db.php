<?php
$global_error = false;

class SiteConfigClass {
	public $annualpremiumcost;
	public $annualrecruitmentcost;
	public $annualadvertisercost;
	public $paypaluser;
	public $paypalpassword;
	public $paypalsignature;
	public $monthlypremiumcost;
	public $monthlyrecruitmentcost;
	public $monthlyadvertisercost;
	public $quarterlypremiumcost;
	public $quarterlyrecruitmentcost;
	public $quarterlyadvertisercost;
	public $sandbox;
	public $googlemapv2apikey;
	public $domainurl;
	public $emailfooter;
	public $terms;
	public $termsandconditions;
	public $jobcreationgreeting;
	
}
	
function start_db() {
	if(!isset($_SESSION)) {
		session_start();
	}
	
	error_reporting(E_ALL ^ E_DEPRECATED);

	if (! defined('DB_HOST')) {
		/*
		define('DB_HOST', 'localhost');
	    define('DB_USER', 'root');
	    define('DB_PASSWORD', 'root');
	    define('DB_DATABASE', 'jomon');
	    */
	    
		define('DB_HOST', 'prodigyworks.co.uk.mysql');
	    define('DB_USER', 'prodigyworks_co');
	    define('DB_PASSWORD', 'i6qFAWND');
	    define('DB_DATABASE', 'prodigyworks_co');
	}
/*
 * Database:

Server IP: 213.171.193.147

DB Name: dtcrmdb

User: dtcrmuser

Pass: KwdC5yWtFn


 */
}

function GetUserName($userid = "") {
	if ($userid == "") {
		return $_SESSION['SESS_FIRST_NAME'] . " " . $_SESSION['SESS_LAST_NAME'];
		
	} else {
		$qry = "SELECT * FROM ols_members A " .
				"WHERE A.member_id = $userid ";
		$result = mysql_query($qry);
		$name = "Unknown";
	
		//Check whether the query was successful or not
		if($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$name = $member['firstname'] . " " . $member['lastname'];
			}
		}
		
		return $name;
	}
}

function GetEmail($userid) {
	$qry = "SELECT email FROM ols_members A " .
			"WHERE A.member_id = $userid ";
	$result = mysql_query($qry);
	$name = "Unknown";

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$name = $member['email'];
		}
	}
	
	return $name;
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")  
{ 
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue; 
 
  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue); 
 
  switch ($theType) { 
    case "text": 
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; 
      break;     
    case "long": 
    case "int": 
      $theValue = ($theValue != "") ? intval($theValue) : "NULL"; 
      break; 
    case "double": 
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL"; 
      break; 
    case "date": 
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; 
      break; 
    case "defined": 
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; 
      break; 
  } 
  return $theValue; 
} 

function initialise_db() {
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	
	if (!$link) {
		logError('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	
	if(!$db) {
		logError("Unable to select database");
	}
	
	if (! isset($_SESSION['SITE_CONFIG'])) {
		$qry = "SELECT * FROM ols_siteconfig";
		$result = mysql_query($qry);

		//Check whether the query was successful or not
		if ($result) {
			if (mysql_num_rows($result) == 1) {
				$member = mysql_fetch_assoc($result);
				
				$data = new SiteConfigClass();
				$data->termsandconditions = $member['termsandconditions'];
				$data->jobcreationgreeting = $member['jobcreationgreeting'];
				$data->annualpremiumcost = $member['annualpremiumcost'];
				$data->annualrecruitmentcost = $member['annualrecruitmentcost'];
				$data->annualadvertisercost = $member['annualadvertisercost'];
				$data->paypaluser = $member['paypaluser'];
				$data->paypalpassword = $member['paypalpassword'];
				$data->paypalsignature = $member['paypalsignature'];
			    $data->monthlypremiumcost = $member['monthlypremiumcost'];
			    $data->monthlyrecruitmentcost = $member['monthlyrecruitmentcost'];
				$data->monthlyadvertisercost  = $member['monthlyadvertisercost'];
			    $data->quarterlypremiumcost = $member['quarterlypremiumcost'];
			    $data->quarterlyrecruitmentcost = $member['quarterlyrecruitmentcost'];
				$data->quarterlyadvertisercost = $member['quarterlyadvertisercost'];
				$data->sandbox = $member['sandbox'];
				$data->googlemapv2apikey = $member['googlemapv2apikey'];
				$data->domainurl = $member['domainurl'];
				$data->emailfooter = $member['emailfooter'];
				$data->terms = $member['terms'];
				
				$_SESSION['SITE_CONFIG'] = $data;
			}
				
		} else {
			header("location: system-access-denied.php");
		}
	}
}
	
function dateStampString($oldnotes, $newnotes, $prefix = "") {
	if ($newnotes == $oldnotes) {
		return $oldnotes;
	}
	
	return 
		mysql_escape_string (
				$oldnotes . "\n\n" .
				$prefix . " - " . 
				date("F j, Y, g:i a") . " : " . 
				$_SESSION['SESS_FIRST_NAME'] . " " . 
				$_SESSION['SESS_LAST_NAME'] . "\n" . 
				$newnotes
			);
}

	
function smtpmailer($to, $from, $from_name, $subject, $body, $attachments = array()) { 
	global $error;
	
	$random_hash = md5(date('r', time()));  
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	//define the headers we want passed. Note that they are separated with \r\n  
	//add boundary string and mime type specification  
	$headers = "From: $from_name <$from>\r\nReply-To: $from";  
	$headers .= "To: <$to>" . "\r\n";
	
	$message = $body;
 
	
	if (count($attachments) > 0) {
 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	 
		// multipart boundary 
		$message = "Attached CV and mail.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
		$message .= "--{$mime_boundary}\n";
	 
		// multipart boundary 
		// preparing attachments
		for($x=0;$x<count($attachments);$x++){
			$file = fopen($attachments[$x],"rb");
			$data = fread($file,filesize($attachments[$x]));
			fclose($file);
			$data = chunk_split(base64_encode($data));
			$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"" . basename($attachments[$x]) . "\"\n" . 
						"Content-Disposition: attachment;\n" . " filename=\"" .basename($attachments[$x]) . "\"\n" . 
						"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
			$message .= "--{$mime_boundary}\n";
		}
		
	}
	
	@mail(
			$to,
			$subject,
			$message,
			$headers
		);
}

function smtpmailer3($to, $from, $from_name, $subject, $body, $attachments = array()) { 
	global $error;
	
	$random_hash = md5(date('r', time()));

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	
	// Additional headers
	$headers .= "To: <$to>" . "\r\n";
	$headers .= "From: $from_name <$from>" . "\r\n";
	
	mail(
			$to,
			$subject,
			$body,
			$headers
		);
}
function smtpmailer2($to, $from, $from_name, $subject, $body, $attachments = array()) { 
	require_once('phpmailer/class.phpmailer.php');

	global $error;
	
	$array = explode(',', $to);
	
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
//	$mail->SMTPAuth = false;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
//	$mail->Host = 'smtp.gmail.com';
	$mail->Host = 'send.one.com';
	$mail->Port = 465; 
//	$mail->Port = 25; 
	$mail->Username = "istudentcontrol@gmail.com";  
	$mail->Password = "istudent";           
//	$mail->Username = "kevin.hilton@prodigyworks.co.uk";  
//	$mail->Password = "Jasmin717440";           
	$mail->SetFrom($from, $from_name);
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$mail->Body = $body;
	
	for ($i = 0; $i < count($attachments); $i++) {
		$mail->AddAttachment($attachments[$i]);
	}
	
	for ($i = 0; $i < count($array); $i++) {
		$mail->AddAddress($array[$i]);
	}
	
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
		
	} else {
		$error = 'Message sent!';
		return true;
	}
}

function sendRoleMessage($role, $subject, $message) {
	require_once('phpmailer/class.phpmailer.php');

	$qry = "SELECT B.email, B.firstname FROM ols_userroles A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.roleid = '$role' ";
	$result = mysql_query($qry);
	$str = "";
	$i = 0;

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			if ($i > 0) {
				$str = $str . ", ";
			}
			
			$i = $i + 1;
			
			$str = $str . $member['email'];
		}
			
		smtpmailer($str, 'admin@oraclelogs.com', 'Oracle Logs Administration', $subject, getEmailHeader() . "<h4>Dear " . $member['firstname'] . ",</h4><p>" . $message . "</p>" . getEmailFooter());
	}
	
	if (!empty($error)) echo $error;
}
	
function endsWith( $str, $sub ) {
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function isAuthenticated() {
	return ! (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == ''));
}

function sendUserMessage($id, $subject, $message, $footer = "", $attachments = array()) {
	$qry = "SELECT B.email, B.firstname FROM ols_members B " .
			"WHERE B.member_id = $id ";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			smtpmailer($member['email'], 'admin@oraclelogs.com', 'Oracle Logs Administration', $subject, getEmailHeader() . "<h4>Dear " . $member['firstname'] . ",</h4><p>" . $message . "</p>" . getEmailFooter(). $footer, $attachments);
		}
	}
	
	if (!empty($error)) echo $error;
}

function sendContactMessage($id, $subject, $message, $footer = "") {
	$qry = "SELECT B.email, B.firstname FROM ols_contacts B " .
			"WHERE B.id = $id ";
	$result = mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			smtpmailer($member['email'], 'admin@oraclelogs.com', 'Oracle Logs Administration', $subject, getEmailHeader() . "<h4>Dear " . $member['firstname'] . ",</h4><p>" . $message . "</p>" . getEmailFooter(). $footer);
		}
	}
	
	if (!empty($error)) echo $error;
}

function createCombo($id, $value, $name, $table, $where = " ", $required = true) {
	echo "<select required='" . $required . "' id='" . $id . "'  name='" . $id . "'>";
	createComboOptions($value, $name, $table, $where);
	
	echo "</select>";
}
	

function createComboOptions($value, $name, $table, $where = " ", $blank = true) {
	if ($blank) {
		echo "<option value='0'></option>";
	}
		
	$qry = "SELECT * " .
			"FROM $table " .
			$where . " " . 
			"ORDER BY $name";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<option value=" . $member[$value] . ">" . $member[$name] . "</option>";
		}
	}
}
	
function escape_notes($notes) {
	return str_replace("\r", "", str_replace("'", "\\'", str_replace("\n", "\\n", str_replace("\"", "\\\"", str_replace("\\", "\\\\", $notes)))));
}
    
function isUserInRole($roleid) {
	for ($i = 0; $i < count($_SESSION['ROLES']); $i++) {
		if ($roleid == $_SESSION['ROLES'][$i]) {
			return true;
		}
	}
	
	return false;
}

function lastIndexOf($string, $item) {
	$index = strpos(strrev($string), strrev($item));

	if ($index) {
		$index = strlen($string) - strlen($item) - $index;
		
		return $index;
		
	} else {
		return -1;
	}
}

function getSiteConfigData() {
	return $_SESSION['SITE_CONFIG'];
}

function redirectWithoutRole($role, $location) {
	start_db();
	initialise_db();
	
	if (! isUserInRole($role)) {
		header("location: $location");
	}
}

function getEmailHeader() {
	return "<img src='" . getSiteConfigData()->domainurl . "/images/logo4.png' />";
}

function getEmailFooter() {
	return getSiteConfigData()->emailfooter;
}

function getPremiumPaymentTerms() {
	return getSiteConfigData()->terms;
}

function getLoggedOnMemberID() {
	start_db();
	
	return $_SESSION['SESS_MEMBER_ID'];
}

function authenticate() {
	start_db();
	initialise_db();
	
	if (! isAuthenticated()) {
		header("location: system-login.php?callback=" . base64_encode($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']));
		exit();
	}
}

function logError($str) {
	global $global_error;
	
	$global_error = true;
	
	sendRoleMessage("ROLE", "An error has occurred.", "<h4>Error<h4><p>" . $str . "</p>");
	
	header("location: oops.php");
}
?>