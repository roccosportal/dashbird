Dashbird.Views.Board.SingleView = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.currentPost = null;
    _protected.postHtmlLayer = null;
  
    me.init = function(){
       _protected.$pane = $('#single-view');
       _protected.$content = _protected.$pane.find('.content');
       
       $('#navigation .single-view').click(me.show);
    }
    
    _protected.loadPost = function(postId){
        _protected.$content.html('');
        Dashbird.Controllers.Posts.getPost(postId, function(post){
            _protected.currentPost = post;
             _protected.postHtmlLayer = Dashbird.ViewModels.Post.construct(post);
             _protected.postHtmlLayer.setAllowedToRedraw(true);
             _protected.postHtmlLayer.attachEvent('/destroying/', me.hide);
             _protected.$content.append(_protected.postHtmlLayer.getLayer());
             Dashbird.Controllers.Post.setLastView(_protected.currentPost);
        });
    }
    
    _protected.isVisible = function(){
        return (_protected.$pane.hasClass('active'));
    };
    
    me.show = function(e){
        if(typeof(e) !== 'undefined')
            e.preventDefault();

        if(!_protected.isVisible())
            $('#navigation .single-view').tab('show');
    
        // move to top
        window.scrollTo(0,0);

       
    }
    
 
    
    me.hide = function(){
         _protected.postHtmlLayer.detachEvent('/destroying/', _protected.hide);
         _protected.currentPost = null;
         _protected.postHtmlLayer = null;
         
         $('#navigation .single-view').hide();
         if(_protected.isVisible()){
             Dashbird.Views.Board.Stack.show();
         }
    }
    
    me.showPost = function(postId){
        if(_protected.currentPost == null || _protected.currentPost.getPostData().postId.toString() != postId){
             $('#navigation .single-view').show();
            _protected.loadPost(postId);
        }
        me.show();
    }
    
   
    return me;
}).construct();

