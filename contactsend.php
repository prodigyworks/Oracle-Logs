<?php include("system-header.php") ?>

<!--  Start of content -->
<h2>Our Contacts</h2>

<div class="pad">
<?php
/*
============================
QuickCaptcha 1.0 - A bot-thwarting text-in-image web tool.
Copyright (c) 2006 Web 1 Marketing, Inc.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
============================
See settings.php for common settings. You shouldn't need to change
anything in this file.
============================
*/
include "settings.php";

$string = strtoupper($_SESSION['string']);
$userstring = strtoupper($_POST['userstring']);
session_destroy();

if (($string == $userstring) && (strlen($string) > 4)) {
//	header("Location: $success");
	echo "<P class='actionMessage'>Thank you for your registration.</P>";
	echo "<H4>We will contact you shortly.</H4>";

	if (isset($_POST["emailbox"]) && $_POST["emailbox"] != "") {
		mail(
				"info@oraclelogs.co.uk",
				"Information request",
				"Phone : " . $_POST["phonebox"] . "\n" . "\n" . $_POST["messagebox"] . "\n",
				"from: " . $_POST["titlebox"] . " " . $_POST["firstnamebox"] . " " . $_POST["surnamebox"] . "<" . $_POST["emailbox"] . ">"
			);

	} else {
		mail(
				"info@oraclelogs.co.uk",
				"Information request",
				"Phone : " . $_POST["phonebox"] . "\n" . "\n" . $_POST["messagebox"] . "\n",
				"from: " . $_POST["titlebox"] . " " . $_POST["firstnamebox"] . " " . $_POST["surnamebox"]
			);
	}

} else {
	// What happens when the CAPTCHA was entered incorrectly
	echo "<P class='actionError'>The security code was invalid, please go back and try again.</P>";
}
?>
</div>
<!--  End of content -->

<?php include("system-footer.php") ?>
