Dashbird.Posts = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.postList = [];
    
    
    
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
    
    _protected.getListIndex = function(postId){
        for(var i = 0; i < _protected.postList; i++){
            if(_protected.postList[i].getPostData().postId === postId){
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
    };
    
    _protected.mergePostDatas = function(postDatas){
        var newPosts = [];
        var mergedPosts = [];
        for (var i = 0; i <  postDatas.length; i++) {
            var listIndex = _protected.getListIndex(postDatas[i]);
            var post = null;
            if(listIndex == null){
                post = Dashbird.Post.construct(postDatas[i]);
                me.add(post);
                newPosts.push(post);
            }
            else {
                post = me.get(listIndex);
                if(_protected.hasChanges(post, postDatas[i])){
                    _protected.changeData(post, postDatas[i]);
                    mergedPosts.push(post);
                }
            }                     
        }
        var result =  {newPosts : newPosts, mergedPosts : mergedPosts}
        if(newPosts.length != 0){
            me.fireEvent('/posts/new/',result);
        }
        
        if(mergedPosts.length != 0){
            me.fireEvent('/posts/merged/',result);
        }
        return result;
    }
    
    me.loadPostsByCreated = function(startDate, postCount, callback){
        $.getJSON('api/posts/load/', {
            'search' : '',
            'start-date' : startDate,
            'post-count' : postCount,
            'order-by' : 'CREATED'
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                var result = _protected.mergePostDatas(ajaxResponse.data.posts);
                me.fireEvent('/load/posts/by/created/', result);
                if(typeof(callback)!== 'undefined'){
                    callback(result);
                }
            }
        });
       
    };
    
    me.getListByCreated = function(startDate, postCount){
        var postList = []; 
        for(var i = 0; i < _protected.postList; i++){
            if(_protected.postList[i].getPostData().created >  startDate){
               postList.push(_protected.postList[i]);
            }
        }
        postList.sort(function (a, b) {
            var contentA = a.getPostData().updated.get();
            var contentB = b.getPostData().updated.get();
            return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
        });
        if(postCount > postList.length){
            postCount = postList.length;
        }
        return postList.slice(0, postCount);
    };
    return me;
    
}).construct();