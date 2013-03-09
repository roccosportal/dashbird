Dashbird.Views.Board.Notification = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.postList = [];
    _protected.originalTitle = null;
  
    me.init = function(){
         _protected.originalTitle = $('title').text();
        Dashbird.Controllers.Posts.attachEvent('/posts/new/', _protected.onNewPosts);
        Dashbird.Controllers.Posts.attachEvent('/posts/merged/', _protected.onPostChange);
    };
    
    _protected.onNewPosts = function(result){
       for(var i = 0; i <  result.newPosts.length; i++){
           _protected.attachEvents(result.newPosts[i]);
           _protected.postList.push(result.newPosts[i]);
       }
       _protected.recalculateNotificationCount();
    }
    
    _protected.attachEvents = function(post){
        var onPostChange = function(){
            _protected.onPostChange(post);
        };
        var onDeleted = function(){
            post.detachEvent('/post/deleted/', onDeleted);
            post.getPostData().updated.unlisten(onPostChange);
            post.getPostData().lastView.unlisten(onPostChange);
              for(var i = 0; i <  _protected.postList.length; i++){
                if(_protected.postList[i].getPostData().postId.toString() == post.getPostData().postId.toString()){
                   _protected.postList.splice(i, 1);
                   break;
                }
            }
            
        };
        
        post.getPostData().updated.listen(onPostChange);
        post.getPostData().lastView.listen(onPostChange);
        post.attachEvent('/post/deleted/', onDeleted)
        return onPostChange;
    }
    
    _protected.recalculateNotificationCount = function(){
       var count = 0;
       for(var i = 0; i <  _protected.postList.length; i++){
           if(_protected.postList[i].getPostData().lastView.get() == null || _protected.postList[i].getPostData().updated.get() > _protected.postList[i].getPostData().lastView.get()){
               count++;
           }
       }
       _protected.drawCount(count);
    }
    
    
    
    
    _protected.onPostChange = function(){
        _protected.recalculateNotificationCount();
    }
    
    
    _protected.drawCount = function(count){
        if(count > 0){
             $('title').text('('+ count +') ' + _protected.originalTitle);
        }
        else {
              $('title').text(_protected.originalTitle);
        }
    }
  
    return me;
    
}).construct();