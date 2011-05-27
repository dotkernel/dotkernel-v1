// post the form using ajax
$(document).ready(function(){
	$("#userRegister").submit(function(){
		var $form = $(this);
		$.post(
			window.location,
			$form.serialize(),
			function(result){
				if (typeof result === "object"){
					// an object was returned, so there were errors
					var errorText = "<ul>";
					for (var i in result.error){
						if (result.error.hasOwnProperty(i)){
							errorText += "<strong>" + i + "</strong>: " + result.error[i] +"<br/>";
						}
					}
					errorText += "</li></ul>";
					$("#msgError").html(errorText).show();
				}else{
					// the result isn't an object, so it's probably the contents of
					// user/account, we should redirect
					window.location = SITE_URL + '/user/account';
				}
			}
		)
		return false;
	})
});