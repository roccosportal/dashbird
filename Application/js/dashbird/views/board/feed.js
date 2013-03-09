Dashbird.Views.Board.Feed = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    
    _protected.postList = [];
    _protected.postCount = 20;
    _protected.postFeedHtmlLayers = [];
    
    me.init = function(){
        _protected.$feed = $('#feed');
        _protected.$posts = _protected.$feed.find('.posts');
        _protected.$changedPostsCounter = $('#feed-changed-posts-counter');
        Dashbird.Views.Board.Stack.attachEvent('/stack/initialized/', function(){
           if(_protected.isVisible()){
               me.show();
           }
           Dashbird.Views.Board.Latest.fireEvent('/latest/initialized/');
          
        });
      
        $('#navigation .feed').click(me.show);
    }
    
    _protected.isVisible = function(){
        return (_protected.$feed.hasClass('active'));
    };
    
    _protected.getFromPostList = function(postId){
        for(var i = 0; i < _protected.postList.length; i++){
            if(postId.toString() == _protected.postList[i].getPostData().postId.toString())
                return _protected.postList[i];
        }
        return null;
    }
    

   
    me.show = function(e){
        if(typeof(e) !== 'undefined')
            e.preventDefault();

        if(!_protected.isVisible())
            $('#navigation .feed').tab('show');
    
        // move to top
        window.scrollTo(0,0);

        _protected.onShow();
    }
    
    _protected.onDestroyingPost = function(feedLayer){
        feedLayer.detachEvent('/destroying/',_protected.onDestroyingPost);
        var index = null;
        for(var k = 0; k < _protected.postFeedHtmlLayers.length; k++){
            if(_protected.postFeedHtmlLayers[k].getPost().getPostData().postId.toString() == feedLayer.getPost().getPostData().postId.toString()){
                index = k;
                break;
            }
        }
        if(index != null){
            _protected.postFeedHtmlLayers.splice(index, 1);
        }
    }
    
    _protected.onShow = function(){
        
        
        _protected.$posts.html('');
        for(var k = 0; k < _protected.postFeedHtmlLayers.length; k++){
            _protected.postFeedHtmlLayers[k].detachEvent('/destroying/',_protected.onDestroyingPost);
            _protected.postFeedHtmlLayers[k].destroy();
        }
         _protected.postFeedHtmlLayers = [];
         
         var posts = Dashbird.Controllers.Posts.getListByUpdated(_protected.postCount);
         for(var i = 0; i < posts.length; i++){
            var feedLayer = Dashbird.ViewModels.PostFeed.construct(posts[i]);
            _protected.postFeedHtmlLayers.push(feedLayer);
            feedLayer.attachEvent('/destroying/',_protected.onDestroyingPost);
            _protected.$posts.append(feedLayer.getLayer());
        }
    }
    
    _protected.redraw = function(){
         
    }
        
    return me;
}).construct();

