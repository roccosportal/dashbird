/**
 * This model represents a post.
 *
 */
Dashbird.Models.Post = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.postData = null;
    me.isFromCurrentUser = false;
    // constructor
    // @param  parameters (.construct(<object>))
    // [0] plain post data
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
            lastView : SimpleJSLib.Observable.construct(postData.lastView)
        }
        me.isFromCurrentUser = Dashbird.Controllers.User.isCurrentUser(_protected.postData.user.userId);
    };
    // Returns the post id.
    // @return <int>
    me.getPostId = function(){
        return _protected.postData.postId;
    }
    // Returns the comments.
    // @return <Dashbird.Models.Comments>
    me.getComments = function(){
        return _protected.postData.comments;
    }
    // Returns the updated datetime.
    // @return <SimpleJSLib.Observable<datetime>>
    me.getUpdated = function(){
        return _protected.postData.updated;
    }
    // Returns the last view datetime.
    // @return <SimpleJSLib.Observable<datetime>>
    me.getLastView = function(){
        return _protected.postData.lastView;
    }
    // Checks if the post is viewed.
    // @return <boolean>
    me.isViewed = function(){
        return !(me.getLastView().get() == null ||  me.getUpdated().get() > me.getLastView().get());
    }
    // Returns the post shares.
    // @return <SimpleJSLib.Observable<array>>
    me.getPostShares = function(){
        return _protected.postData.postShares;
    }
    // Merges the given data to our data.
    // @param data <object> (plain data object)
    me.mergeData = function(data){
        _protected.postData.lastView.set(data.lastView);
        _protected.postData.updated.set(data.updated);
        _protected.postData.tags.set(data.tags);
        _protected.postData.text.set(data.text);
        _protected.postData.comments.mergeData(data.comments);
        _protected.postData.postShares.set(data.postShares);
    }
    // Destroys the model
    me.destroy = function(){
         me.fireEvent('/post/deleted/', me); 
    }
    // Returns the post data
    // @return <object>
    me.getPostData = function(){
        return _protected.postData;
    };
    // Checks if the keyword matches.
    // @param keyword <string>
    // @return <boolean>
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
        return false;
    }
    // Checks if the search object matches.
    // @param search <object>
    // @return <boolean>
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