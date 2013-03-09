Dashbird.Controllers.Post = SimpleJSLib.BaseObject.inherit(function(me, _protected){

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
    
    // @param post <Dashbird.Models.Post>
    // @param newLastView [optional] <datetime>
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

	return me;
}).construct();