Dashbird.PostHtmlLayer =  SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$post = null;
    _protected.$meta = null;
    _protected.$comments = null;
    
    _protected.allowedToRedraw = false;
    
    _protected.changeSet = {};
    
    _protected.setChangeSetToDefault = function(){
        _protected.changeSet = {
            'text' : false,
            'postShares' : false,
            'comments' : false,
            'tags' : false,
            'lastView' : false
        };
    }
    
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
        
        if(_protected.post.isFromCurrentUser){
            _protected.$post = Dashbird.Templates.getTemplate('post');
        }
        else {
            _protected.$post = Dashbird.Templates.getTemplate('foreign-post');
        }
        
        // create jquery shortcuts
        _protected.$meta =  _protected.$post.find('.content .meta');
        _protected.commands.$bar = _protected.$post.find('.content .command-bar.popup');
        _protected.$comments = _protected.$post.find('.comments');
        
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
        _protected.drawComments();
        

      
        // show options
        _protected.$post.mouseover(function(){
            _protected.commands.$bar.show();
        });
        _protected.$post.mouseleave(function (){
            _protected.commands.$bar.hide();
        }); 
        
        _protected.$post.data('post', me);
        
        _protected.$meta.find('.notViewed').click(_protected.post.setLastView);
        
        
        _protected.setChangeSetToDefault();
        // attach listener
        _protected.post.getPostData().text.listen(_protected.onTextChanged);
        _protected.post.getPostData().comments.listen(_protected.onCommentsChanged);
        _protected.post.getPostData().tags.listen(_protected.onTagsChanged);
        _protected.post.getPostData().postShares.listen(_protected.onPostSharesChanged);
        _protected.post.getPostData().lastView.listen(_protected.onLastViewChanged);
        _protected.post.attachEvent('/post/deleted/', _protected.onDeleted);
    }
    
    _protected.onTextChanged = function(){
        _protected.allowedToRedraw ? _protected.drawText() : _protected.changeSet.text = true;
    }
    
    _protected.onCommentsChanged = function(){
        _protected.allowedToRedraw ?  _protected.drawComments() : _protected.changeSet.comments = true;
    }
    
    _protected.onTagsChanged = function(){
        _protected.allowedToRedraw ?  _protected.drawTags() : _protected.changeSet.tags = true;
    }
    
    _protected.onPostSharesChanged = function(){
         _protected.allowedToRedraw ? _protected.drawPostShares() : _protected.changeSet.postShares = true;
    }
    
    _protected.onLastViewChanged = function(){
         _protected.allowedToRedraw ? _protected.drawLastView() : _protected.changeSet.lastView = true;
    }
    
    _protected.onDeleted = function(){
         _protected.allowedToRedraw ? me.destroy() : _protected.changeSet.isDeleted = true;
    }
    
    _protected.redraw = function(){
         if(_protected.changeSet.isDeleted == true){
             me.destroy();
         }
         else {
            if(_protected.changeSet.text == true)
                 _protected.drawText();

            if(_protected.changeSet.comments == true)
                 _protected.drawComments();

            if(_protected.changeSet.tags == true)
                 _protected.drawTags();

            if(_protected.changeSet.postShares == true)
                 _protected.drawPostShares();
             
            if(_protected.changeSet.lastView == true)
                 _protected.drawLastView();
        }
        _protected.setChangeSetToDefault();
    };
    
    me.undraw = function(){
        me.getLayer().fadeOut(function(){
             me.getLayer().detach();
        });
    }
    
    me.destroy = function(){
        me.undraw();
        _protected.post.getPostData().text.unlisten(_protected.onTextChanged);
        _protected.post.getPostData().comments.unlisten(_protected.onCommentsChanged);
        _protected.post.getPostData().tags.unlisten(_protected.onTagsChanged);
        _protected.post.getPostData().postShares.unlisten(_protected.onPostSharesChanged);
        _protected.post.getPostData().lastView.unlisten(_protected.onLastViewChanged);
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
            _protected.$meta.find('.notViewed').show();
            me.getLayer().removeClass('viewed');
        }
        else {
            _protected.$meta.find('.notViewed').hide();
            me.getLayer().addClass('viewed');
        }
        // redraw comments
        // todo: use a better solution
        _protected.drawComments();
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
                names +=  Dashbird.User.getNameForUser(element);
            });
            _protected.$meta.find('.sharing a').attr('title', names);
            _protected.$meta.find('.sharing a').tooltip();
            _protected.$meta.find('.sharing').css('display', '');
        }
    }

    _protected.drawComments = function(){
        _protected.$comments.empty();
        var $template = Dashbird.Templates.getTemplate('post-comment');
        $.each(_protected.post.getPostData().comments.get(),function(index, comment){
            var $comment = $template.clone();
            if(comment.datetime <= me.getPost().getPostData().lastView.get()){
                    $comment.addClass('viewed');
            }
              
            
            $comment.find('.text').html(Dashbird.Utils.convertLineBreaks(comment.text));
            $comment.find('.meta .info .username').html(comment.user.name);
            $comment.find('.meta .info .date').html(Dashbird.Utils.convertDate(comment.datetime));
            if(Dashbird.User.isCurrentUser(comment.user.userId)){
           
                
                // show options
                $comment.mouseover(function(){
                    $comment.find('.command-bar.popup').show();
                });
                $comment.mouseleave(function (){
                    $comment.find('.command-bar.popup').hide();
                });
                // delete comment button
                $comment.find('.command-bar.popup .command-delete').click(function(){
                    me.getPost().setLastView();
                    Dashbird.Modal.show({
                        headline: 'Deleting comment', 
                        text : 'Do you really want to delete this comment?',
                        'cancel-button-text' : 'No, no, I am sorry', 
                        'submit-button-text' : 'Remove the rubish!', 
                        callback : function(){
                            me.getPost().deleteComment(comment.commentId, function(){
                                 me.getPost().setLastView();
                            });
                        }
                    })
                });
            }
            _protected.$comments.append($comment);
        });
        
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
            "<a href=\"$1\" target=\"blank\">$1</a>",
            '<div class="media youtube-preview" data-id="$2"><a class="pull-left thumbnail" href="#"><img class="media-object" src="https://img.youtube.com/vi/$2/1.jpg"></a><div class="media-body"><h6 class="media-heading title">Loading ...</h6><p class="muted">www.youtube.com</p><p class="description">Loading ...</p></div></div>',
            '<div class="media vimeo-preview" data-id="$2"><a class="pull-left thumbnail" href="#"><img class="media-object" src=""></a><div class="media-body"><h6 class="media-heading title">Loading ...</h6><p class="muted">www.vimeo.com</p><p class="description">Loading ...</p></div></div>'
            //"<iframe class='vimeo' src='$1' width='480' height='270' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>"
            );
        for(var i = 0; i < search.length; i++) {
            text = text.replace(search[i],replace[i]);
        }
        var $html =  $('<div>' + text  + '</div>'); // add wrapper so .find is possible
        
        // youtube preview
        // we don't want a page full with video objects
        $html.find('div.youtube-preview').each(function(){
            var $this = $(this);
            var id = $this.data('id');
            $.getJSON('http://gdata.youtube.com/feeds/api/videos/' + id +'?v=2&alt=jsonc', {}, function(data) {
               
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