Dashbird.ViewModels.Post =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$post = null;
    _protected.$meta = null;
    
    _protected.allowedToRedraw = false;
    
    _protected.commentsLayer = null;
    
    me.isAllowedToRedraw = function(){
        return  _protected.allowedToRedraw;
    }
    
    me.setAllowedToRedraw = function(value){
        _protected.allowedToRedraw = (value===true);
        if(me.isAllowedToRedraw()){
            me.getLayer().removeClass('deniedForRedraw');
            _protected.redraw();
        }
        else {
            me.getLayer().addClass('deniedForRedraw');
        }
    }
    
    _protected.commands = {
        $bar : null
    }
    
    _protected.post = null;
    
    _protected.construct = function(parameters){
        _protected.post = parameters[0];
        _protected.drawingManager = Dashbird.ViewModels.Utils.DrawingManager.construct(_protected.redraw, me.isAllowedToRedraw, ['text', 'postShares', 'comments', 'tags', 'lastView']);
        
        if(_protected.post.isFromCurrentUser){
            _protected.$post = Dashbird.Views.Utils.Templates.getTemplate('post');
        }
        else {
            _protected.$post = Dashbird.Views.Utils.Templates.getTemplate('foreign-post');
        }
         _protected.$post.find('img.media-object').attr('src', Dashbird.Controllers.User.getGravatarUrlForUser(_protected.post.getPostData().user.name, 48));

        // create jquery shortcuts
        _protected.$meta =  _protected.$post.find('.content .meta');
        _protected.commands.$bar = _protected.$post.find('.content .command-bar.popup');
        _protected.commentsLayer = Dashbird.ViewModels.Comments.construct(me, _protected.$post.find('.comments'));;
        
        _protected.drawText();
        _protected.$meta.find('.info .username').html(_protected.post.getPostData().user.name);
        _protected.$meta.find('.info .date').html(Dashbird.Utils.convertDate(_protected.post.getPostData().created));
        
        _protected.drawLastView();
        
        if(_protected.post.isFromCurrentUser){
            _protected.commands.edit = Dashbird.Commands.Edit.construct(me);
            
            
            _protected.commands.share = Dashbird.Commands.Share.construct(me);
            
            _protected.commands.remove = Dashbird.Commands.Remove.construct(me);
        }

        _protected.commands.comment = Dashbird.Commands.Comment.construct(me);
        
        _protected.drawTags();
        _protected.drawPostShares();

        // show options
        _protected.$post.mouseover(function(){
            _protected.commands.$bar.show();
        });
        _protected.$post.mouseleave(function (){
            _protected.commands.$bar.hide();
        }); 
        
        _protected.$post.data('post', me);
        
        _protected.$meta.find('.viewStatus').click(function() {_protected.post.setLastView() });
        

        // attach listener
        _protected.post.getPostData().text.listen(_protected.onTextChanged);
        // _protected.post.getPostData().comments.listen(_protected.onCommentsChanged);
        _protected.post.getPostData().tags.listen(_protected.onTagsChanged);
        _protected.post.getPostData().postShares.listen(_protected.onPostSharesChanged);
        _protected.post.getPostData().lastView.listen(_protected.onLastViewChanged);
        _protected.post.getPostData().updated.listen(_protected.onLastViewChanged); // todo: better solution
        _protected.post.attachEvent('/post/deleted/', _protected.onDeleted);
    }
    
    _protected.onTextChanged = function(){
        _protected.drawingManager.queueRedraw(['text']);
    }
   
    _protected.onTagsChanged = function(){
        _protected.drawingManager.queueRedraw(['tags']);
    }
    
    _protected.onPostSharesChanged = function(){
        _protected.drawingManager.queueRedraw(['postShares']);
         _protected.allowedToRedraw ? _protected.drawPostShares() : _protected.changeSet.postShares = true;
    }
    
    _protected.onLastViewChanged = function(){
        _protected.drawingManager.queueRedraw(['lastView']);
    }
    
    _protected.onDeleted = function(){
        _protected.drawingManager.queueRedraw(['isDeleted']);
    }
    
    _protected.redraw = function(){
        var drawingChangeSet = _protected.drawingManager.getDrawingChangeSet();
         if(drawingChangeSet.isDeleted){
             me.destroy();
         }
         else {
            if(drawingChangeSet.text)
                 _protected.drawText();

            if(drawingChangeSet.tags)
                 _protected.drawTags();

            if(drawingChangeSet.postShares)
                 _protected.drawPostShares();
             
            if(drawingChangeSet.lastView)
                 _protected.drawLastView();

            // pass redraw to comments
            _protected.commentsLayer.redraw();
        }
        _protected.drawingManager.setDrawingChangeSetToDefault();
    };
    
    me.undraw = function(){
        me.getLayer().fadeOut(function(){
             me.getLayer().detach();
        });
    }
    
    me.destroy = function(){
        me.undraw();
        _protected.post.getPostData().text.unlisten(_protected.onTextChanged);
        _protected.post.getPostData().tags.unlisten(_protected.onTagsChanged);
        _protected.post.getPostData().postShares.unlisten(_protected.onPostSharesChanged);
        _protected.post.getPostData().lastView.unlisten(_protected.onLastViewChanged);
        _protected.post.getPostData().updated.unlisten(_protected.onLastViewChanged);
        _protected.post.detachEvent('/post/deleted/', _protected.onDeleted);
        me.fireEvent('/destroying/', me);
        delete _protected.post;
        delete me;
        delete _protected;
    }
    
    me.getLayer = function(){
        return _protected.$post;
    };
    
    me.getPost = function(){
        return _protected.post;
    };
    
    me.getCommandBar = function(){
        return _protected.commands.$bar;
    }
    
    _protected.drawText = function(){
        _protected.$post.find('.content .text').html(_protected.convertTextToHtml());
    };
    
    _protected.drawLastView = function(){
        if(_protected.post.getPostData().lastView.get() == null || _protected.post.getPostData().updated.get() > _protected.post.getPostData().lastView.get()){
            me.getLayer().removeClass('viewed');
        }
        else {
            me.getLayer().addClass('viewed');
        }
    };
    
    _protected.drawTags = function(){
        var $tag = null;
        _protected.$meta.find('.tags ul').html('');
        $.each(_protected.post.getPostData().tags.get(), function(key, element){
            $tag = $('#templates #template-tag .tag').clone();
            $tag.find('span').html(element);
            _protected.$meta.find('.tags ul').append($tag);
        });
      
    };
    
    _protected.drawPostShares = function(){
        if(_protected.post.getPostData().postShares.get().length == 0){
            _protected.$meta.find('.sharing').hide();
            _protected.$meta.find('.private-sharing').css('display', '');
        }
        else {
            _protected.$meta.find('.private-sharing').hide();
           
            _protected.$meta.find('.sharing a span.count').html(_protected.post.getPostData().postShares.get().length);
            // if multiple persons add a trailing s;
           
            if(_protected.post.getPostData().postShares.get().length > 1 ){
                _protected.$meta.find('.sharing a span.persons').html('persons');
            }
            else{
                _protected.$meta.find('.sharing a span.persons').html('person');
            }
            
            var names = '';
            var first = true;
            $.each(_protected.post.getPostData().postShares.get(), function(key, element){
                if(first){
                    first = false;
                }
                else {
                    names +=  ', ';
                }
                names +=  Dashbird.Controllers.User.getNameForUser(element);
            });
            _protected.$meta.find('.sharing a').attr('title', names);
            _protected.$meta.find('.sharing a').tooltip();
            _protected.$meta.find('.sharing').css('display', '');
        }
    }

    _protected.convertTextToHtml = function(){
        var text = _protected.post.getPostData().text.get();
        text = Dashbird.Utils.convertLineBreaks(text)
        var search = new Array(
            /\[img\](.*?)\[\/img\]/g,
            /\[b\](.*?)\[\/b\]/g,
            /\[url\](http(s?):\/\/[^ \\"\n\r\t<]*?)\[\/url\]/g,
            /\[youtube\]http(s?):\/\/www.youtube.com\/embed\/(.*?)\[\/youtube\]/g,
            /\[vimeo]http(s?):\/\/player.vimeo.com\/video\/(.*?)\[\/vimeo]/g
            );
     
        var replace = new Array(
            "<img src=\"$1\" alt=\"An image\">",
            "<strong>$1</strong>",
            '<div class="media link-preview" data-url="$1"><a class="pull-left thumbnail" href="#"><img class="media-object" src="http://placehold.it/120x70&text=no+image"></a><div class="media-body"><h6 class="media-heading title">Loading ...</h6><p class="muted">$1</p><p class="description">Loading ...</p></div></div>',
            '<div class="media youtube-preview" data-id="$2"><a class="pull-left thumbnail" href="#"><img class="media-object" src="https://img.youtube.com/vi/$2/1.jpg"></a><div class="media-body"><h6 class="media-heading title">Loading ...</h6><p class="muted">www.youtube.com</p><p class="description">Loading ...</p></div></div>',
            '<div class="media vimeo-preview" data-id="$2"><a class="pull-left thumbnail" href="#"><img class="media-object" src="http://placehold.it/120x70&text=no+image"></a><div class="media-body"><h6 class="media-heading title">Loading ...</h6><p class="muted">www.vimeo.com</p><p class="description">Loading ...</p></div></div>'
            );
        for(var i = 0; i < search.length; i++) {
            text = text.replace(search[i],replace[i]);
        }
        var $html =  $('<div>' + text  + '</div>'); // add wrapper so .find is possible
        
        
        

        // link preview
        $html.find('div.link-preview').each(function(){
            var $this = $(this);
            var url = $this.data('url');
            $.getJSON('http://api.embed.ly/1/oembed', {url : url, key : Dashbird.InitialData.EmbedlyKey}, function(data) {
               $this.find('img').attr('src', data.thumbnail_url);
               $this.find('.title').html(data.title);
               
               if(typeof(data.description) == 'undefined'){
                   data.description = 'No description';
               }
               
               if(data.description.length > 300){
                   data.description = data.description.substring(0, 300) + '...';
               }
               $this.find('.description').html(data.description);
            });
            $this.click(function(e){
                e.preventDefault();
                window.open(url);
            });
        });
       
        

        
        // youtube preview
        // we don't want a page full with video objects
        $html.find('div.youtube-preview').each(function(){
            var $this = $(this);
            var id = $this.data('id');
            $.getJSON('https://gdata.youtube.com/feeds/api/videos/' + id +'?v=2&alt=jsonc', {}, function(data) {
               
               $this.find('.title').html(data.data.title);
               if(data.data.description.length > 300){
                   data.data.description = data.data.description.substring(0, 300) + '...';
               }
               $this.find('.description').html(data.data.description);
            });
            $this.click(function(e){
                e.preventDefault();
                $this.replaceWith("<iframe class='youtube'  width='480' height='270'  src='https://www.youtube.com/embed/" + id + "?autoplay=1' frameborder='0' allowfullscreen></iframe>");
            });
        });
        
        // vimeo preview
        // we don't want a page full with video objects
        $html.find('div.vimeo-preview').each(function(){
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                    type: 'GET',
                    url: 'https://vimeo.com/api/v2/video/' + id +'.json',
                    dataType: 'jsonp',
                    success: function (data) {
                            data = data[0];
                            $this.find('img').attr('src', data.thumbnail_large);
                            $this.find('.title').html(data.title);
                            if(data.description.length > 300){
                                data.description = data.description.substring(0, 300) + '...';
                            }
                            $this.find('.description').html(data.description);
                    },
                    jsonp: 'callback'
            });
            $this.click(function(e){
                e.preventDefault();
                $this.replaceWith("<iframe class='youtube'  width='480' height='270'  src='https://player.vimeo.com/video/" + id + "?autoplay=1' frameborder='0' width='480' height='270' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
            });
        });
        
      
        // return inside of wrapper
        return $html.contents();
    }
                
                              
    return me;
});