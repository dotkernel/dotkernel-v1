/**
 * Send _POST add fields through ajax
 * @param {String} siteUrl
 * @param {int} page
 */
function adminAddTransporter(siteUrl, page) 
{
    //Look up the node we'll stick the text under.
    var targetNode = dojo.byId("adminList");
    //The parameters to pass to xhrPost, the url, how to handle it, and the callbacks.
    var xhrArgs = {
        url: siteUrl,
        handleAs: "text",
        form: dojo.byId("transporterAdd"),
         content: {
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
    
    return false;
}
