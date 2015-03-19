<?php
	include("system-header.php"); 
	include("confirmdialog.php");
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Confirm request ?", "requestWork");
?>
<script src='js/jquery.ui.timepicker.js' type='text/javascript'></script>

<table class='grid list'>
	<tr>
		<td>
			Job Type
		</td>
		<td>
			<?php createCombo("id", "id", "name", "jomon_jobtype"); ?>
		</td>
	</tr>
	<tr>
		<td>
			Response Date
		</td>
		<td>
			<input type="text" id="responsedate" name="responsedate" class="datepicker" />
		</td>
	</tr>
	<tr>
		<td>
			Response Time
		</td>
		<td>
			<input type="text" id="responsetime" name="responsetime" class="timepicker" />
		</td>
	</tr>
	<tr>
		<td>
			Description
		</td>
		<td>
			<textarea id="description" name="description" cols=80 rows=6></textarea>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<button>Submit</button>
		</td>
	</tr>
</table>
<?php
	include("system-footer.php"); 
?>