Dashbird.Views.Board.Latest = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    
    _protected.postList = [];
    _protected.postCount = 30
    //_protected.postHtmlLayers = [];
    
    me.init = function(){
        _protected.postHtmlLayersManager = Dashbird.Views.Utils.PostHtmlLayersManager.construct();
        _protected.$changedPostsCounter = $('#latest-changed-posts-counter');
        _protected.$latest = $('#latest');
        _protected.$posts = _protected.$latest.find('.posts');
        Dashbird.Views.Board.Stack.attachEvent('/stack/initialized/', function(){
            Dashbird.Controllers.Posts.loadPostsByUpdated({'post-count': _protected.postCount}, function(result){
                // only draw the posts if the pane is visible
                if(!_protected.isVisible())
                    _protected.postList = result.posts; // just set it to get the counter working
                else 
                    _protected.setPosts(result.posts);
                 
                Dashbird.Controllers.Posts.attachEvent('/posts/changed/', _protected.onPostsChanged);
                Dashbird.Views.Board.Latest.fireEvent('/latest/initialized/');
            })
          
        });
        $(window).scroll(function() {
            if(_protected.isVisible()){
                 _protected.postHtmlLayersManager.changeAllowedToRedraw();
            }
        });
        $('#navigation .latest').click(me.show);
    }
    
    _protected.isVisible = function(){
        return (_protected.$latest.hasClass('active'));
    };
    
    _protected.getFromPostList = function(postId){
        for(var i = 0; i < _protected.postList.length; i++){
            if(postId.toString() == _protected.postList[i].getPostData().postId.toString())
                return _protected.postList[i];
        }
        return null;
    }
    
    _protected.onPostsChanged = function(){
        var changeCounter = 0;
        var posts = Dashbird.Controllers.Posts.getListByUpdated(_protected.postCount);
        var post = null;
        for(var i = 0; i < posts.length; i++){
            post = _protected.getFromPostList(posts[i].getPostData().postId);
            if(post==null)
                changeCounter++;
        }
        _protected.drawChangedPostsCounter(changeCounter);
    }
    
    _protected.drawChangedPostsCounter = function(count){
        if(count==0){
            _protected.$changedPostsCounter.html('');
        }
        else {
            _protected.$changedPostsCounter.html(count);
        }
    }
    
    me.show = function(e){
        if(typeof(e) !== 'undefined')
            e.preventDefault();

        if(!_protected.isVisible())
            $('#navigation .latest').tab('show');
    
        // move to top
        window.scrollTo(0,0);

        _protected.onShow();
    }
    
    _protected.onShow = function(){
        // view is now on top again;
        var posts = Dashbird.Controllers.Posts.getListByUpdated(_protected.postCount);
        _protected.setPosts(posts);
        _protected.drawChangedPostsCounter(0);
    }
    
    _protected.setPosts = function(posts){
        _protected.postList = posts;
        
        _protected.postHtmlLayersManager.clear();

        _protected.$posts.html('');
        for (var j = 0; j <  _protected.postList.length; j++) {
            var postHtmlLayer = Dashbird.ViewModels.Post.construct(_protected.postList[j]);
            _protected.postHtmlLayersManager.registerPostHtmlLayer(postHtmlLayer, 'bottom');
            _protected.$posts.append(postHtmlLayer.getLayer());
        }
    }
    
    return me;
}).construct();

