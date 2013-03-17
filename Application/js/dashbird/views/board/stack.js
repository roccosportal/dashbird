Dashbird.Views.Board.Stack = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$posts = null;
    _protected.posts = [];
    _protected.pager = {};
    _protected.pager.$morePosts = null;
    _protected.pager.postCount = 10;
    
    _protected.$loading = null;
    _protected.isLoading = false;
    _protected.topCreatedDate = null;
    _protected.newPosts = [];
    _protected.newPostsTopCreatedDate = null;
    _protected.isVisible = function(){
        return (_protected.$stack.hasClass('active'));
    };
    
    
    
    _protected.getCreateDateOfLastPost = function(){
        if(_protected.posts.length == 0)
            return Dashbird.InitialData.LoadedAt;

        return _protected.posts[_protected.posts.length - 1].getPostData().created;
    }

    
    me.init = function (){  
         _protected.viewModelPostsManager = Dashbird.Views.Utils.ViewModelPostsManager.construct();
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
                _protected.viewModelPostsManager.changeAllowedToRedraw();
                if(!_protected.isLoading &&  _protected.pager.$morePosts.is(':visible') && Dashbird.Utils.topIsOnScreen( _protected.pager.$morePosts )){
                    me.showMorePosts();
                }
            }
        });

        Dashbird.Controllers.Posts.loadPostsByCreated(Dashbird.InitialData.LoadedAt, _protected.pager.postCount, function(result){
            var posts = result.newPosts;
            var topCreatedDate = Dashbird.InitialData.LoadedAt;
            if(posts.length > 0){
                topCreatedDate = posts[0].getPostData().created;
            }
            _protected.topCreatedDate = topCreatedDate;


            me.addPosts(posts);
            _protected.$loading.hide();
            _protected.pager.$morePosts.show();
            me.fireEvent('/stack/initialized/');
            Dashbird.Controllers.Posts.attachEvent('/posts/new/', _protected.onNewPosts);
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
        _protected.viewModelPostsManager.allowAll();
        
        
        if( _protected.newPosts.length > 0){
            me.addPosts(_protected.newPosts, 'top');
            _protected.topCreatedDate = _protected.posts[0].getPostData().created;
            _protected.newPosts = [];
            _protected.drawNewPostCounter();
        }
       
    }

    me.addPosts = function(posts, position){
        if(typeof(position) === 'undefined'){
            position = 'bottom';
        }

        for (var i = 0; i <  posts.length; i++) {
            var postHtmlLayer = Dashbird.ViewModels.Post.construct(posts[i]);
            _protected.viewModelPostsManager.registerViewModelPost(postHtmlLayer, position);
            
            if(position==='bottom'){
                _protected.posts.push(posts[i]);
                _protected.$posts.append(postHtmlLayer.getLayer());
            }
            else if(position==='top'){
                _protected.posts.unshift(posts[i]);
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
          
            Dashbird.Controllers.Posts.loadPostsByCreated( _protected.getCreateDateOfLastPost(), _protected.pager.postCount, function(result){
                me.addPosts(result.posts);
                if(result.posts.length ==  _protected.pager.postCount){
                    _protected.pager.$morePosts.show();
                }
                _protected.$loading.hide();
                _protected.isLoading = false;
            });

        }
    };
    

    return me;
}).construct();