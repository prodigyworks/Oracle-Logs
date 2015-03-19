<!--  Start of content -->
<div class='chatwindow'>
	<div id="chat" name="chat" ></div>
</div>
<br>
<br>
<label>Your message</label>
<form method='POST' id='messageform'>
	<input type="hidden" id="chatsessionid" name="chatsessionid" value="<?php echo $chatsessionid; ?>" />
	<textarea id="message" name="message" style='width:500px' cols=75 rows=5></textarea>
	<br />
	<br />
	<span class="wrapper"><a href="javascript: chat()" class="link1 rgap5"><em><b>Send</b></em></a></span>
	<span class="wrapper"><a href="javascript: endChat()" class="link1"><em><b>End</b></em></a></span>
</form>
<script>
	var timeOut;
	
	$(document).ready(function() {
			refresh();
			$("#message").focus();
		});
		
	var dataHandler = function(data) {
			$("#chat").html("");
					
			$.each(data, function(i, item) { 
					if (i == 0) {
						if (item.status == "C") {
							$("#chat").attr("disabled", true);
							$("#message").attr("disabled", true);
							$(".link1").attr("disabled", true);
							$(".link1").attr("href", "javascript: void(0)");
						}
					}
					
					var content;
					
					content = "<br><span class='datestamp'>" + item.datestamp + "</span> - ";
					
					if (item.imageid != 0) {
						content = content + "<img src='system-imageviewer?id=" + item.imageid + "' width=16 height=16 />";
					}
					
					content = content + "<span class='username'>" + item.id + " :</span> " + item.name;
					
					$("#chat").html($("#chat").html() + content);
				}); 
				
			$('.chatwindow').attr("scrollTop", 9999999); 
		};
		
	function endChat() {
		clearTimeout(timeOut);
		
		callAjax(
				"requestexpertentry.php", 
				{ 
					message: "Chat session ended",
					command: "END",
					chatsessionid: "<?php echo $chatsessionid; ?>"
				},
				function(data) {
					dataHandler(data);
				},
				false
			);
	}
		
	function chat() {
		callAjax(
				"requestexpertentry.php", 
				{ 
					message: $("#message").val(),
					chatsessionid: "<?php echo $chatsessionid; ?>"
				},
				function(data) {
					$("#message").val("");
					
					dataHandler(data);
				},
				false
			);
	}
		
	function refresh() {
		callAjax(
				"requestexpertrefresh.php", 
				{ 
					chatsessionid: "<?php echo $chatsessionid; ?>"
				},
				dataHandler,
				true
			);
			
		timeOut = setTimeout(refresh, 2000);
	}
</script>
		