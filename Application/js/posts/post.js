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
            comments : Dashbird.Comments.construct(postData.comments, me),
            postShares : SimpleJSLib.Observable.construct(postData.postShares),
            text : SimpleJSLib.Observable.construct(postData.text),
            lastView : SimpleJSLib.Observable.construct(postData.lastView)//,
            //isViewed : SimpleJSLib.Observable.construct(!(postData.lastView == null || postData.updated > postData.lastView)),
        }
        me.isFromCurrentUser = Dashbird.User.isCurrentUser(_protected.postData.user.userId);
    };

    // --- catch events ---


    // todo : for later
    // _protected.onLastViewChanged = function(){
    //     _protected.recalulateIsViewed();
    // }

    // _protected.onUpdatedChanged = function(){
    //     _protected.recalulateIsViewed();
    // }

    // --- end ---

    // todo : for later
    // _protected.recalulateIsViewedTriggered = false;
    // _protected.recalulateIsViewed = function(){
    //     if(!_protected.recalulateIsViewedTriggered){
    //         _protected.recalulateIsViewedTriggered = true;
    //         setTimeout(function(){
    //             me.isViewed().set(!(me.getLastView().get() == null ||  me.getUpdated().get() > me.getLastView().get()));
    //             _protected.recalulateIsViewedTriggered = false;
    //         }, 50)
    //     }
    // }


    // --- getters and setters ---
    me.getPostId = function(){
        return _protected.postData.postId;
    }

    // @return Dashbird.Comments
    me.getComments = function(){
        return _protected.postData.comments;
    }

     // @return SimpleJSLib.Óbservable<Date>
    me.getUpdated = function(){
        return _protected.postData.updated;
    }

    // @return SimpleJSLib.Óbservable<Date>
    me.getLastView = function(){
        return _protected.postData.lastView;
    }

    // //@return SimpleJSLib.Óbservable<Boolean>
    // @return Boolean
    me.isViewed = function(){
        return !(me.getLastView().get() == null ||  me.getUpdated().get() > me.getLastView().get());
        //return _protected.postData.isViewed;
    }


    // --- end ---
    
   
    
    me.mergeData = function(data){
        _protected.postData.lastView.set(data.lastView);
        _protected.postData.updated.set(data.updated);
        _protected.postData.tags.set(data.tags);
        _protected.postData.text.set(data.text);
        _protected.postData.comments.mergeData(data.comments);
        _protected.postData.postShares.set(data.postShares);
    }


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
               me.mergeData(ajaxResponse.data);
               me.setLastView();
            }
        });
    };
              
    me.deletePost = function(){
        $.getJSON('api/post/delete/', {
            postId : _protected.postData.postId
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               
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
               me.mergeData(ajaxResponse.data);
               me.setLastView();
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
                  me.mergeData(ajaxResponse.data);
                  me.setLastView();
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
                me.mergeData(ajaxResponse.data.post);
                me.setLastView();
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
                me.mergeData(ajaxResponse.data);
                me.setLastView();

                // // rebuild comments
                // var comments = [];
                // $.each(_protected.postData.comments.get(),function(index, comment){
                //     if(comment.commentId.toString() !== id.toString()){
                //         comments.push(comment);
                //     }
                // });
                // _protected.postData.comments.set(comments);
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