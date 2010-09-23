/**
 * Ajax function for changing the active field of admin user and then display again the admin list updated
 * @param {String} siteUrl - url to be called for the request
 * @param {Int} id - admin user ID
 * @param {Int} isActive - only values (0,1)
 * @param {Int} page - page number
 */
function adminList(siteUrl, id, isActive, page) 
{
    //Look up the node we'll stick the text under.
    var targetNode = dojo.byId("adminList");
    //The parameters to pass to xhrPost, the url, how to handle it, and the callbacks.
    var xhrArgs = {
        url: siteUrl,
        handleAs: "text",
      	 content: {
            id: id,
            isActive: isActive,
            page: page
        },
        load: function(data) {
            targetNode.innerHTML = data;
        },
        error: function(error) {
            targetNode.innerHTML = "An unexpected error occurred: " + error;
        }
    }
    //Call the asynchronous xhrPost
    var deferred = dojo.xhrPost(xhrArgs);
}
/**
 * Ajax function for filter the logins list
 * @param {Object} siteUrl
 * @param {Object} page
 */
function adminLogins(siteUrl, page, browser, loginDate)
{
	var dateLogin = ''	
	if (loginDate != null) {
		dojo.require("dojo.date.locale");
		dateLogin = dojo.date.locale.format(loginDate, {
			selector: "date",
			datePattern: "yyyy-MM-dd"
		});
	}	
	window.location = siteUrl+'/page/1/browser/'+browser+'/loginDate/'+dateLogin;
}
