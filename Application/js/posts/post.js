Dashbird.Post = SimpleJSLib.EventHandler.inherit(function(me, _protected){
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
            text : SimpleJSLib.Observable.construct(postData.text),
            lastView : SimpleJSLib.Observable.construct(postData.lastView)
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
                _protected.postData.lastView.set(ajaxResponse.data.lastView);
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
        me.fireEvent('/post/deleted/', me);            
    };
    
    me.setPostShares = function(userIds){
        $.getJSON('api/post/shares/set/', {
            postId : _protected.postData.postId, 
            userIds : userIds
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                _protected.postData.postShares.set(userIds);
            }
        });
    }
    
    me.setLastView = function(){
        if(_protected.postData.updated.get() !=_protected.postData.lastView.get()){
            $.getJSON('/api/post/lastview/set/', {
               postId : _protected.postData.postId, 
               lastView : _protected.postData.updated.get()
           }, function(data) {
               var ajaxResponse = Dashbird.AjaxResponse.construct(data);
               if(ajaxResponse.isSuccess){
                   _protected.postData.lastView.set(ajaxResponse.data.lastView);
               }
           });
        }
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

    me.deleteComment = function(id, callback){
        $.getJSON('api/post/comment/delete/', {
            commentId : id
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               _protected.postData.updated.set(ajaxResponse.data.post.updated);
                

                // rebuild comments
                var comments = [];
                $.each(_protected.postData.comments.get(),function(index, comment){
                    if(comment.commentId.toString() !== id.toString()){
                        comments.push(comment);
                    }
                });
                _protected.postData.comments.set(comments);
                if(typeof(callback) != 'undefined'){
                    callback(ajaxResponse);
                }

            }
        });
    }
    
    me.isKeywordMatch = function(keyword){
           if(_protected.postData.text.get().indexOf(keyword) !== -1)
                return true;
            
             if(_protected.postData.user.name.indexOf(keyword) !== -1)
                return true;

            var comments = _protected.postData.comments.get();
            for(var i = 0; i < comments.length; i++){
                if(comments[i].text.indexOf(keyword) !== -1)
                     return true;
            }

            var tags = _protected.postData.tags.get();
            for(var k = 0; k < tags.length; k++){
                if(tags[k].indexOf(keyword) !== -1)
                     return true;
            }
            return false
    }
    
    me.isSearchMatch = function(search){
        for(var j = 0; j < search.keywords.length; j++){
            if(me.isKeywordMatch(search.keywords[j])==false){
                return false;
            }
        }
        return true;
    }
                      
    return me;
});