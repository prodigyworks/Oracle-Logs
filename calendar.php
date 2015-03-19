<?php include("system-header.php"); ?>

<!--  Start of content -->
<link rel="stylesheet" href="css/fullcalendar.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/fullcalendar.print.css" type="text/css" media="all" />

<script type="text/javascript" src="js/fullcalendar.min.js"></script>

<script>
	$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			editable: true,
			
			eventRender: function(event, element) {
			   element.attr('title', "Click to view " + event.title);
			},
			
			eventClick: function(calEvent, jsEvent, view) {

				window.location.href = "viewquote.php?id=" + calEvent.id;
		    },
			
		    dayClick: function(date, allDay, jsEvent, view) {
		
		        // change the day's background color just for fun
		
		    },
			
			events: [
				{
					id: 1,
					title: 'Job CC70-00001',
					allDay: true,
					start: new Date(2012, 05, 29),
					end: new Date(2012, 06, 10),
					className : 'contractor' 
				},
				{
					id: 2,
					title: 'Job CC70-00002',
					allDay: true,
					start: new Date(2012, 05, 23),
					end: new Date(2012, 06, 04)
				}
			]
		});
		
	});
	
	
	
</script>
<div id='calendar'></div>

<!--  End of content -->
<?php include("system-footer.php"); ?>