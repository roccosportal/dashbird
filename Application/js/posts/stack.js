Dashbird.Stack = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$posts = null;
    _protected.posts = [];
    _protected.pager = {};
    _protected.pager.$morePosts = null;
    _protected.pager.postCount = 10;
    
    _protected.$loading = null;
    
    _protected.getCreateDateOfLastPost = function(){
        return _protected.posts[_protected.posts.length - 1].getPostData().created;
    }
    
    me.addPosts = function(posts){
        for (var i = 0; i <  posts.length; i++) {
            var postHtmlLayer = Dashbird.PostHtmlLayer.construct(posts[i]);
            _protected.posts.push(posts[i]);
            _protected.$posts.append(postHtmlLayer.getLayer());
        }
    }

    me.init = function (){    
        _protected.$posts = $('#stack .posts');
        _protected.$loading = $('#stack .loading');
        _protected.$loading.show();      
        _protected.pager.$morePosts = $('#stack .more-posts')
        _protected.pager.$morePosts.click(function(e){
            e.preventDefault();
            me.showMorePosts();
        });

        Dashbird.Posts.loadPostsByCreated(Dashbird.InitialData.LoadedAt, _protected.pager.postCount, function(result){
            var posts = result.newPosts;
            me.addPosts(posts);
            _protected.$loading.hide();
            _protected.pager.$morePosts.show();
        });
    };
    
    me.showMorePosts = function(){
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
            });
        }
        else {
            _protected.pager.$morePosts.show();
            _protected.$loading.hide();
        }
    }
                
    //    me.addToTop = function(postData){
    //        var post = Dashbird.Post();
    //        post.init(postData);
    //        var $post = post.create();      
    //                                             
    //        var first = $('#stack .posts .post:first');
    //        if(first.length != 0){
    //            first.before($post);
    //        }
    //        else {
    //            _protected.$posts.append($post);
    //        }     
    //    };
    //    
    //
    //    me.loadPosts = function(){
    //        me.pager.$morePosts.hide();
    //        _protected.$loading.show();              
    //        var request = _protected.loadPostsAjaxRequestQueue.runAsynchronRequest();
    //        $.getJSON('api/posts/load/', {
    //            search : Dashbird.Search.getSearchPhrase(),
    //            'start-position' : me.pager.startPosition,
    //            'post-count' : me.pager.postCount,
    //            'order-by' : 'CREATED'
    //        }, function(data) {
    //            var ajaxResponse = Dashbird.AjaxResponse.construct().init(data);
    //            if(request.isLatestRequest()){
    //                if(ajaxResponse.isSuccess){
    //                    _protected.$loading.hide();            
    //                    var posts = ajaxResponse.data.posts;
    //                    for (var i = 0; i <  posts.length; i++) {
    //                        var postData = posts[i];
    //                        var post = Dashbird.Post.construct(postData);
    //                        me.posts.push(post); // make me protected later
    //                       
    //                                           
    //                    }
    //                    if(posts.length>0){
    //                        me.pager.$morePosts.show();
    //                    }
    //                }
    //            }
    //        });
    //       
    //    };

    return me;
}).construct();