<?php 
	include("system-header.php"); 
	
	function delete() {
		$id = $_POST['pk1'];
		$qry = "DELETE FROM jomon_messages WHERE id = $id";
		$result = mysql_query($qry);
	}
	
	function setPortletState($title) {
		if (isset($_SESSION["PORTLET_" . $title]) && $_SESSION["PORTLET_Premises"] == "hide") {
			echo "<img title='Show' src='images/maximize.png' onclick='showHide(this)' />\n";
			echo '<table width="100%" class="grid hide" id="sitelist" maxrows=0 width=100% cellspacing=0 cellpadding=0>';
			
		} else {
			echo "<img title='Hide' src='images/minimize.png' onclick='showHide(this)' />\n";
			echo '<table width="100%" class="grid" id="sitelist" maxrows=0 width=100% cellspacing=0 cellpadding=0>';
		}
	} 	
	
?>
<table width='100%' class='portlet' cellspacing=3>
	<tr>
		<td width='50px'>
			<img src='images/house.png' />
		</td>
		<td class='content'>
			<h3>Premises</h3>
			<?php 	
				if (isset($_SESSION['PORTLET_Premises']) && $_SESSION['PORTLET_Premises'] == "hide") {
					echo "<img title='Show' src='images/maximize.png' onclick='showHide(this)' />\n";
					echo '<table width="100%" class="grid hide" id="sitelist" maxrows=0 width=100% cellspacing=0 cellpadding=0>';
					
				} else {
					echo "<img title='Hide' src='images/minimize.png' onclick='showHide(this)' />\n";
					echo '<table width="100%" class="grid" id="sitelist" maxrows=0 width=100% cellspacing=0 cellpadding=0>';
				}
			?>
				<thead>
					<tr>
						<td>Name</td>
						<td>Street</td>
						<td>Town</td>
						<td>City</td>
						<td>County</td>
						<td>Post code</td>
						<td>Tel</td>
						<td>Fax</td>
					</tr>
				</thead>
				<?php
					$memberid = $_SESSION['SESS_MEMBER_ID'];
					$qry = "SELECT * FROM jomon_sites where memberid = $memberid ORDER by name";
					$result = mysql_query($qry);
					$found = false;
					
					//Check whether the query was successful or not
					if ($result) {
						while (($member = mysql_fetch_assoc($result))) {
							echo "<tr>\n";
							echo "<td><a href='customersites.php?id=" . $member['id'] . "'>" . $member['name'] . "</a></td>\n";
							echo "<td>" . $member['address1'] . "</td>\n";
							echo "<td>" . $member['address2'] . "</td>\n";
							echo "<td>" . $member['address3'] . "</td>\n";
							echo "<td>" . $member['address4'] . "</td>\n";
							echo "<td>" . $member['postcode'] . "</td>\n";
							echo "<td>" . $member['telephone'] . "</td>\n";
							echo "<td>" . $member['fax'] . "</td>\n";
							echo "</tr>\n";
							
							$found = true;
						}
					}
					
					if (! $found) {
					?>
						<h4 style'float:left'><p>No premises have been added.</p></h4>
						<h5 style='margin-top: -30px; float:right'><a href="customersites.php">Click here to add premises and contacts</a></h5>
					<?php
					}
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width='50px'>
			<img src='images/mail.png' />
		</td>
		<td class='content'>
			<h3>Messages</h3>
			<?php 	
				if (isset($_SESSION['PORTLET_MESSAGES']) && $_SESSION['PORTLET_MESSAGES'] == "hide") {
					echo "<img title='Show' src='images/maximize.png' onclick='showHide(this)' />\n";
					
				} else {
					echo "<img title='Hide' src='images/minimize.png' onclick='showHide(this)' />\n";
				}
			
				showMessages();
			?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width='50px'>
			<img src='images/tasks.png' />
		</td>
		<td class='content'>
			<h3>Tasks</h3>
			<?php 	
				if (isset($_SESSION['PORTLET_TASKS']) && $_SESSION['PORTLET_TASKS'] == "hide") {
					echo "<img title='Show' src='images/maximize.png' onclick='showHide(this)' />\n";
					
				} else {
					echo "<img title='Hide' src='images/minimize.png' onclick='showHide(this)' />\n";
				}
			?>
			<?php 	
				showMessages();
			?>
		</td>
	</tr>
</table>
<script>
	function showHide(widget) {
		var setting;
		
		if ($(widget).attr("src") == "images/maximize.png") {
			$(widget).attr("src", "images/minimize.png");
			$(widget).attr("title", "Hide");
			$(widget).next().show();
			setting = "show";
			
		} else {
			$(widget).attr("src", "images/maximize.png");
			$(widget).attr("title", "Show");
			$(widget).next().hide();
			setting = "hide";
		}

		callAjax(
				"updateportletsetting.php", 
				{
					item: setting,
					title: $(widget).prev().html()
				}
			);
					
	}
</script>

<?php 
	include("system-footer.php"); 
?>
