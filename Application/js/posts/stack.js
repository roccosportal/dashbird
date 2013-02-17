Dashbird.Stack = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$posts = null;
    _protected.posts = [];
//    _protected.postHtmlLayers = [];
//    _protected.postHtmlLayersAllowedForRedraw = [];
//    _protected.postHtmlLayersDeniedForRedraw = [];
    _protected.pager = {};
    _protected.pager.$morePosts = null;
    _protected.pager.postCount = 10;
    
    _protected.$loading = null;
    _protected.isLoading = false;
    _protected.topCreatedDate = null;
    _protected.newPosts = [];
    _protected.isVisible = function(){
        return (_protected.$stack.hasClass('active'));
    };
    
    
    
    _protected.getCreateDateOfLastPost = function(){
        return _protected.posts[_protected.posts.length - 1].getPostData().created;
    }
    me.init = function (){  
         _protected.postHtmlLayersManager = Dashbird.PostHtmlLayersManager.construct();
        _protected.$stack = $('#stack');
        _protected.$posts = $('#stack .posts');
        _protected.$loading = $('#stack .loading');
        _protected.$newPostCounter = $('#stack-new-post-counter');
        _protected.$loading.show();      
        _protected.pager.$morePosts = $('#stack .more-posts')
        _protected.pager.$morePosts.click(function(e){
            e.preventDefault();
            me.showMorePosts();
        });
        
        $(window).scroll(function() {
            if(_protected.isVisible()){
                _protected.postHtmlLayersManager.changeAllowedToRedraw();
                if(!_protected.isLoading &&  _protected.pager.$morePosts.is(':visible') && Dashbird.Utils.topIsOnScreen( _protected.pager.$morePosts )){
                    me.showMorePosts();
                }
            }
        });

        Dashbird.Posts.loadPostsByCreated(Dashbird.InitialData.LoadedAt, _protected.pager.postCount, function(result){
            var posts = result.newPosts;
            _protected.topCreatedDate = posts[0].getPostData().created;
            me.addPosts(posts);
            _protected.$loading.hide();
            _protected.pager.$morePosts.show();
            me.fireEvent('/stack/initialized/');
            Dashbird.Posts.attachEvent('/posts/new/', _protected.onNewPosts);
        });
        $('#navigation .stack').click(me.show);
    };
    
    me.show = function(e){
        if(typeof(e) !== 'undefined')
            e.preventDefault();

        if(!_protected.isVisible())
            $('#navigation .stack').tab('show');
    
        // move to top
        window.scrollTo(0,0);

        _protected.onShow();
    }
    
    _protected.onShow = function(){
          // view is now on top again;
        _protected.postHtmlLayersManager.allowAll();
        
        
        if( _protected.newPosts.length > 0){
            me.addPosts(_protected.newPosts, 'top');
            _protected.newPosts = [];
            _protected.drawNewPostCounter();
        }
       
    }

    me.addPosts = function(posts, position){
        if(typeof(position) === 'undefined'){
            position = 'bottom';
        }

        for (var i = 0; i <  posts.length; i++) {
            var postHtmlLayer = Dashbird.PostHtmlLayer.construct(posts[i]);
            _protected.postHtmlLayersManager.registerPostHtmlLayer(postHtmlLayer, position);
            _protected.posts.push(posts[i]);
            //postHtmlLayer.setAllowedToRedraw(true);
            //var index = _protected.postHtmlLayers.push(postHtmlLayer) - 1;
            if(position==='bottom'){
                //_protected.postHtmlLayersAllowedForRedraw.push(index);
                _protected.$posts.append(postHtmlLayer.getLayer());
            }
            else if(position==='top'){
                //_protected.postHtmlLayersAllowedForRedraw.unshift(index);
                _protected.$posts.prepend(postHtmlLayer.getLayer());
            }
            
        }
    }
    
    _protected.drawNewPostCounter = function(){
        if(_protected.newPosts.length==0){
            _protected.$newPostCounter.html('');
        }
        else {
            _protected.$newPostCounter.html(_protected.newPosts.length);
        }
    }
    
    _protected.onNewPosts = function(result){
        var posts = result.newPosts;
        for(var i = 0; i < posts.length; i++){
            if(posts[i].getPostData().created >= _protected.topCreatedDate){
                _protected.newPosts.push(posts[i]);
            }
        }
        _protected.drawNewPostCounter();
    }
    
    me.showMorePosts = function(){
        if(!_protected.isLoading){
            _protected.isLoading = true;
            _protected.pager.$morePosts.hide();
            _protected.$loading.show();
            var currentCreateDateOfLastPost = _protected.getCreateDateOfLastPost();
            var posts = Dashbird.Posts.getListByCreated(currentCreateDateOfLastPost, _protected.pager.postCount);
            me.addPosts(posts);
            if(posts.length < _protected.pager.postCount){  // we need to load the rest of the data
                var remainingPostCount = _protected.pager.postCount - posts.length;
                Dashbird.Posts.loadPostsByCreated(_protected.getCreateDateOfLastPost(), remainingPostCount, function(result){
                    me.addPosts(result.newPosts);
                    if(result.newPosts.length == remainingPostCount){
                        _protected.pager.$morePosts.show();
                    }
                    _protected.$loading.hide();
                    _protected.isLoading = false;
                });
            }
            else {
                _protected.pager.$morePosts.show();
                _protected.$loading.hide();
                _protected.isLoading = false;
            }
        }
    };
    
