Dashbird.Posts = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.postList = [];
    
    
    me.init = function(){
        // try to get new data every  15 seconds
        setInterval(function(){
            me.loadPostsByUpdated(50)
            }, 15000);
    }
    
    me.getPosts = function(){
        return _protected.postList;
    };
    
    me.add = function(post){
        _protected.postList.push(post);
        return post;
    }
    
    me.get = function(index){
        return _protected.postList[index];
    }
    
    me.getPost = function(postId, callback){
        var index = _protected.getListIndex(postId);
        if(index!=null){
            callback(me.get(index))
        }
        else {
            me.loadPost(function(result){ 
                var post = null
                if(result.posts.length==1)
                    post = result.posts[0];
                callback(post);
            });
        }
    }
    
    _protected.getListIndex = function(postId){
        for(var i = 0; i < _protected.postList.length; i++){
            if(_protected.postList[i].getPostData().postId.toString() === postId.toString()){
                return i;
            }
        }
        return null;
    };
    
    _protected.hasChanges = function(post, newPostData){
        return (post.getPostData().updated.get()!==newPostData.updated);
    };
    
    _protected.changeData = function(post, newPostData){
        post.getPostData().updated.set(newPostData.updated);
        post.getPostData().text.set(newPostData.text);
        post.getPostData().comments.set(newPostData.comments);
        post.getPostData().tags.set(newPostData.tags);
        post.getPostData().postShares.set(newPostData.postShares);
        post.getPostData().lastView.set(newPostData.lastView);
    };
    
    _protected.onPostDeleted = function(post){
        var index = _protected.getListIndex(post.getPostData().postId);
        if(index){
            _protected.postList.splice(index, 1);
        }
        post.detachEvent('/post/deleted/',_protected.onPostDeleted);
    }
    
    me.mergePostDatas = function(postDatas){
        var newPosts = [];
        var mergedPosts = [];
        var posts = [];
        for (var i = 0; i <  postDatas.length; i++) {
            var listIndex = _protected.getListIndex(postDatas[i].postId);
            var post = null;
            if(listIndex == null){
                post = Dashbird.Post.construct(postDatas[i]);
                post.attachEvent('/post/deleted/',_protected.onPostDeleted);
                me.add(post);
                newPosts.push(post);
                posts.push(post);
            }
            else {
                post = me.get(listIndex);
                if(_protected.hasChanges(post, postDatas[i])){
                    _protected.changeData(post, postDatas[i]);
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
        var postList =  _protected.postList.slice();
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
        for(var i = 0; i < _protected.postList.length; i++){
            if(_protected.postList[i].getPostData().created <  startDate){
                postList.push(_protected.postList[i]);
            }
        }
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
        for(var i = 0; i < _protected.postList.length; i++){
            if(_protected.postList[i].isSearchMatch(search)){
                matchingPosts.push(_protected.postList[i]);
            }
        }
        return matchingPosts;
    }
    
    
    
  
    return me;
    
}).construct();