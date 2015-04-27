<h2>This is a mobile compatible page !</h2>
<ul data-role="listview" data-inset="true"  data-dividertheme="b">
	<li data-role="list-divider">External Link(not mobile compatible)</li>
	<li><a href="http://www.dotkernel.com/" target="_blank" >DotKernel PHP Application Framework</a></li>
</ul>
<div class="ui-body ui-body-d">
	<h3>This is an example of a contact form</h3>
	<h2>{ERROR_MESSAGE}</h2>
	<div class="ui-body ui-body-e">
	<form id="contactForm" action="{SITE_URL}/mobile/" method="post" >
		<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
			<label for="email">Email:</label>
			<input class="ui-input-text ui-body-null ui-corner-all ui-body-c required email"
				name="email" id="email" value="{EMAIL}" type="email">
			<br/><span id="emailError"  style="color: #ff0000;"></span>
		</div>
		<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
			<label for="phone">Phone (only US country):</label>
			<input class="ui-input-text ui-body-null ui-corner-all ui-body-c required" 
				name="phone" id="phone" value="{PHONE}" type="text">
			<br/>
		</div>

		<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
			<label for="textarea">Message:</label>
			<textarea class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c required" 
				cols="40" rows="8" name="message" id="message">{MESSAGE}</textarea>
			<br />
			<span id="messageError" style="color: #ff0000;"></span>
		</div>
		<div class="ui-block-b">
			<button class="ui-btn-active" type="submit">Submit</button>
		</div>
	</form>
	</div>
</div>
<script>
	$("#contactForm").submit(function()
	{
		return validateSubmit();
	});
</script>
