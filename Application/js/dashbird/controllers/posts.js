/*
 * The Posts controller is a loader for multiple Posts or even a single Post from the server.
 * It provides a list of all Posts that are loaded into the client application.
 *
 */
Dashbird.Controllers.Posts = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    // A list of <Dashbird.Models.Post> which are loaded into the client application
    _protected.postList = null;
    // Instantiate the post controller
    me.init = function(){
        _protected.postList = SimpleJSLib.MappingArray.construct();
        // shortcuts
        me.add = _protected.postList.add;
        me.get = _protected.postList.getByIndex;
        me.getByPostId = _protected.postList.getByKey;
        _protected.initRefreshingPosts();
    }
    // Hosts a function that automatically tries to load new or updated posts from the server
    _protected.initRefreshingPosts = function(){
        var latestUpdateDate = null;;
        Dashbird.Controllers.Posts.attachEvent('/load/posts/by/updated/', function(result){
            if(result.posts.length > 0)
                latestUpdateDate = result.posts[0].getUpdated().get();
        });
        // try to get new data every 15 seconds
        setInterval(function(){
            var options = {'post-count' : 50 };
            if(latestUpdateDate != null)
                options['newer-equals-than-date'] = latestUpdateDate
            me.loadPostsByUpdated(options);
        }, 15000);
    }
    // shortcut
    me.add = null;
    // shortcut
    me.get = null;
    // shortcut
    me.getByPostId = null;
    // Returns a post by the posts id.
    // First it tries to find it in the local loaded posts and then loades it from the server
    // @param postId <int>
    // @param callback <function(Dashbird.Models.Post)>
    me.getPost = function(postId, callback){
        var post =  me.getByPostId(postId);
        if(post!=null){
            callback(post)
        }
        else {
            me.loadPost(function(result){ 
                if(result.posts.length==1)
                    post = result.posts[0];
                callback(post);
            });
        }
    }
    // Checks if the post model differs from a data object given by the server
    // @param post <Dashbird.Models.Post>
    // @param newPostData <object>
    // @return <boolean>
    _protected.hasChanges = function(post, newPostData){
        return (post.getPostData().updated.get()!==newPostData.updated);
    };
    // This method gets called when a post was deleted.
    // It removes it from the list of loaded posts.
    // @param <Dashbird.Models.Post>
    _protected.onPostDeleted = function(post){
        _protected.postList.remove(post.getPostId())
        post.detachEvent('/post/deleted/',_protected.onPostDeleted);
    }
    // This functions merges a list of post data objects given from the server into the already existing post models or create the post model.
    // @param postDatas <object>
    // @return <object>
    me.mergePostDatas = function(postDatas){
        var newPosts = [], mergedPosts = [],posts = [], post = null;
        for (var i = 0; i <  postDatas.length; i++) {
            post = me.getByPostId(parseInt(postDatas[i].postId));
            if(post == null){
                // create new post model
                post = Dashbird.Models.Post.construct(postDatas[i]);
                post.attachEvent('/post/deleted/',_protected.onPostDeleted);
                me.add(post.getPostId(),post);
                newPosts.push(post);
                posts.push(post);
            }
            else {
                if(_protected.hasChanges(post, postDatas[i])){
                    post.mergeData(postDatas[i]);
                    mergedPosts.push(post);
                }
                posts.push(post);
            }                     
        }
        var result =  {
            newPosts : newPosts, 
            mergedPosts : mergedPosts, 
            posts : posts
        };
        if(newPosts.length != 0)
            me.fireEvent('/posts/new/',result);
        if(mergedPosts.length != 0)
            me.fireEvent('/posts/merged/',result);
        if(newPosts.length != 0 || mergedPosts.length != 0)
            me.fireEvent('/posts/changed/',result);
        return result;
    }
    // Loads posts from the server sorted by create date.
    // @param startData <datetime>
    // @param postCount <int> the max post count the server should return
    // @param callback <function(<object>)>
    me.loadPostsByCreated = function(startDate, postCount, callback){
        $.getJSON('api/posts/load/', {
            'start-date' : startDate,
            'post-count' : postCount,
            'order-by' : 'CREATED'
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas(ajaxResponse.data.posts);
                me.fireEvent('/load/posts/by/created/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
    };
    // Loads posts from the server sorted by update date.
    // @param options <object>
    // @param callback <function(<object>)>
    me.loadPostsByUpdated = function(options, callback){
        var defaultOptions = {
            'post-count' : 50,
            'order-by' : 'UPDATED'
        }
        if(typeof(options['order-by']) != 'undefined')
            throw 'You are not allowed to set "order-by"';
        options = $.extend(defaultOptions, options);
        $.getJSON('api/posts/load/', options, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas(ajaxResponse.data.posts);
                me.fireEvent('/load/posts/by/updated/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
    };
    // Loads a post from the server. You should rather prefer .getPost.
    // @param postId <int>
    // @param callback <function(<object>)>
    me.loadPost = function(postId, callback){
        $.getJSON('/api/post/get/', {
            'postId' : 'postId'
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas([ajaxResponse.data]);
                me.fireEvent('/load/post/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
       
    };
    // Gets a list from the already loaded posts sorted by update date and limited to the post count.
    // @param postCount <int>
    // @return <array> of Dashbird.Models.Post
    me.getListByUpdated = function(postCount){
        var postList =  _protected.postList.cloneArray();
        postList.sort(function (a, b) {
            var contentA = a.getPostData().updated.get();
            var contentB = b.getPostData().updated.get();
            if(contentA > contentB) {
                return -1;
            } else if (contentA < contentB) {
                return 1;  
            }
            else{
                contentA = a.getPostData().created;
                contentB = b.getPostData().created;
                if(contentA > contentB) {
                    return -1;
                } else if (contentA < contentB) {
                    return 1;  
                }
                else {
                    return 0;
                }
            }
        });
        if(postList.length > postCount)
            return postList.slice(0, postCount);
        else
            return postList;
    };
    // Gets a list from the already loaded posts sorted by create date and limited by a start date and to the post count.
    // @param startDate <datetime> post create date must be newer than the start date 
    // @param postCount <int>
    // @return <array> of Dashbird.Models.Post
    me.getListByCreated = function(startDate, postCount){
        var postList = []; 
        _protected.postList.each(function(index, post){
            if(post.getPostData().created <  startDate){
                postList.push(post);
            }
        });
        postList.sort(function (a, b) {
            var contentA = a.getPostData().created;
            var contentB = b.getPostData().created;
              if(contentA > contentB) {
                return -1;
            } else if (contentA < contentB) {
                return 1;  
            }
            else{
                contentA = a.getPostData().updated.get();
                contentB = b.getPostData().updated.get();
                if(contentA > contentB) {
                    return -1;
                } else if (contentA < contentB) {
                    return 1;  
                }
                else {
                    return 0;
                }
            }
        });
        if(postCount > postList.length){
            postCount = postList.length;
        }
        return postList.slice(0, postCount);
    };
    // Loads post from the server by a search.
    // @param search <string> the search keyword
    // @param callback <function(<object>)> [optional]
    me.loadPostBySearch = function(search, callback){
        $.getJSON('/api/posts/search/', {
            'search' : search
        }, function(data) {
            var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas(ajaxResponse.data.posts);
                result.search = search;
                me.fireEvent('/load/post/by/search/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
    };
    // Searches through the already loaded posts.
    // @param search <object>
    // @return <array> of Dashbird.Models.Post
    me.search = function(search){
        var matchingPosts = [];
        _protected.postList.each(function(index, post){
            if(post.isSearchMatch(search)){
                matchingPosts.push(post);
            }
        });
        return matchingPosts;
    }
    return me;
}).construct();