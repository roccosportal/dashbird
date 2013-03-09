Dashbird.Models.Post = SimpleJSLib.EventHandler.inherit(function(me, _protected){
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
            comments : Dashbird.Models.Comments.construct(postData.comments, me),
            postShares : SimpleJSLib.Observable.construct(postData.postShares),
            text : SimpleJSLib.Observable.construct(postData.text),
            lastView : SimpleJSLib.Observable.construct(postData.lastView)//,
            //isViewed : SimpleJSLib.Observable.construct(!(postData.lastView == null || postData.updated > postData.lastView)),
        }
        me.isFromCurrentUser = Dashbird.Controllers.User.isCurrentUser(_protected.postData.user.userId);
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

    // @return Dashbird.Models.Comments
    me.getComments = function(){
        return _protected.postData.comments;
    }

     // @return SimpleJSLib.Observable<Date>
    me.getUpdated = function(){
        return _protected.postData.updated;
    }

    // @return SimpleJSLib.Observable<Date>
    me.getLastView = function(){
        return _protected.postData.lastView;
    }

    // //@return SimpleJSLib.Observable<Boolean>
    // @return Boolean
    me.isViewed = function(){
        return !(me.getLastView().get() == null ||  me.getUpdated().get() > me.getLastView().get());
        //return _protected.postData.isViewed;
    }

    // @return SimpleJSLib.Observable<array>
    me.getPostShares = function(){
        return _protected.postData.postShares;
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

    me.destroy = function(){
         me.fireEvent('/post/deleted/', me); 
    }


    me.getPostData = function(){
        return _protected.postData;
    };
  
    me.isKeywordMatch = function(keyword){
           if(_protected.postData.text.get().indexOf(keyword) !== -1)
                return true;
            
             if(_protected.postData.user.name.indexOf(keyword) !== -1)
                return true;

            var comments = me.getComments().each(function(index, comment){
                if(comment.isKeywordMatch(keyword))
                     return true;
            });
           

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