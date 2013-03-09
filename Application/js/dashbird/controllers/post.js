/**
 *  The Post controller updates and removes all post relevant data on the server.
 *  The Post controller is a singelton.
 */
Dashbird.Controllers.Post = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    // Updates the text and tags from a post on the server and updates the relating post model in the client application.
    // Sets the post automitically to viewed, assuming the user noticed that he changed the data.
	// @param post <Dashbird.Models.Post>
	// @param text <string>
	// @param tags <array>
	me.updatePost = function(post, text, tags){
        $.getJSON('api/post/edit/', {
            postId : post.getPostId(), 
            text : text,
            tags: tags
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               post.mergeData(ajaxResponse.data);
               me.setLastView(post);
            }
        });
    };
    // Deletes a post on the server and destroyes the relating post model in the client application.
    // @param post <Dashbird.Models.Post>     
    me.deletePost = function(post){
        $.getJSON('api/post/delete/', {
            postId : post.getPostId()
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               
            }
        });
        post.destroy();
    };
    // Sets the post shares for a post on the server and updates the relating post model in the client application.
    // Sets the post automitically to viewed, assuming the user noticed that he changed the data.
    // @param post <Dashbird.Models.Post>
    // @param userIds <array>
    me.setPostShares = function(post, userIds){
        $.getJSON('api/post/shares/set/', {
            postId : post.getPostId(), 
            userIds : userIds
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               post.mergeData(ajaxResponse.data);
               me.setLastView(post);
            }
        });
    }
    // Sets the last view for a post on the server and updates the relating post model in the client application.
    // @param post <Dashbird.Models.Post>
    // @param newLastView [optional] <datetime> By default it is the updated time from the post
    me.setLastView = function(post, newLastView){
        if(typeof(newLastView) === 'undefined')
            newLastView = post.getUpdated().get();

        if(newLastView != post.getLastView().get()){
            // set it first on local side so there is an instant change
            post.getLastView().set(newLastView)

            $.getJSON('/api/post/lastview/set/', {
               postId : post.getPostId(), 
               lastView : newLastView
           }, function(data) {
               var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
               if(ajaxResponse.isSuccess){
                  post.mergeData(ajaxResponse.data);
               }
           });
        }
    }
    // Adds a comment to a post on the server and updates the relating post model in the client application.
    // Sets the post automitically to viewed, assuming the user noticed that he changed the data.
    // @param post <Dashbird.Models.Post>
    // @param text <string>
    // @param callback [optional] <function>
    me.addComment = function(post, text, callback){
        $.getJSON('api/post/comment/add/', {
            postId :post.getPostId(), 
            text : text
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                post.mergeData(ajaxResponse.data.post);
                me.setLastView(post);
            }
            if(callback != null){
                callback();
            }
        });
    }
    // Deletes a comment from a post and updates the relating post model in the client application.
    // Sets the post automitically to viewed, assuming the user noticed that he changed the data.
 	// @param post <Dashbird.Models.Post>
    // @param id <int>
    // @param callback [optional] <function>
    me.deleteComment = function(post, id, callback){
        $.getJSON('api/post/comment/delete/', {
            commentId : id
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                post.mergeData(ajaxResponse.data);
                me.setLastView(post);
                if(typeof(callback) != 'undefined'){
                    callback(ajaxResponse);
                }

            }
        });
    }
    // Adds a post on the server and creates a relating post model in the client application.
    // Sets the post automitically to viewed, assuming the user noticed that he changed the data.
    // @param text <string>
    // @param tags <array>
    // @param postShares <array>
    // @param callback [optional] <function(<Dashbird.Models.Post>)>
    me.addPost = function(text, tags, postShares, callback){
        $.getJSON('api/post/add/', {
            text : text,
            tags :  tags,
            shares : postShares
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = Dashbird.Controllers.Posts.mergePostDatas([ajaxResponse.data]);
                me.setLastView(result.posts[0]);
                if(typeof(callback) != 'undefined'){
                    callback(result.posts[0]);
                }
            }
        });
    }
	return me;
}).construct();