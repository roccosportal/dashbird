if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Board = function(){
    var me = SimpleJSLib.EventHandler(),
    _private = {};
    _private.$commandBar = null;
    _private.$posts = null;
    _private.posts = [];
    _private.loadPostsAjaxRequestQueue = null;
    _private.boardKeyCapture = false;
    _private.boardBarKeyCapture = false;
        
    me.pager = {};
    me.pager.$morePosts = null;
    me.pager.startPosition = 0;
    me.pager.postCount = 10;
    me.pager.hasMorePosts = false;
    
    me.$loading = null;

    me.init = function (){    
        _private.$posts = $('#board .posts');
        _private.$loading = $('#board .loading');
        _private.$viewAll = $('#board .view-all')
        _private.$viewAll.click(function(e){
             e.preventDefault();
             _private.$viewAll.hide();
             me.refreshPosts();
        })
        me.pager.$morePosts = $('#board .more-posts')
        me.pager.$morePosts.click(function(e){
            e.preventDefault();
            me.pager.startPosition =  me.pager.startPosition + me.pager.postCount;
            me.loadPosts();
        });

       
        
        $('#board').show();
        $('#side-bar').show();
        
        _private.loadPostsAjaxRequestQueue = SimpleJSLib.SingleRequestQueue();
    };
                
    me.addToTop = function(postData){
        var post = Dashbird.Post();
        post.init(postData);
        var $post = post.create();      
                                             
        var first = $('#board .posts .post:first');
        if(first.length != 0){
            first.before($post);
        }
        else {
            _private.$posts.append($post);
        }     
    };
    
    me.reorderPosts = function(){
        var $posts =  $('.posts .post');
        $posts.detach();
        $posts.sort(function (a, b) {
            var contentA = $(a).data('post').postData.updated;
            var contentB =$(b).data('post').postData.updated;
            return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
        });
        _private.$posts.append($posts);
    }
    
    me.refreshPosts = function(){
         me.pager.startPosition = 0;
        _private.$posts.html('');
        _private.posts = [];
        me.loadPosts();
    };
    
   
    
    me.apiPostsUpdatedGet = function(callback){
        $.getJSON('/api/posts/updated/get/', {}, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }
    
    me.getHash = function(postId, callback){
        $.getJSON('api/post/hash/get/', {postId : postId}, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }

    me.loadPosts = function(){
        me.pager.$morePosts.hide();
        _private.$loading.show();              
        var request = _private.loadPostsAjaxRequestQueue.runAsynchronRequest();
        $.getJSON('api/posts/load/', {
            search : Dashbird.Search.getSearchPhrase(),
            'start-position' : me.pager.startPosition,
            'post-count' : me.pager.postCount
        }, function(data) {
            if(request.isLatestRequest()){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.$loading.hide();            
                    var posts = data[AJAX.DATA]['posts'];
                    for (var i = 0; i <  posts.length; i++) {
                        var postData = posts[i];
                        var post = Dashbird.Post();
                        post.init(postData);
                        _private.posts.push(post);
                        var $post = post.create();
                        _private.$posts.append($post);                         
                    }
                    if(posts.length>0){
                        me.pager.$morePosts.show();
                    }
                }
            }
        });
       
    };
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
     
    me.showSinglePost = function(postId){
        me.pager.$morePosts.hide();
        _private.$viewAll.show();
        _private.$posts.html('');
        $('#navbar .nav .show-board').tab('show');
        _private.$loading.show();        
         me.getPost(postId, function(data) {
            _private.$loading.hide();     
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.fire('showSinglePost', data[AJAX.DATA]);
                me.addToTop(data[AJAX.DATA]);
            }
        });
    }
    
    
    me.getPost = function(postId, callback){
        $.getJSON('api/post/get/', {
            postId : postId
        }, function(data) {
           callback(data);
        });
    };
    
    me.getPosts = function(postIds, callback){
         $.getJSON('api/posts/get/', {
            postIds : postIds
        }, function(data) {
           callback(data);
        });
    }
    
    me.convertDate = function(date){
        return date.substring(0, date.length - 3);
    }
        
    me._private = _private; // for inheritance      
    return me;
}();