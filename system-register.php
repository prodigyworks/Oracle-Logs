<?php include("system-header.php"); ?>

<!--  Start of content -->
<?php
if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
?>
<div id="errorwindow">
	<?php showErrors(); ?>
</div>
<?php
}
?>
<div class="registerPage">
	<form id="loginForm" enctype="multipart/form-data" name="loginForm" class="entryform" method="post" action="system-register-exec.php">
	  <table border="0" align="left" cellpadding="2" cellspacing="5">
	    <tr>
	      <td>First Name </td>
	      <td><input required="true" name="fname" type="text" class="textfield" id="fname" /></td>
	    </tr>
	    <tr>
	      <td>Last Name </td>
	      <td><input required="true" name="lname" type="text" class="textfield" id="lname" /></td>
	    </tr>
	    <tr>
	      <td>Login</td>
	      <td><input required="true" name="login" type="text" class="textfield logintext" id="login" /></td>
	    </tr>
	    <tr>
	      <td>Email</td>
	      <td><input required="true" name="email" type="text" class="textfield60" id="email" /></td>
	    </tr>
	    <tr>
	      <td>Confirm Email</td>
	      <td><input required="true" name="confirmemail" type="text" class="textfield60" id="confirmemail" /></td>
	    </tr>
	    <tr>
	      <td>Web Site</td>
	      <td><input name="website" type="text" class="textfield90" id="website" /></td>
	    </tr>
	    <tr>
	      <td>Image</td>
	      <td><input name="image" type="file" class="textfield60" id="image" /></td>
	    </tr>
	    <tr>
	      <td>Account Type</td>
	      <td>
	      	  <SELECT id="accounttype" name="accounttype">
	      	  	<OPTION value="USER">User</OPTION>
	      	  	<OPTION value="RECRUITER">Recruiter</OPTION>
	      	  	<OPTION value="CONSULTANT">Consultant</OPTION>
	      	  	<OPTION value="ADVERTISER">Advertiser</OPTION>
	      	  </SELECT>
	      </td>
	    </tr>
	    <tr>
	    	<td colspan="2">
	    		<br />
	    		<h3>Security</h3>
	    		<hr />
	    	</td>
	    </tr>
	    <tr>
	      <td>Password</td>
	      <td>
	      	<input required="true" name="password" type="password" class="textfield pwd" id="password" />
	      </td>
	    </tr>
	    <tr>
	      <td>Confirm Password </td>
	      <td><input required="true" name="cpassword" type="password" class="textfield" id="cpassword" /></td>
	    </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td>
	  		<span class="wrapper"><a class='link1' href="javascript:if (verify()) $('#loginForm').submit();"><em><b>Submit</b></em></a></span>
	      </td>
	    </tr>
	  </table>
	  <input type="hidden" id="description" name="description" value="Profile image" />
	</form>
</div>
<script>
	$(document).ready(function() {
		$(".pwd").blur(verifypassword);
		$(".logintext").blur(checkLogin);
		$("#email").blur(checkEmail);
		$("#cpassword").blur(verifycpassword);
		$("#confirmemail").blur(verifycemail);
		$("#fname").focus();
	});
	
	function verify() {
		var isValid = verifyStandardForm('#loginForm');
		
		if (! verifypassword()) {
			isValid = false;
		}
		
		if (! verifycpassword()) {
			isValid = false;
		}
		
		if (! checkLogin()) {
			isValid = false;
		}
		
		if (! checkEmail()) {
			isValid = false;
		}
		
		if (! verifycemail()) {
			isValid = false;
		}
		
		return isValid;
	}
	
	function verifypassword() {
		var node = $(".pwd");
		var str = $(node).val();
		
		if( str.match(/(?=.*\d{2,})(?=.*[a-zA-Z])/)) {
			$(node).removeClass("invalid");
			$(node).next().css("visibility", "hidden");
			$(node).next().attr("title", "Required field.");
			
			return true;
			
		} else {
			$(node).addClass("invalid");
			$(node).next().css("visibility", "visible");
			$(node).next().attr("title", "The password entered does not meet the minimum requirements. Passwords should be at least 6 characters in length and contain a minimum of two digits.");
			
			return false;
		}
	}
	
	function verifycpassword() {
		var node = $("#cpassword");
		var str = $(node).val();
		
		if ($(node).val() == "") {
			return false;
		}
		
		if( str == $(".pwd").val()) {
			$(node).removeClass("invalid");
			$(node).next().css("visibility", "hidden");
			$(node).next().attr("title", "Required field.");
			
			return true;
			
		} else {
			$(node).addClass("invalid");
			$(node).next().css("visibility", "visible");
			$(node).next().attr("title", "Passwords do not match.");
			
			return false;
		}
	}
	
	function checkLogin() {
		var node = $(".logintext");
		var returnValue = true;
		
		if ($(node).val() == "") {
			return false;
		}
		
		callAjax(
				"finduser.php", 
				{ 
					login: $(".logintext").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Login is already in use.");
						
						returnValue = false;
						
					} else {
						$(node).removeClass("invalid");
						$(node).next().css("visibility", "hidden");
						$(node).next().attr("title", "Required field.");
					}
				},
				false
			);
			
		return returnValue;
	}
	
	function checkEmail() {
		var node = $("#email");
		var returnValue = true;
		
		if ($(node).val() == "") {
			return false;
		}
		
		callAjax(
				"findemail.php", 
				{ 
					email: $("#email").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Email address is already in use.");
						
						returnValue = false;
						
					} else {
						$(node).removeClass("invalid");
						$(node).next().css("visibility", "hidden");
						$(node).next().attr("title", "Required field.");
					}
				},
				false
			);
			
		return returnValue;
	}
	
	function verifycemail() {
		var node = $("#confirmemail");
		var str = $(node).val();
		
		if ($(node).val() == "") {
			return false;
		}
		
		if( str == $("#email").val()) {
			$(node).removeClass("invalid");
			$(node).next().css("visibility", "hidden");
			$(node).next().attr("title", "Required field.");
			
			return true;
			
		} else {
			$(node).addClass("invalid");
			$(node).next().css("visibility", "visible");
			$(node).next().attr("title", "Email addresses do not match.");
			
			return false;
		}
	}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
