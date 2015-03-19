<?php
	include("system-header.php"); 

	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		
		$qry = "SELECT B.email, B.firstname, B.website, B.lastname, B.imageid, A.* " .
			   "FROM ols_job A " .
			   "INNER JOIN ols_members B " .
			   "ON B.member_id = " . getLoggedOnMemberID() . " " .
			   "WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
?>							
<h4>
<?php
	if ($member['imageid'] != null && $member['imageid'] != 0) {
?>							
	<img height=32 src='system-imageviewer.php?id=<?php echo $member['imageid']; ?>' />
<?php
	}
?>							
	<?php echo " " . $member['firstname'] . " " . $member['imageid']; ?>
</h4>
<br>
<br>
<h2>
	<?php echo $member['title']; ?>
</h2>
<br>
<br>
<hr>
<form class="entryform" method="POST"  enctype="multipart/form-data" action="jobapplysend.php" id="jobform">
	<table cellspacing=10 width=100% class='fixed' id="xx" cellspacing=0 cellpadding=0>
		<tbody>
			<tr>
				<td class='label'>Location</td>
				<td><?php echo $member['location'];?></td>
			</tr>
			<tr>
				<td class='label'>Recruitment Agent</td>
				<td>
					<a href='<?php echo $member['website']; ?>'><?php echo $member['firstname'] . " " . $member['lastname']; ?></a>&nbsp;
<?php
					if ($member['imageid'] != null && $member['imageid'] != 0) {
?>							
						<img height=32 src='system-imageviewer.php?id=<?php echo $member['imageid']; ?>' height=16  />
<?php
					}
?>							
				</td>
			</tr>
			<tr>
				<td class='label'>Reference</td>
				<td><?php echo $member['reference']; ?></td>
			</tr>
			<tr>
				<td class='label'>Posted</td>
				<td><?php echo $member['createddate']; ?></td>
			</tr>
			<tr>
				<td colspan=2>
					<hr>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<label>First name</label>
					<input required="true" type="text" id="firstname" name="firstname" class="textbox20" value="<?php echo $member['firstname']; ?>" />
					
					<label>Last name</label>
					<input required="true"  type="text" id="lastname" name="lastname" class="textbox20" value="<?php echo $member['lastname']; ?>" />
					
					<label>Email</label>
					<input required="true"  type="text" id="email" name="email" class="textbox70" value="<?php echo $member['email']; ?>" />
					
					<label>Contact Number</label>
					<input required="true"  type="text" id="number" name="number" class="textbox20" />
					
					<label>C.V.</label>
					<input required="true"  type="file" id="cv" name="cv" class="textbox90" />
				</td>
			</tr>
			<tr>
				<td>
					<span class="wrapper"><a class='link1' href="javascript:if (verifyStandardForm('#jobform')) $('#jobform').submit();"><em><b>Apply</b></em></a></span>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" id="jobid" name="jobid" value="<?php echo $_GET['id']; ?>" />
</form>
<?php
						}
					} else {
						logError($qry . " = " . mysql_error());
		}
	}

	include("system-footer.php"); 
?>