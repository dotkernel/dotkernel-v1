function adminList(siteUrl, id, isActive, page) {
        //Look up the node we'll stick the text under.
        var targetNode = dojo.byId("adminList");
        //The parameters to pass to xhrGet, the url, how to handle it, and the callbacks.
        var xhrArgs = {
            url: siteUrl+"/admin/admin/activate/",
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
    

