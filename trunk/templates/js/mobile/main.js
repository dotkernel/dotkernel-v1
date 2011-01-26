/**
 * Validate Email - with regular expression
 * @param {String} value
 * @return bool
 */
function validateEmail(value)
{
	var regExp = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])","");
    return regExp.test(value);

}
/**
 * Validate message - required field
 * @param {String} value
 * @return bool
 */
function validateMessage(value)
{
	if(value) 
	{
       return true;
   	}
   	else 
	{
       return false;
   	}

}
/**
 * Validate the form before submitting
 * @return bool
 */
function validateSubmit()
{
	var email = $("#email").val();
	var message = $("#message").val();
	var error = 0;
	$("#emailError").text("").show();
	$("#messageError").text("").show();	
	if(!validateEmail(email))
	{
		 $("#emailError").text("Email is not valid!").show();
		 error = 1;
	}
	if(!validateMessage(message))
	{
		$("#messageError").text("Message is required").show();
		error = 1;
	}
	if(error)
	{
		return false;
	}
	else
	{
		return true;
	}
	
}
