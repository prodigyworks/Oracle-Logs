<?php
	require_once("system-db.php"); 
	
	start_db();
	initialise_db();
	
	if (! isUserInRole("CONSULTANT")) {
		redirectWithoutRole("PREMIUM", "recruitmentpremiummember.php");
	}
	
	require_once("system-header.php"); 
	require_once("tinymce.php");
?>
<script type='text/javascript' src='jsc/jquery.autocomplete.js'></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"><;/script>
<script src="http://maps.google.com/maps/api/js?v=3.1&sensor=false&region=PH"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo getSiteConfigData()->googlemapv2apikey; ?>" type="text/javascript"></script>
<script type="text/javascript">
	var directionsService = new google.maps.DirectionsService();
	
	function getLatLng(address)  {
	    var geocoder = new google.maps.Geocoder();
	    
	    geocoder.geocode(
	    		{ 
	    			'address' : address 
	    		}, 
	    		function( results, status ) {
			        if (status == google.maps.GeocoderStatus.OK ) {
						$('#lat').val(results[0].geometry.location.lat())
						$('#lng').val(results[0].geometry.location.lng())
						
			        } else {
			            alert( "Geocode was not successful for the following reason: " + status );
			        }
			    }
			);            
	}
	
	$(document).ready(function() {
			$("#location").change(function() {
					setTimeout(
							function() { 
								getLatLng($("#location").val());
							},
							500
						);
				});
		});
	
	function initialize() {
	    var input = document.getElementById('location');
        var options = {
        		types: ['(cities)'],           
        		componentRestrictions: {country: ["uk"]}       
        	};

	    var autocomplete = new google.maps.places.Autocomplete(input, options);
	}
	
  	google.maps.event.addDomListener(window, 'load', initialize);
</script>
<div id="jobsearch">
	<form action="postjobsave.php" method="POST" id="jobform" class="entryform">
		<label>Title</label>
		<input required="true" type="text" id="title" name="title" class="textbox90" />
		
		<label>Type</label>
		<SELECT required="true" id="type" name="type">
			<OPTION value="P">Permanent</OPTION>
			<OPTION value="C">Contract</OPTION>
		</SELECT>
		
		<label>Location</label>
		<input required="true" type="text" id="location" name="location" class="textbox70" />
		
		<label>Reference</label>
		<input required="true" type="text" id="ref" name="ref" class="textbox90" />
		
		<label>Currency</label>
		<SELECT required="true" id="currency" name="currency">
			<OPTION value="GBP">GBP</OPTION>
			<OPTION value="USD">USD</OPTION>
			<OPTION value="EUR">EURO</OPTION>
		</SELECT>
		
		<label>Salary Range</label>
		<SELECT id="salary" name="salary">
			<OPTION value=""></OPTION>
			<OPTION value="ZEROTOTEN">0 - 10,000</OPTION>
			<OPTION value="TENTOTWENTY">10,000 - 20,000</OPTION>
			<OPTION value="TWENTYTOTHIRTY">20,000 - 30,000</OPTION>
			<OPTION value="THIRTYTOFORTY">30,000 - 40,000</OPTION>
			<OPTION value="FORTYTOFIFTY">40,000 - 50,000</OPTION>
			<OPTION value="FIFTYTOHUNDRED">50,000 - 100,000</OPTION>
			<OPTION value="HUNDREDPLUS">100,000+</OPTION>
		</SELECT>
		
		<label>Rate (Per)</label>
		<SELECT id="rateper" name="rateper">
			<OPTION value=""></OPTION>
			<OPTION value="HOUR">Hour</OPTION>
			<OPTION value="WEEK">Week</OPTION>
			<OPTION value="MONTH">Month</OPTION>
			<OPTION value="YEAR">Year</OPTION>
		</SELECT>
		
		<label>Rate Range</label>
		<SELECT id="rate" name="rate">
			<OPTION value=""></OPTION>
			<OPTION value="ZEROTOTEN">0 - 10</OPTION>
			<OPTION value="TENTOTWENTY">10 - 20</OPTION>
			<OPTION value="TWENTYTOTHIRTY">20 - 30</OPTION>
			<OPTION value="THIRTYTOFORTY">30 - 40</OPTION>
			<OPTION value="FORTYTOFIFTY">40 - 50</OPTION>
			<OPTION value="FIFTYTOHUNDRED">50 - 100</OPTION>
			<OPTION value="HUNDREDPLUS">100+</OPTION>
		</SELECT>
		
		<label>Description</label>
		<textarea id="description" name="description" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>
		<br>
		<br>
		<input type="hidden" id="lat" name="lat" value="" />
		<input type="hidden" id="lng" name="lng" value="" />
		
	  	<span class="wrapper"><a class='link1' href="javascript:if (verifyStandardForm('#jobform')) $('#jobform').submit();"><em><b>Post Job</b></em></a></span>
	</form>
</div>
<?php
	include("system-footer.php"); 
?>