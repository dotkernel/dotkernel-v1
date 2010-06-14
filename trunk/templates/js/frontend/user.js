/**
 * Submit add form. Validate it with AJax, using json. 
 * @param {String} formId
 * @param {String} msgBoxId
 * @param {String} url
 */
function formSubmit(formId, msgBoxId, url) 
{	
	// submit the form in the background
	dojo.xhrPost(
	{
	    url: "submit.php",
	    form: formId,
	    handleAs: "json",
	    handle: function(response,args)
		{
			if(typeof response == "error")
			{
			    console.warn("error!",args);
				window.location = url;
			}
			else
			{
			    // show our response
				console.log(response);
			}
			// no error, redirect to home page			
			if(response.error == '' || typeof(response.error) == "undefined")
			{
				window.location = url;
			}
			//display field value that are valid
			for(var i in response.data)
			{
				dojo.byId(i).value = response.data[i];				
			}
			// get the error messages
			var errorMsg = '';
			for(var i in response.error)
			{
				errorMsg += i.toUpperCase()+' - '+response.error[i]+'<br>';
				if(i == 'Secure Image')
				{
					i = 'recaptcha_response_field';
				}
				dojo.byId(i).value = '';
			}
			dojo.byId(msgBoxId).innerHTML = errorMsg;
			dojo.byId('password').value = '';
			dojo.byId('password2').value = '';
	    }
		
	});
	Recaptcha.switch_type('image');
};
