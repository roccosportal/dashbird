if(Dashbird===undefined){
    var Dashbird = {};
}
if(Dashbird.Plugins===undefined){
    Dashbird.Plugins = {};
}
Dashbird.Plugins.Notifications = function (){
    var me = {},
    _private = {};
    _private.name = 'Notifications';
    _private.data = null;
    _private.$count = null;
    _private.$ = null;
    _private.changedPostIds = [];
    _private.originalTitle = null;
        
    me.init = function(){
        _private.originalTitle = $('title').text();
        _private.$count = $('#navbar .show-notifications span');
        $('#navbar .nav .show-notifications').on('show', _private.onShow);
        _private.$ = $('#notifications');
        me.loadData(function(){
            me.onFirstTime(function(){
                me.refresh();
            });
        });
 
        setInterval(me.refresh, 30000);
              
              
              
        Dashbird.Board.attach('showSinglePost', _private.visitedPost);
        Dashbird.NewPost.attach('newPost', _private.visitedPost);
        Dashbird.Board.attach('post#save', _private.visitedPost);
        Dashbird.Board.attach('post#addComment', _private.visitedPostByChangingComment);
        Dashbird.Board.attach('post#deleteComment',_private.visitedPostByChangingComment);
    }
    
    _private.onShow = function(){
        
    };
        
    me.loadData = function(onLoaded){
        Dashbird.PluginManager.loadData(_private.name, function(data){
            _private.data = data;
            if(onLoaded!=null){
                onLoaded();
            }
        });
    };
        
    me.saveData = function(){
        Dashbird.PluginManager.saveData(_private.name, _private.data);
    };
        
    me.onFirstTime = function(callback){
        if($.isEmptyObject(_private.data)){
            Dashbird.Board.apiPostsUpdatedGet(function(data){
                _private.data = {
                    options : {},
                    lastViews : data.dates
                }
                me.saveData();
                callback();
            });
        }
        else {
            callback();
        }
    }
        
            
    me.refresh = function(){
        _private.check(function(){
            _private.updateCountDisplay();
            _private.showNotifications();
        });
        
    }
    
    _private.check = function(callback){
        _private.changedPostIds = [];
        Dashbird.Board.apiPostsUpdatedGet(function(data){
            var lastView = null;
            $.each(data.dates, function(){
                lastView = _private.getLastViewFromData(this.postId)
                if(this.updated != lastView){
                    _private.changedPostIds.push(this.postId);
                }
            });
            if(callback != null){
                callback();
            }
        });
    };
    
    _private.updateCountDisplay = function(){
        _private.$count.html(_private.changedPostIds.length);
        if(_private.changedPostIds.length > 0){
             $('title').text('('+ _private.changedPostIds.length +') ' + _private.originalTitle);
        }
        else {
              $('title').text(_private.originalTitle);
        }
    }
    
    _private.showLoading = function(){
        _private.$.find('.loading').show();
    };
    
    _private.hideLoading = function(){
        _private.$.find('.loading').hide();
    };
    

    
    _private.showNotifications = function(){
        _private.$.find('.content').html('');
        _private.hideLoading();
        
        if(_private.changedPostIds.length > 0){
            _private.showLoading();
    
            Dashbird.Board.getPosts(_private.changedPostIds, function(data){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    var posts = data[AJAX.DATA];
                    $.each(posts, function(key, element){
                        var $notification = $('#templates #template-notification .notification').clone();
                        var text = element.text;
                        if(text.length > 120){
                            text = text.substring(0,120);
                        }
                        text += '...';
                        $notification.find('.text').html(text);
                        $notification.find('.meta .info .username').html(element.user.name);
                        $notification.find('.meta .info .date').html(Dashbird.Board.convertDate(element.updated));
                    
                        $notification.find('.meta .comments span').html(element.comments.length);
                    
                        $notification.mouseover(function(){
                            $notification.find('.command-bar.popup').show();
                        });
                        $notification.mouseleave(function (){
                            $notification.find('.command-bar.popup').hide();
                        });
                        $notification.find('.command-bar.popup .command-mark-as-read').click(function(e){
                            e.preventDefault();
                            _private.visitedPost(element);
                        });
                        $notification.find('.text').click(function(e){
                            e.preventDefault();
                            Dashbird.Board.showSinglePost(element.postId);
                        });
                        $notification.find('.read').click(function(e){
                            e.preventDefault();
                            Dashbird.Board.showSinglePost(element.postId);
                        });
                    
                        _private.$.find('.content').append($notification)
                    });
                }
                _private.hideLoading();
            });
        }
        
        
     
        
    }
    
    _private.getLastViewFromData = function(postId){
        var lastView = null;
        $.each(_private.data.lastViews, function(){
            if(this.postId == postId){
                lastView = this.updated;
                return false;
            }
            return true;
        });
        return lastView;
    };
    
    _private.updateLastView = function(postId, lastView){
        var inArray = false;
        $.each(_private.data.lastViews, function(){
            if(this.postId == postId){
                this.updated = lastView;
                inArray = true;
                return false;
            }
            return true;
        });
        if(!inArray){
            _private.data.lastViews.push({
                postId : postId, 
                updated : lastView
            });
        }
        me.saveData();
    };
    
    _private.visitedPost = function(data){
        _private.updateLastView(data.postId, data.updated);
        me.refresh();
    }
    
    _private.visitedPostByChangingComment = function(data){
        _private.updateLastView(data.post.postData.postId, data.post.postData.updated);
        me.refresh();
    }
        
    return me;
    
}();