//    _protected.changeAllowedToRedraw = function(){
//        
//        var movePostsToAllow = [];
//        // go reverse
//        var postHtmlLayer = null
//        for(var j = _protected.postHtmlLayersDeniedForRedraw.length - 1; j >= 0; j --){
//            postHtmlLayer = _protected.postHtmlLayers[_protected.postHtmlLayersDeniedForRedraw[j]];
//            if(Dashbird.Utils.bottomIsOnScreen(postHtmlLayer.getLayer()))
//                movePostsToAllow.push(j);
//            else
//                break;
//        }
//    
//        if(movePostsToAllow.length > 0){
//            var index = null;
//            var postHtmlLayerIndex = null;
//            for(var k = 0; k <movePostsToAllow.length; k ++){
//                index = movePostsToAllow[k];
//                postHtmlLayerIndex = _protected.postHtmlLayersDeniedForRedraw[index];
//                // delete from denied array
//                _protected.postHtmlLayersDeniedForRedraw.splice(index, 1);
//                // add to top of allowed array
//                _protected.postHtmlLayersAllowedForRedraw.unshift(postHtmlLayerIndex);
//                _protected.postHtmlLayers[postHtmlLayerIndex].setAllowedToRedraw(true);
//            }
//        }
//        else {
//            var movePostsToDenied = [];
//            for(var i = 0; i < _protected.postHtmlLayersAllowedForRedraw.length; i ++){
//                postHtmlLayer = _protected.postHtmlLayers[_protected.postHtmlLayersAllowedForRedraw[i]];
//                if(!Dashbird.Utils.bottomIsOnScreen(postHtmlLayer.getLayer()))
//                    movePostsToDenied.push(i);
//                else
//                    break;
//            }
//            if(movePostsToDenied.length > 0){
//                for(var h = 0; h < movePostsToDenied.length; h ++){
//                    index = movePostsToDenied[h];
//                    postHtmlLayerIndex =_protected.postHtmlLayersAllowedForRedraw[index];
//                    // delete from allowed array
//                    _protected.postHtmlLayersAllowedForRedraw.splice(index, 1);
//                    // add to bottom of denied array
//                    _protected.postHtmlLayersDeniedForRedraw.push(postHtmlLayerIndex);
//                    _protected.postHtmlLayers[postHtmlLayerIndex].setAllowedToRedraw(false);
//                }
//            }
//        }
//        
//
//    };

    return me;
}).construct();