<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php 
	//Include database connection details
	require_once('system-config.php');
	require_once("confirmdialog.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Technology Portal</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<link rel="shortcut icon" href="favicon.ico">

<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
<link href="css/dcmegamenu.css" rel="stylesheet" type="text/css" />
<link href="css/skins/white.css" rel="stylesheet" type="text/css" />

<script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/jcarousellite.js" type="text/javascript"></script>
<script src='js/jquery.hoverIntent.minified.js' type='text/javascript'></script>
<script src='js/jquery.dcmegamenu.1.3.3.js' type='text/javascript'></script>
<script src="js/oraclelogs.js" language="javascript" ></script> 
<script type="text/javascript"> 
 
	$(document).ready(function(){
	
		$("a.new_window").attr("target", "_blank");
		
		//carousel
		$(".carousel").jCarouselLite({
			btnNext: ".next",
			btnPrev: ".prev"
		});
	});
		
</script>
<!--[if lt IE 7]>
<script type="text/javascript" src="js/ie_png.js"></script>
<script type="text/javascript">
	ie_png.fix('.png, .carousel-box .next img, .carousel-box .prev img');
</script>
<link href="css/ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body id="page1">
<?php
	createConfirmDialog("passworddialog", "Forgot password ?", "forgotPassword");
	
	if (isset($_POST['command'])) {
		$_POST['command']();
	}
?>
<form method="POST" id="commandForm" name="commandForm">
	<input type="hidden" id="command" name="command" />
	<input type="hidden" id="pk1" name="pk1" />
	<input type="hidden" id="pk2" name="pk2" />
</form>
<div class="tail-top-left"></div>
<div class="tail-top">
<!-- header -->
	<div id="header" class='header1'>
		<div id="chatpanel">
			<label>Experts on line : <label>
			<label class="expertsonline"></label>
		</div>
		<div id="searchpaneldiv">
			<form action="search.php" method="POST" id="searchform">
				<input type="text" id="searchpanel" name="searchpanel" value="<?php if (isset($_POST['searchpanel'])) echo $_POST['searchpanel']; else echo "Search"; ?>"  onfocus="$(this).val('')" />
				<a href="javascript: void(0)" onclick="$('#searchform').submit()">&nbsp;&nbsp;</a>
 			</form>
		</div>
	<?php
		if (! isUserInRole("CONSULTANT")) {
	?>
		<div id="expertpanel">
			<a href="requestexpertnew.php">
				<img id="chatimage" src="images/chat.png" title="Chat with expert" />
			</a>
		</div>
	<?php
		}
	?>
		<?php 
			if (! isAuthenticated()) {
		?>
			<div id='logindialog' class="<?php
						if (! isset($_SESSION['LOGIN_ERRMSG_ARR']) || count($_SESSION['LOGIN_ERRMSG_ARR']) == 0) {
							echo "hide";
						}
					?>">
				<form id='loginform' action="system-login-exec.php" method="post">
					<input type="hidden" id="callback" name="callback" value="<?php if (isset($_GET['callback'])) echo base64_decode($_GET['callback']); else echo "index.php"; ?>" />
					<a id='close' href='#' onclick="javascript: $('#logindialog').hide(); $('#errorlogin').html('');">Close</a>
					<table cellspacing=3>
						<tr>
							<td>User</td>
							<td><input type='text' id='login' name='login' /></td>
						</tr>
						<tr>
							<td>Password</td>
							<td><input type='password' id='password' name='password' /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<img src='images/login-mini.png' onclick="$('#loginform').submit()" />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</td>
						<tr>
							<td>&nbsp;</td>
							<td>
								<a id='register' href='system-register.php'>Register</a>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<a href="javascript:void(0)" onclick="checkForgotPassword()">Forgotten password ?</a>
							</td>
						</tr>
						
					</table>
					<p id='errorlogin'>
						<?php
						if (isset($_SESSION['LOGIN_ERRMSG_ARR'])) {
							for ($i = 0; $i < count($_SESSION['LOGIN_ERRMSG_ARR']); $i++) {
								echo $_SESSION['LOGIN_ERRMSG_ARR'][$i] . "<br>\n";
							}
							
							unset($_SESSION['LOGIN_ERRMSG_ARR']);
						}
						?>
					</p>
				</form>
			</div>
		<div class='login'>
			<div id='loginbutton'></div>
			<script>
				function checkForgotPassword() {
					if ($("#login").val() != "") {
						$("#passworddialog .confirmdialogbody").html("You are about to reset the password.<br>Are you sure ?");
						$("#passworddialog").dialog("open");
						
					} else {
						$("#errorlogin").html("User must be entered for this feature.");
					}
				}
				
				function forgotPassword() {
					$("#loginform").attr("action", "forgotpassword.php");	
					$("#loginform").submit();	
				}
				
				$(document).ready(function() {
					$("#loginbutton").click(function() {
						$("#logindialog").show();
						$("#login").focus();
						
						setTimeout(function() {
									$("#login").focus();
								}, 
								1000
							);
					});
				});
			</script>
		</div>
		<?php		
			} else {
				$qry = "UPDATE ols_members SET " .
						"lastaccessdate = NOW() " .
						"WHERE member_id = " . $_SESSION['SESS_MEMBER_ID'] . "";
				$result = mysql_query($qry);
		?>
		<div id="toppanel">
			<label>logged on: </label>
			<label><a href='profile.php'><?php echo $_SESSION['SESS_FIRST_NAME'] . " " . $_SESSION['SESS_LAST_NAME']; ?></a></label>
		</div>
		<div onclick='window.location.href = "answerexpertrequest.php";' id="chatrequestalert"></div>
		<div onclick='window.location.href = "newservicerequests.php";' id="servicerequestalert"></div>
		<div class='login'>
			<div id='logoutbutton'></div>
			<script>
				$(document).ready(function() {
					$("#logoutbutton").click(function() {
						window.location.href = "system-logout.php";
					});
				});
			</script>
		</div>
		<?php		
			}
		?>
		<script>
			$(document).ready(function() {
				headerDetailRefresh();
			});
			
			var headerPageDataHandler = function(data) {
					$.each(data, function(i, item) {
							$("#answerexpertrequest").html(item.chatrequests);
							$("#answerservicerequest").html(item.servicerequests);
							
						<?php
						if (isUserInRole("CONSULTANT")) {
						?>
							if (parseInt(item.chatrequests) > 0) {
								$("#chatrequestalert").show();
								
							} else {
								$("#chatrequestalert").hide();
							}
							
							if (parseInt(item.servicerequests) > 0) {
								$("#servicerequestalert").show();
								
							} else {
								$("#servicerequestalert").hide();
							}
						<?php
						}
						?>
							if (parseInt(item.expertsonline) == 0) {
								$("#chatimage").attr("src", "images/chat-grey.png");	
								$("#chatimage").attr("title", "No experts available");	
								
							} else {
								$("#chatimage").attr("src", "images/chat.png");	
								$("#chatimage").attr("title", "Chat with expert");	
							}
							
							$(".expertsonline").html(item.expertsonline);
							<?php 
							if (isAuthenticated()) {
							?>
							$("#myarticles").html(item.myarticles);
							$("#myopenquestions").html(item.myopenquestions);
							$("#myansweredquestions").html(item.myansweredquestions);
							$("#awaitingpublish").html(item.awaitingpublish);
							$("#unpublishedquestions").html(item.unpublishedquestions);
							<?php 
							}
							?>
						}); 
				};
				
			function headerDetailRefresh() {
				callAjax(
						"chatinforefresh.php", 
						null,
						headerPageDataHandler,
						true,
						function() {
							/* Ignore stray events. */
						}
					);
					
				setTimeout(headerDetailRefresh, 5000);
			}
			
		</script>
		<div class="row-1">
			<div class="fleft">
				<img src="images/logo4.png" alt="" />
			</div>
			<div class="fright">
				<?php
					showMenu();
				?>
			</div>
		</div>
	</div>
<!-- content -->
	<div id="content">
		<div class="row-1">
		<div class='topad'>
		<?php showAdvert(1, 960, 100); ?>
		</div>
			<div class="inside">
				<div class="container">
					<div class="aside">
						<h3>Our Services</h3>
						<ul>
							<?php 
							if (isAuthenticated()) {
							?>
							<li>
								<img src="images/pic2.gif" alt="" />
								<div class="extra-wrap">
									<span>My Workspace</span>
									<table width='100%'>
										<tr>
											<td>My Open Questions</td>
											<td align=right width=16><a href="myquestions.php" id="myopenquestions"><img src='images/wait.gif' /></a></td>
										</tr>
										<tr>
											<td>My Answered Questions</td>
											<td align=right width=16><a href="myquestions.php" id="myansweredquestions"><img src='images/wait.gif' /></a></td>
										</tr>
										<tr>
											<td>My Articles</td>
											<td align=right width=16><a href="myarticles.php" id="myarticles"><img src='images/wait.gif' /></a></td>
										</tr>
										<tr>
											<td>My Unpublished Articles</td>
											<td align=right width=16><a href="articles.php" id="awaitingpublish"><img src='images/wait.gif' /></a></td>
										</tr>
										<tr>
											<td>My Unpublished Questions</td>
											<td align=right width=16><a href="questions.php" id="unpublishedquestions"><img src='images/wait.gif' /></a></td>
										</tr>
									</table>
								</div>
							</li>
							<?php
							}
							?>
							<li>
								<img src="images/pic1.gif" alt="" />
								<div class="extra-wrap">
									<span>Technical Help</span>
									<table width='100%'>
										<tr>
											<td><a href="requestexpertnew.php">Experts Online</a></td>
											<td align=right width=16><a href="requestexpertnew.php" class="expertsonline"><img src='images/wait.gif' /></a></td>
										</tr>
										<?php
										if (isUserInRole("CONSULTANT")) {
										?>
										<tr>
											<td><a href="answerexpertrequest.php">Chat Requests Waiting</a></td>
											<td align=right><a href="answerexpertrequest.php" id="answerexpertrequest"><img src='images/wait.gif' /></a></td>
										</tr>
										<tr>
											<td><a href="answerexpertrequest.php">Service Requests Waiting</a></td>
											<td align=right><a href="newservicerequests.php" id="answerservicerequest"><img src='images/wait.gif' /></a></td>
										</tr>
										<?php
										}
										?>
									</table>
								</div>
							</li>
							<li>
								<div class='leftad'>
									<?php showAdvert(2, 167, 400); ?>
								</div>
							</li>
						</ul>
					</div>
					<div class="content">
					<?php
						BreadCrumbManager::showBreadcrumbTrail();
					?>
					<hr>
			