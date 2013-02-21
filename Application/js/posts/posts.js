Dashbird.Posts = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.postList = null;
    

    
    me.init = function(){
        _protected.postList = SimpleJSLib.MappingArray.construct();

        // shortcuts
        me.add = _protected.postList.add;
        me.get = _protected.postList.getByIndex;
        me.getByPostId = _protected.postList.getByKey;
        // try to get new data every  15 seconds
        setInterval(function(){
            me.loadPostsByUpdated(50)
            }, 15000);
    }
    
  
    me.add = null;
    
    me.get = null;

    me.getByPostId = null;
    
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
    
    _protected.hasChanges = function(post, newPostData){
        return (post.getPostData().updated.get()!==newPostData.updated);
    };
    
    _protected.onPostDeleted = function(post){
        _protected.postList.remove(post.getPostId())
        post.detachEvent('/post/deleted/',_protected.onPostDeleted);
    }
    
    me.mergePostDatas = function(postDatas){
        var newPosts = [];
        var mergedPosts = [];
        var posts = [];
        var post = null;
        for (var i = 0; i <  postDatas.length; i++) {
            post = me.getByPostId(parseInt(postDatas[i].postId));
            if(post == null){
                post = Dashbird.Post.construct(postDatas[i]);
                post.attachEvent('/post/deleted/',_protected.onPostDeleted);
                me.add(post.getPostId(),post);
                newPosts.push(post);
                posts.push(post);
            }
            else {
                 if(_protected.hasChanges(post, postDatas[i])){
                    post.mergeData(postDatas[i]);
                    // _protected.changeData(post, postDatas[i]);
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
    
    me.loadPostsByCreated = function(startDate, postCount, callback){
        $.getJSON('api/posts/load/', {
            'start-date' : startDate,
            'post-count' : postCount,
            'order-by' : 'CREATED'
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas(ajaxResponse.data.posts);
                me.fireEvent('/load/posts/by/created/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
       
    };
    
      
    me.loadPostsByUpdated = function(postCount, callback){
        $.getJSON('api/posts/load/', {
            'post-count' : postCount,
            'order-by' : 'UPDATED'
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas(ajaxResponse.data.posts);
                me.fireEvent('/load/posts/by/updated/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
       
    };
    
    me.loadPost = function(postId, callback){
        $.getJSON('/api/post/get/', {
            'postId' : 'postId'
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = me.mergePostDatas([ajaxResponse.data]);
                me.fireEvent('/load/post/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
       
    };
    
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
    
    me.loadPostBySearch = function(search, callback){
        $.getJSON('/api/posts/search/', {
            'search' : search
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
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