Dashbird.Post = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.postData = null;
    me.isFromCurrentUser = false;

    _protected.construct = function (parameters){
        var postData = parameters[0];
        _protected.postData = {
            postId :  postData.postId,
            created : postData.created,
            user : postData.user,
            updated : SimpleJSLib.Observable.construct(postData.updated),
            tags : SimpleJSLib.Observable.construct(postData.tags),
            comments : SimpleJSLib.Observable.construct(postData.comments),
            postShares : SimpleJSLib.Observable.construct(postData.postShares),
            text : SimpleJSLib.Observable.construct(postData.text)
        }
        me.isFromCurrentUser = Dashbird.User.isCurrentUser(_protected.postData.user.userId);
    };
    
    me.getPostData = function(){
        return _protected.postData;
    };
  
    
    me.update = function(text, tags){
        $.getJSON('api/post/edit/', {
            postId : _protected.postData.postId, 
            text : text,
            tags: tags
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                //Dashbird.Board.fire('post#save', data[AJAX.DATA]);
                // save status
                _protected.postData.tags.set(ajaxResponse.data.tags);
                _protected.postData.text.set(ajaxResponse.data.text);
                _protected.postData.updated.set(ajaxResponse.data.updated);
            }
        });
    };
              
    me.deletePost = function(){
        $.getJSON('api/post/delete/', {
            postId : _protected.postData.postId
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
            // do nothing
            }
        });
        me.undrawPost();                  
    };
    
    me.setPostShares = function(userIds){
        $.getJSON('api/post/shares/set/', {
            postId : _protected.postData.postId, 
            userIds : userIds
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                _protected.postData.postShares = userIds;
                me.drawPostShares();
            }
        });
    }
    

    
    me.addComment = function(text, callback){
        $.getJSON('api/post/comment/add/', {
            postId : _protected.postData.postId, 
            text : text
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                _protected.postData.updated.set(ajaxResponse.data.post.updated);
                _protected.postData.comments.get().push(ajaxResponse.data.comment);
                _protected.postData.comments.trigger();
            }
            if(callback != null){
                callback();
            }
        });
    }

    me.deleteComment = function(id){
        $.getJSON('api/post/comment/delete/', {
            commentId : id
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               _protected.postData.updated.set(ajaxResponse.data.post.updated);
                
//                Dashbird.Board.fire('post#deleteComment', {
//                    post : me,
//                    data : data[AJAX.DATA]
//                })
                // rebuild comments
                var comments = [];
                $.each(_protected.postData.comments.get(),function(index, comment){
                    if(comment.commentId.toString() !== id.toString()){
                        comments.push(comment);
                    }
                });
                _protected.postData.comments.set(comments);

            }
        });
    }
                      
    return me;
});