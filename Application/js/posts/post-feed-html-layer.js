Dashbird.PostFeedHtmlLayer =  SimpleJSLib.EventHandler.inherit(function(me, _protected){

    _protected.drawingManager = null;
    _protected.activityFeedLayer = null;

    _protected.construct = function(parameters){
        _protected.post = parameters[0];
        
        _protected.$layer = $('#templates #template-post-feed .post').clone();
        _protected.$headline =  _protected.$layer.find('.headline');
        _protected.$activity =  _protected.$layer.find('.activity');
        _protected.drawHeadline();

        _protected.activityFeedLayer = Dashbird.ActivityFeedLayer.construct(me ,_protected.$activity.find('ul'));
        _protected.drawingManager = Dashbird.DrawingManager.construct(me.redraw, me.isAllowedToRedraw, ['viewed']);

        _protected.post.attachEvent('/post/deleted/', me.destroy);
        _protected.post.getLastView().listen(_protected.onLastViewChanged);
    }

    // --- catch events ---

    _protected.onLastViewChanged = function(){
        _protected.drawingManager.queueRedraw(['viewed']);
    }

    // --- end ---

    // --- getter and setters ---
    
    me.getLayer = function(){
        return _protected.$layer;
    }

    me.getPost = function(){
        return _protected.post;
    }

    me.isViewed = function(){
        return _protected.post.isViewed();
    }

    me.isAllowedToRedraw = function(){
        return true;
    }

    // --- end ---


    // --- drawing ---
    
    me.redraw = function(){
        var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
        if(drawingChangeSet.viewed){
            _protected.drawHeadline();
        }
        _protected.activityFeedLayer.redraw();
        _protected.drawingManager.setDrawingChangeSetToDefault();
    }
    
    me.undraw = function(){
        me.getLayer().fadeOut(function(){
             me.getLayer().detach();
        });
    }

    _protected.drawViewed = function(){
        if(_protected.post.getLastView().get() == null){
            _protected.$headline.removeClass('viewed');
        }
        else {
            _protected.$headline.addClass('viewed');
        }
    }

    _protected.drawHeadline = function(){
        _protected.drawViewed();
       
        _protected.$headline.find('.username').html(_protected.post.getPostData().user.name);
        _protected.$headline.find('.date').html(_protected.post.getPostData().created);
        _protected.$headline.find('.post-link').click(function(e){
            e.preventDefault();
            Dashbird.SingleView.showPost(_protected.post.getPostData().postId);
        });
    }
    

    // --- end ---
  
    
    me.destroy = function(){
        me.undraw();
        _protected.post.getLastView().listen(_protected.onLastViewChanged);
        _protected.post.detachEvent('/post/deleted/',  me.destroy);
        me.fireEvent('/destroying/', me);
        delete _protected.post;
        delete me;
        delete _protected;
    }
    
    
                        
    return me;
});