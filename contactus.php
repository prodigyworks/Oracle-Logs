<?php include("system-header.php") ?>

<!--  Start of content -->
<script>
function validate_form() {
	if (document.getElementById("titlebox").selectedIndex == 0) {
		alert("Please fill in the 'Title' box");
		return false;
	}
	
	if (document.getElementById("firstnamebox").value == "") {
		alert("Please fill in the 'First Name' box");
		return false;
	}
	
	if (document.getElementById("surnamebox").value == "") {
		alert("Please fill in the 'Last Name' box");
		return false;
	}
	
	if (document.getElementById("originbox").selectedIndex == 0) {
		alert("Please fill in the 'How did you find us' box");
		return false;
	}
	
	if (document.getElementById("userstring").value == "") {
		alert("Please fill in the 'Security code' box");
		return false;
	}
	
	if (document.getElementById("emailbox").value == "" &&
	    document.getElementById("phonebox").value == "") {
		alert("Please fill in either the 'E-mail' or the 'Phone' box");
		return false;
	}
	
	return true;
}
</script>

<h2>Our Contacts</h2>
<div class="pad">
	<div class="wrapper">
		<p class="cols">
			Email:</strong></p> <a href="mailto:info@oraclelogs.co.uk">info@oraclelogs.co.uk</a></p>
		<p><strong>Miscellaneous Info:</strong><br>We will aim to reply to your request within 24 hours.</p>
	</div>
</div>
<h2><span>Contact Form</span></h2>
<FORM action="contactsend.php" method="post" id="contactForm" name="contactForm" onSubmit="return validate_form ();">
	<table cellspacing=10>
		<tr>
			<td>
				<span>Title:</span>
			</td>
			<td>
				<select id="titlebox" name="titlebox" class="input">
					<option value="Select" selected> Select </option>
					<option value="Mr" >Mr</option>
					<option value="Mrs">Mrs</option>
					<option value="Ms">Ms</option>
					<option value="Miss">Miss</option>
					<option value="Dr">Dr</option>
					<option value="Prof">Prof</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<span>First Name:</span>
			</td>
			<td>
				<input type="text" class="input" id="firstnamebox" name="firstnamebox">
			</td>
		</tr>
		<tr>
			<td>
				<span>Surname:</span>
			</td>
			<td>
				<input type="text" class="input" id="surnamebox" name="surnamebox">
			</td>
		</tr>
		<tr>
			<td>
				<span>Phone:</span>
			</td>
			<td>
				<input type="text" class="input" id="phonebox" name="phonebox">
			</td>
		</tr>
		<tr>
			<td>
				<span>E-mail:</span>
			</td>
			<td>
				<input type="text" class="input" id="emailbox" name="emailbox">
			</td>
		</tr>
		<tr>
			<td>
				<span>Your Message:</span>
			</td>
			<td>
				<textarea name="messagebox" id="messagebox" cols="80" rows="5"></textarea>								
			</td>
		</tr>
		<tr>
			<td>
				<span>&nbsp;</span>
			</td>
			<td>
				<img src="imagebuilder.php" border="1">
			</td>
		</tr>
		<tr>
			<td>
				<span>Security Code:</span>
			</td>
			<td>
				<input type="text" class="input" id="userstring" name="userstring" maxlength=8 style="width:100px">
			</td>
		</tr>
		<tr>
			<td>
				<a href="#" class='link1' onClick="document.getElementById('contactForm').submit()"><em><b>Send</b></em></a>
			</td>
		</tr>
	</table>
		
		
</form>
<!--  End of content -->

<?php include("system-footer.php") ?>
