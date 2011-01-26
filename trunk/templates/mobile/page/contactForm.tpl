<div class="ui-body ui-body-d">
	<h3>This is an example of a contact form</h3>
	<span style="color: #ff0000;"><h2>{ERROR_MESSAGE}</h2></span>	
	<div class="ui-body ui-body-e">
	<form id="contactForm" action="{SITE_URL}/mobile/page/contact" method="post" >
	<input type="hidden" name="send" value="on">
		<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
	         <label class="ui-input-text" for="name">Email:</label>
	         <input class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c required email" 
				 name="email" id="email" value="" type="text">
			 <br /><span id="emailError"  style="color: #ff0000;"></span>
			</div>

		<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
			<label class="ui-input-text" for="textarea">Message:</label>
			<textarea class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c required" 
				cols="40" rows="8" name="message" id="message"></textarea>
			<br /><span id="messageError" style="color: #ff0000;"></span>
		</div>
		<div class="ui-block-b">			
			<button type="submit" data-theme="a">Submit</button>
		</div>
	</form>	
	</div>
</div>
<script>
	$("form").submit(function() 
	{
		return validateSubmit();		
    });	
</script>