Dashbird.PostFeedHtmlLayer =  SimpleJSLib.EventHandler.inherit(function(me, _protected){

    _protected.construct = function(parameters){
        _protected.post = parameters[0];
        
        _protected.$layer = $('#templates #template-post-feed .post').clone();
        _protected.$headline =  _protected.$layer.find('.headline');
        _protected.$activity =  _protected.$layer.find('.activity');
        
        
        
        _protected.post.getPostData().updated.listen(_protected.redraw);
        _protected.post.getPostData().comments.listen(_protected.redraw);
        _protected.post.getPostData().lastView.listen(_protected.redraw);
        _protected.post.attachEvent('/post/deleted/', me.destroy);
        _protected.redraw();
    }
    
    me.getLayer = function(){
        return _protected.$layer;
    }
    
    _protected.redraw = function(){
        _protected.drawHeadline();
        _protected.drawActivity();
    }
    
    me.undraw = function(){
        me.getLayer().fadeOut(function(){
             me.getLayer().detach();
        });
    }
  
    
    me.destroy = function(){
        me.undraw();
        _protected.post.getPostData().updated.unlisten(_protected.drawActivity);
        _protected.post.getPostData().comments.unlisten(_protected.drawActivity);
        _protected.post.getPostData().lastView.unlisten(_protected.redraw);
        _protected.post.detachEvent('/post/deleted/',  me.destroy);
        me.fireEvent('/destroying/', me);
        delete _protected.post;
        delete me;
        delete _protected;
    }
    
    _protected.drawHeadline = function(){
        if(_protected.post.getPostData().lastView.get() == null){
            _protected.$headline.removeClass('viewed');
        }
        else {
            _protected.$headline.addClass('viewed');
        }
       
        _protected.$headline.find('.username').html(_protected.post.getPostData().user.name);
        _protected.$headline.find('.date').html(_protected.post.getPostData().created);
        _protected.$headline.find('.post-link').click(function(e){
            e.preventDefault();
            Dashbird.SingleView.showPost(_protected.post.getPostData().postId);
        });
    }
    
    _protected.drawActivity = function(){
        var $ul = _protected.$activity.find('ul');
        $ul.html('');
        var $update = $('#templates #template-post-feed-update li').clone()
        $update.find('.date').html(_protected.post.getPostData().updated.get());
        if(_protected.post.getPostData().lastView.get() == null || _protected.post.getPostData().updated.get() > _protected.post.getPostData().lastView.get())
            $update.removeClass('viewed');
        else 
            $update.addClass('viewed');
        
        $ul.append($update);
        
        var comments = _protected.post.getPostData().comments.get();
        var $comment = $('#templates #template-post-feed-comment li').clone();
        var $viewed = $('#templates #template-post-feed-viewed li').clone();
        
        var unviewedComments = false;
        for(var i = 0; i < comments.length; i++){
            $comment = $comment.clone();
            $comment.find('.username').html(comments[i].user.name);
            $comment.find('.date').html(comments[i].datetime);
            if(unviewedComments ||  (_protected.post.getPostData().lastView.get() == null || comments[i].datetime > _protected.post.getPostData().lastView.get())){
                if(unviewedComments==false){
                    // first one
                    if(_protected.post.getPostData().lastView.get()!=null){
                        $viewed.find('.date').html(_protected.post.getPostData().lastView.get());
                        $ul.append($viewed);
                    }
                    unviewedComments = true;
                }
               
                $comment.removeClass('viewed');
            } else {
                $comment.addClass('viewed');
            }
            
            $ul.append($comment);
        }
        
        if(!unviewedComments){
            if(_protected.post.getPostData().lastView.get()!=null){
                $viewed.find('.date').html(_protected.post.getPostData().lastView.get());
                $ul.append($viewed);
            }
        }
          
       
    }
                        
    return me;
});