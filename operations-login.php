<?php 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	createConfirmDialog("confirmdialog", "Forgot password ?", "forgotPassword");
?>
<style>
	.loginerror {
		position: absolute;
		margin-left: 180px;
		margin-top: -10px;
		color: red;
		font-style: italic;
	}
</style>
<!--  Start of content -->
<p align="center">&nbsp;</p>
		<?php
			if (! isAuthenticated()) {
		?>
		<div class="modal" id="dialog">
		<?php
			if (isset($_SESSION['ERRMSG_ARR'])) {
				echo "<div class='loginerror'>\n";
				echo "<img src='images/alert.png' />";
				
				for ($i = 0; $i < count($_SESSION['ERRMSG_ARR']); $i++) {
					echo "<p>" . $_SESSION['ERRMSG_ARR'][$i] . "</p>";
				}

				echo "</div>";
			}
		?>
			<form action="system-login-exec.php" method="post" id="loginForm">
				<div><label>User name</label></div>
				<input type="text" name="login" id="login" value="<?php if (isset($_SESSION['ERR_USER'])) echo $_SESSION['ERR_USER']; ?>"/>
				<br/>
				<br/>
				<input type="hidden" id="callback" name="callback" value="index.php" />
				<input type="hidden" id="roleid" name="roleid" value="OPERATIONS" />
				<div><label>password</label></div>
				<input type="password" name="password" id="password" value="" />
				<br />
				<br>
				<a href="javascript:void(0)" onclick="checkForgotPassword()">Forgotten password ?</a>
				<br>
				<img src="images/login.png" style="float:right; margin-top: 73px" onclick="$('#loginForm').submit()"/>
				<img src="images/loginlogo.png" style="float:left"  />
			</form>
			<script>
				document.onkeypress = changeHREF;
				function changeHREF(ev) {
					ev = ev || event;
					
					if (ev.keyCode == 13) {
						$('#loginForm').submit();
					}
				}

			</script>
		</div>
		
		<script>
			function checkForgotPassword() {
				if ($("#login").val() != "") {
					$("#confirmdialog .confirmdialogbody").html("You are about to reset the password.<br>Are you sure ?");
					$("#confirmdialog").dialog("open");
				}
			}
			
			function forgotPassword() {
				$("#loginForm").attr("action", "forgotpassword.php");	
				$("#loginForm").submit();	
			}
			
			$(document).ready(function() {
					$("#login").change(
							function() {
								$(".loginerror").hide();
							}
						);
						
					$("#dialog").dialog({
							modal: true,
							closeOnEscape: false,
							dialogClass: 'login-dialog',
							beforeClose: function() { return false; }
						});
				});
			
		</script>
				
		<?php
			}
			
			unset($_SESSION['ERRMSG_ARR']);
			unset($_SESSION['ERR_USER']);
		?>
<!--  End of content -->

<?php include("system-footer.php"); ?>					
