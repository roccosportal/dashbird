if(SimpleJSLib===undefined){
        var SimpleJSLib = {};
}
SimpleJSLib.EventHandler = function(){
        var me = {},
        _private = {};
    
        _private.listeners = [];
        me.fire = function (name, data){
                for (var i = 0; i < _private.listeners.length; i++) {
                        if(_private.listeners[i].name==name){
                                _private.listeners[i].callback(data);
                        }
                }
        };
        me.attach = function (name, callback){
                _private.listeners.push({
                        name : name, 
                        callback : callback
                });
        };
        me.detach = function (name, callback){
                var indexes = [];
                for (var i = 0; i < _private.listeners.length; i++) {
                        if(_private.listeners[i].name==name && _private.listeners[i].callback==callback){
                                indexes.push(i);
                        }
                }
                for (var j = 0; j < indexes.length; j++) {
                        _private.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
                }
        };
        
           // for inheritance
        me._private = _private;
        
        return me;	
};
if(SimpleJSLib===undefined){
        var SimpleJSLib = {};
}
SimpleJSLib.SingleRequestQueue = function(){
        var me = {},
        _private = {};
                
        _private.latestRequestId = 0;
        _private. timeout = null;
        me.setTimeout = function (timeout){
                _private. timeout = timeout;
        };
        
        me.getLatestRequestId = function(){
                return _private.latestRequestId;
        }
        
        me.addToQueue = function(data, callback){
                if(_private. timeout===null){
                        throw "Timeout was not set. Use the setTimeout function of this object."
                }
                _private.latestRequestId++;
                var currentRequestId = _private.latestRequestId;
                setTimeout(function (){
                        if(_private.latestRequestId===currentRequestId){
                                callback(data);
                        }
                }, _private. timeout);
        };
        
        me.runAsynchronRequest = function(){
                _private.latestRequestId++;
                var currentRequestId = _private.latestRequestId;
                var requestObject = {
                        currentRequestId : currentRequestId,
                        isLatestRequest : function(){
                                return _private.latestRequestId===currentRequestId;
                        }
                };
                return requestObject;
        };
        
        // for inheritance
        me._private = _private;
        
        return me;	
};
var AJAX = {
   'STATUS': 'STATUS',
   'STATUS_SUCCESS': 'STATUS_SUCCESS',
   'STATUS_ERROR': 'STATUS_ERROR',
   'MESSAGE': 'MESSAGE',
   'DATA': 'DATA',
   'IS_LOGGED_IN': 'IS_LOGGED_IN',
   'IS_NOT_LOGGED_IN': 'IS_NOT_LOGGED_IN',
   'ALREADY_LOGGED_IN': 'ALREADY_LOGGED_IN',
   'WRONG_DATA': 'WRONG_DATA'
   
};
if(typeof Dashbird == "undefined"){
    var Dashbird = {};
}
Dashbird.Post = function(){
    var me = {},
    _private = {};
    
    _private.editPostBoxTags = null;
    _private.postSharesBoxPostShares = null;
     
    me.postData = null;
    me.$post = null;
    me.$meta = null;
    me.$comments = null;
    me.commands = {
        $bar : null
    }
    me.init = function (postData){
        me.postData = postData;
    }
    me.create = function(){
        if(Dashbird.User.getUser().userId === me.postData.user.userId){
            me.$post = Dashbird.Templates.getTemplate('post');
        }
        else {
            me.$post = Dashbird.Templates.getTemplate('foreign-post');
        }
        
        // create jquery shortcuts
        me.$meta =  me.$post.find('.content .meta');
        me.commands.$bar = me.$post.find('.content .command-bar.popup');
        me.$comments = me.$post.find('.comments');
        
        me.drawText();
        me.$meta.find('.info .username').html(me.postData.user.name);
        me.$meta.find('.info .date').html(Dashbird.Board.convertDate(me.postData.created));
        
        if(Dashbird.User.getUser().userId === me.postData.user.userId){
            me.commands.edit = Dashbird.Commands.Edit(me);
            me.commands.edit.init();
            
            me.commands.share = Dashbird.Commands.Share(me);
            me.commands.share.init();
            
            me.commands.remove = Dashbird.Commands.Remove(me);
            me.commands.remove.init();
        }
        me.drawTags();
        me.drawPostShares();
        _private.drawComments();
        
        me.commands.comment = Dashbird.Commands.Comment(me);
        me.commands.comment.init();
      
        // show options
        me.$post.mouseover(function(){
            me.commands.$bar.show();
        });
        me.$post.mouseleave(function (){
            me.commands.$bar.hide();
        });     
        
        me.$post.data('post', me);
        return me.$post;
    },
    
    me.drawText = function(){
        me.$post.find('.content .text').html(me.bbcode(me.postData.text.replace(/\n/g,'<br />')));
    };
    
    me.update = function(text, tags){
        $.getJSON('api/post/edit/', {
            postId : me.postData.postId, 
            text : text,
            tags: tags
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                Dashbird.Board.fire('post#save', data[AJAX.DATA]);
                // save status
                me.postData.tags =  data[AJAX.DATA].tags;
                me.postData.text =  data[AJAX.DATA].text;
                me.postData.updated = data[AJAX.DATA].updated;
                me.drawText();
                me.drawTags();
            }
        });
    };
    
   
                
    me.deletePost = function(){
        $.getJSON('api/post/delete/', {
            postId : me.postData.postId
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
            // do nothing
            }
        });
        me.$post.fadeOut("slow", function(){       
            me.$post.detach();
        });                     
    };
    
    me.drawTags = function(){
        var $tag = null;
        me.$meta.find('.tags ul').html('');
        $.each(me.postData.tags, function(key, element){
            $tag = $('#templates #template-tag .tag').clone();
            $tag.find('span').html(element);
            me.$meta.find('.tags ul').append($tag);
        });
      
    };
    
    me.drawPostShares = function(){
        if(me.postData.postShares.length == 0){
            me.$meta.find('.sharing').hide();
            me.$meta.find('.private-sharing').css('display', '');
        }
        else {
            me.$meta.find('.private-sharing').hide();
           
            me.$meta.find('.sharing a span.count').html(me.postData.postShares.length);
            // if multiple persons add a trailing s;
           
            if(me.postData.postShares.length > 1 ){
                me.$meta.find('.sharing a span.persons').html('persons');
            }
            else{
                me.$meta.find('.sharing a span.persons').html('person');
            }
            
            var names = '';
            var first = true;
            $.each(me.postData.postShares, function(key, element){
                if(first){
                    first = false;
                }
                else {
                    names +=  ', ';
                }
                names +=  Dashbird.User.getNameForUser(element);
            });
            me.$meta.find('.sharing a').attr('title', names);
            me.$meta.find('.sharing a').tooltip();
            me.$meta.find('.sharing').css('display', '');
        }
    }
        
    me.setPostShares = function(userIds){
        $.getJSON('api/post/shares/set/', {
            postId : me.postData.postId, 
            userIds : userIds
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.postData.postShares = userIds;
                me.drawPostShares();
            }
        });
    }
    
    
    me.addComment = function(text, callback){
        $.getJSON('api/post/comment/add/', {
            postId : me.postData.postId, 
            text : text
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.postData.updated = data[AJAX.DATA].post.updated;
                
                Dashbird.Board.fire('post#addComment', {
                    post : me, 
                    data : data[AJAX.DATA]
                })
                me.postData.comments.push(data[AJAX.DATA].comment);
                _private.drawComments();
              
            }
            if(callback != null){
                callback();
            }
        });
    }
    me.deleteComment = function(id){
        $.getJSON('api/post/comment/delete/', {
            commentId : id
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.postData.updated = data[AJAX.DATA].post.updated;
                
                Dashbird.Board.fire('post#deleteComment', {
                    post : me,
                    data : data[AJAX.DATA]
                })
                // rebuild comments
                var comments = [];
                $.each(me.postData.comments,function(index, comment){
                    if(comment.commentId.toString() !== id.toString()){
                        comments.push(comment);
                    }
                });
                me.postData.comments = comments;
                _private.drawComments();
            }
        });
    }
    
    _private.drawComments = function(){
        me.$comments.empty();
        var $template = Dashbird.Templates.getTemplate('post-comment');
        $.each(me.postData.comments,function(index, comment){
            var $comment = $template.clone();
            $comment.find('.text').html(comment.text.replace(/\n/g,'<br />'));
            $comment.find('.meta .info .username').html(comment.user.name);
            $comment.find('.meta .info .date').html(Dashbird.Board.convertDate(comment.datetime));
            if(Dashbird.User.getUser().userId === comment.user.userId){
                // show options
                $comment.mouseover(function(){
                    $comment.find('.command-bar.popup').show();
                });
                $comment.mouseleave(function (){
                    $comment.find('.command-bar.popup').hide();
                });
                // delete comment button
                $comment.find('.command-bar.popup .command-delete').click(function(){
                    Dashbird.Modal.show({
                        headline: 'Deleting comment', 
                        text : 'Do you really want to delete this comment?',
                        'cancel-button-text' : 'No, no, I am sorry', 
                        'submit-button-text' : 'Remove the rubish!', 
                        callback : function(){
                            me.deleteComment(comment.commentId);
                        }
                    })
                });
            }
            me.$comments.append($comment);
        });
        
    }
    
    me.bbcode = function(text){
        var search = new Array(
            /\[img\](.*?)\[\/img\]/g,
            /\[b\](.*?)\[\/b\]/g,
            /\[url\](http(s?):\/\/[^ \\"\n\r\t<]*?)\[\/url\]/g,
            /\[youtube\](.*?)\[\/youtube\]/g,
            /\[vimeo](.*?)\[\/vimeo]/g
            );
     
        var replace = new Array(
            "<img src=\"$1\" alt=\"An image\">",
            "<strong>$1</strong>",
            "<a href=\"$1\" target=\"blank\">$1</a>",
            "<iframe class='youtube'  width='480' height='270'  src='$1' frameborder='0' allowfullscreen></iframe>",
            "<iframe class='vimeo' src='$1' width='480' height='270' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>");
        for(var i = 0; i < search.length; i++) {
            text = text.replace(search[i],replace[i]);
        } 
        return text;
    }
                
                              
    return me;
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Base = function(post){
    var me = {};
    
    me.set$ = function(selector){
         me.$ = post.$post.find('.content .command.' + selector);
    }
    
    me.hideCommands = function(callback){
         post.$post.find('.content .command').fadeOut().promise().done(callback);
    }
    
    return me;
};
if(typeof Dashbird == "undefined"){
    var Dashbird = {};
}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Comment = function(post){
    var me = Dashbird.Commands.Base(post),
    _private = {};
     
    _private.isOnDemandInited = false;

    me.init = function(){
        post.commands.$bar.find('.command-comment').click(me.show);
        me.set$('command-comment');
      
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.cancel-button').click(function(){
                me.$.fadeOut();
            });
        
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                post.addComment(me.$.find('textarea').val(), function(){
                    me.$.fadeOut(); 
                })
            });
            _private.isOnDemandInited = true;
        }
    };
    
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        // fade out all opend options
        me.hideCommands(function(){
            // show option
            me.$.fadeIn(function(){
                me.$.find('textarea').focus();
            });
        });
    };
    return me;
};
if(typeof Dashbird == "undefined"){
    var Dashbird = {};
}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Edit = function(post){
    var me = Dashbird.Commands.Base(post),
    _private = {};
        
    _private.tags = null;
    _private.bbcode = {};
     
    _private.isOnDemandInited = false;
     
    me.init = function(){
        post.commands.$bar.find('.command-edit').click(me.show);
        me.set$('command-edit');
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                me.addTag();
                post.update(me.$.find('textarea').val(), _private.tags);
                me.$.fadeOut();
            });
            
            me.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                me.$.fadeOut();
            });
            me.$.find('.add-tag-input button').click(me.addTag);
            me.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _private.addTag(e);
                }
            });
        
            _private.bbcode.link = Dashbird.BBCode.Link()
            _private.bbcode.link.init(me.$.find('.command-bar .command-link'), me.$.find('textarea'));
            _private.bbcode.video = Dashbird.BBCode.Video()
            _private.bbcode.video.init(me.$.find('.command-bar .command-video'), me.$.find('textarea'));
            _private.bbcode.image = Dashbird.BBCode.Image()
            _private.bbcode.image.init(me.$.find('.command-bar .command-image'), me.$.find('textarea'));
            _private.bbcode.bold = Dashbird.BBCode.Bold()
            _private.bbcode.bold.init(me.$.find('.command-bar .command-bold'), me.$.find('textarea'));
        
        
            me.$.find('.tag-alert').alert();
            _private.isOnDemandInited = true;
        }
    };
    
     
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        _private.tags = post.postData.tags;
        // fade out all opend options
        me.hideCommands(function(){
            me.$.find('textarea').html(post.postData.text);
            me.hideTagAlert();
            me.drawTags();
            // show option
            me.$.fadeIn(function(){
                me.$.find('textarea').focus();
            });
        });
    };
    
    me.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag = me.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _private.tags);
            if(position===-1){ // only add if not already in tags
                me.drawTag(tag);
                _private.tags.push(tag);
                me.$.find('.add-tag-input input').val('');
                me.hideTagAlert();
            }
            else {
                me.showTagAlert();
            }
            
        }
    };
    
    me.showTagAlert = function(){
        me.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        me.$.find('.tag-alert').append($alert);
    };
    me.hideTagAlert = function(){
        me.$.find('.tag-alert').html('');
    };
    
      
    me.drawTags = function(){
        me.$.find('.tags').html('');
        $.each(_private.tags, function(key, element){
            me.drawTag(element);
        });
    };
    
    me.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _private.tags);
            if(position!== -1){ 
                _private.tags.splice(position, 1);
            }
               
        });
        me.$.find('.tags').append($tag);
    };
    
     
    return me;
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Remove = function(post){
     var me = Dashbird.Commands.Base(post),
     _private = {};
     
     me.init = function(){
        post.commands.$bar.find('.command-remove').click(_private.removePostClick);
    };
    
    _private.removePostClick = function(e){
        e.preventDefault();
        Dashbird.Modal.show({
            headline: 'Deleting post', 
            text : 'Do you really want to remove this post?',
            'cancel-button-text' : 'No, no, I am sorry', 
            'submit-button-text' : 'Remove the rubish!', 
            callback : function(){
                post.deletePost();
            }
        });
    }
    
    return me;
};
if(typeof Dashbird == "undefined"){
    var Dashbird = {};
}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Share = function(post){
    var me = Dashbird.Commands.Base(post),
    _private = {};
        
    _private.isOnDemandInited = false;
        
    me.init = function(){
        post.commands.$bar.find('.command-share').click(me.show);
        me.set$('command-share');
       
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                post.setPostShares(_private.postShares);
                me.$.fadeOut();
            });
            me.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                me.$.fadeOut();
            });
            _private.isOnDemandInited = true;
        }
    };
    
    
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        me.hideCommands(function(){
            _private.postShares = post.postData.postShares;
            _private.draw();
            // show option
            me.$.fadeIn(function(){
                
                });
        });
    };
    
    _private.draw = function(){
        var $share = null;
        me.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_private.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _private.shareChange($(this), element.userId);
            });
            me.$.find('.shares').append($share);
        });
    }
    
    _private.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _private.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _private.postShares);
            if(position!== -1){ 
                _private.postShares.splice(position, 1);
            }
        }
    }
     
    return me;
        
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.BBCode == "undefined"){Dashbird.BBCode  = {};}
Dashbird.BBCode.Bold = function(){
    var me = {}, _private = {};
    
    _private.$button = null;
    _private.$textarea = null;
    me.init = function($button, $textarea){
        _private.$button = $button;
        _private.$textarea = $textarea;
        _private.$button.click( _private.onButtonClick);
    };
    
    _private.onButtonClick = function(e){
        e.preventDefault();
        _private.$textarea.insertAtCaret('[b]' + _private.$textarea.getSelection().text + '[/b]');
    }
    return me;
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.BBCode == "undefined"){Dashbird.BBCode  = {};}
Dashbird.BBCode.Image = function(){
    var me = {}, _private = {};
    
    _private.$button = null;
    _private.$textarea = null;
    me.init = function($button, $textarea){
        _private.$button = $button;
        _private.$textarea = $textarea;
        _private.$button.click( _private.onButtonClick);
    };
    
    _private.onButtonClick = function(e){
        e.preventDefault();
        var selection = _private.$textarea.getSelection();
        if(selection.text != ''){
             _private.$textarea.insertAtCaret('[img]' + selection.text +'[/img]')
        }
        else {
        
            var $modalBody = $('#templates #template-bbcode-image-modal .modal-body').clone();
            Dashbird.Modal.show({
                headline : 'Image',
                'submit-button-text' : 'Add',
                '$modal-body' : $modalBody,
                callback : function($modal){
                    _private.$textarea.insertAtCaret('[img]' + $modal.find('.image').val() +'[/img]');
                },
                onShown : function($modal){
                    $modal.find('.image').focus();
                    $modal.find('.image').keydown(function(e){
                        if(e.keyCode == 13){ // enter
                            e.preventDefault();
                            $modal.find('.submit-button').click();
                        }
                    });
                }
            });
        }
    }
    return me;
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.BBCode == "undefined"){Dashbird.BBCode  = {};}
Dashbird.BBCode.Link = function(){
    var me = {}, _private = {};
    
    _private.$button = null;
    _private.$textarea = null;
    me.init = function($button, $textarea){
        _private.$button = $button;
        _private.$textarea = $textarea;
        _private.$button.click( _private.onButtonClick);
    };
    
    _private.onButtonClick = function(e){
        e.preventDefault();
         var selection = _private.$textarea.getSelection();
        if(selection.text != ''){
             _private.$textarea.insertAtCaret('[url]' + selection.text +'[/url]')
        }
        else {
            var $modalBody = $('#templates #template-bbcode-link-modal .modal-body').clone();
            Dashbird.Modal.show({
                headline : 'Link',
                'submit-button-text' : 'Add',
                '$modal-body' : $modalBody,
                callback : function($modal){
                    _private.$textarea.insertAtCaret('[url]' + $modal.find('.link').val() +'[/url]');
                },
                onShown : function($modal){
                    $modal.find('.link').focus();
                    $modal.find('.link').keydown(function(e){
                        if(e.keyCode == 13){ // enter
                            e.preventDefault();
                            $modal.find('.submit-button').click();
                        }
                    });
                }
            });
        }
    };
    return me;
};
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.BBCode == "undefined"){Dashbird.BBCode  = {};}
Dashbird.BBCode.Video = function(){
    var me = {}, _private = {};
    
    _private.$button = null;
    _private.$textarea = null;
    me.init = function($button, $textarea){
        _private.$button = $button;
        _private.$textarea = $textarea;
        _private.$button.click( _private.onButtonClick);
    };
    
    _private.onButtonClick = function(e){
        e.preventDefault();
        var $modalBody = $('#templates #template-bbcode-video-modal .modal-body').clone();
        Dashbird.Modal.show({
            headline : 'Video',
            'submit-button-text' : 'Add',
            '$modal-body' : $modalBody,
            callback : _private.onCallback,
            onShown : function($modal){
                $modal.find('.video').focus();
                $modal.find('.video').keydown(function(e){
                      if(e.keyCode == 13){ // enter
                          e.preventDefault();
                          $modal.find('.submit-button').click();
                      }
                  });
            }
        });
        
    }
    
    _private.onCallback = function($modal){
        $modal.find('.alerts').html('');
        var video = $modal.find('.video').val();
        if(video.indexOf('youtube.com') !== -1){
            var pos = video.indexOf('=');
            if(pos !== -1){
                video = 'https://www.youtube.com/embed/' + video.substring(pos + 1, video.length);
                _private.$textarea.insertAtCaret('[youtube]' + video +'[/youtube]');
                return true;
            }
        }
        else if(video.indexOf('vimeo.com') !== -1){
            var pos = video.indexOf('vimeo.com/');
            if(pos !== -1){
                video = 'https://player.vimeo.com/video/' + video.substring(pos + 10, video.length);
                _private.$textarea.insertAtCaret('[vimeo]' + video +'[/vimeo]');
                return true;
            }
        }
        var $alert  = $('#templates #template-bbcode-video-modal-alert .alert').clone();
        $modal.find('.alerts').append($alert);
        return false;
    };
    return me;
};
if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Board = function(){
    var me = SimpleJSLib.EventHandler(),
    _private = {};
    _private.$commandBar = null;
    _private.$posts = null;
    _private.posts = [];
    _private.loadPostsAjaxRequestQueue = null;
    _private.boardKeyCapture = false;
    _private.boardBarKeyCapture = false;
        
    me.pager = {};
    me.pager.$morePosts = null;
    me.pager.startPosition = 0;
    me.pager.postCount = 10;
    me.pager.hasMorePosts = false;
    
    me.$loading = null;

    me.init = function (){    
        _private.$posts = $('#board .posts');
        _private.$loading = $('#board .loading');
        _private.$viewAll = $('#board .view-all')
        _private.$viewAll.click(function(e){
             e.preventDefault();
             _private.$viewAll.hide();
             me.refreshPosts();
        })
        me.pager.$morePosts = $('#board .more-posts')
        me.pager.$morePosts.click(function(e){
            e.preventDefault();
            me.pager.startPosition =  me.pager.startPosition + me.pager.postCount;
            me.loadPosts();
        });

       
        
        $('#board').show();
        $('#side-bar').show();
        
        _private.loadPostsAjaxRequestQueue = SimpleJSLib.SingleRequestQueue();
    };
                
    me.addToTop = function(postData){
        var post = Dashbird.Post();
        post.init(postData);
        var $post = post.create();      
                                             
        var first = $('#board .posts .post:first');
        if(first.length != 0){
            first.before($post);
        }
        else {
            _private.$posts.append($post);
        }     
    };
    
    me.reorderPosts = function(){
        var $posts =  $('.posts .post');
        $posts.detach();
        $posts.sort(function (a, b) {
            var contentA = $(a).data('post').postData.updated;
            var contentB =$(b).data('post').postData.updated;
            return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
        });
        _private.$posts.append($posts);
    }
    
    me.refreshPosts = function(){
         me.pager.startPosition = 0;
        _private.$posts.html('');
        _private.posts = [];
        me.loadPosts();
    };
    
   
    
    me.apiPostsUpdatedGet = function(options, callback){
        $.getJSON('/api/posts/updated/get/', options, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }
    
    me.getHash = function(postId, callback){
        $.getJSON('api/post/hash/get/', {postId : postId}, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }

    me.loadPosts = function(){
        me.pager.$morePosts.hide();
        _private.$loading.show();              
        var request = _private.loadPostsAjaxRequestQueue.runAsynchronRequest();
        $.getJSON('api/posts/load/', {
            search : Dashbird.Search.getSearchPhrase(),
            'start-position' : me.pager.startPosition,
            'post-count' : me.pager.postCount
        }, function(data) {
            if(request.isLatestRequest()){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.$loading.hide();            
                    var posts = data[AJAX.DATA]['posts'];
                    for (var i = 0; i <  posts.length; i++) {
                        var postData = posts[i];
                        var post = Dashbird.Post();
                        post.init(postData);
                        _private.posts.push(post);
                        var $post = post.create();
                        _private.$posts.append($post);                         
                    }
                    if(posts.length>0){
                        me.pager.$morePosts.show();
                    }
                }
            }
        });
       
    };
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
     
    me.showSinglePost = function(postId){
        me.pager.$morePosts.hide();
        _private.$viewAll.show();
        _private.$posts.html('');
        $('#navbar .nav .show-board').tab('show');
        _private.$loading.show();        
         me.getPost(postId, function(data) {
            _private.$loading.hide();     
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.fire('showSinglePost', data[AJAX.DATA]);
                me.addToTop(data[AJAX.DATA]);
            }
        });
    }
    
    
    me.getPost = function(postId, callback){
        $.getJSON('api/post/get/', {
            postId : postId
        }, function(data) {
           callback(data);
        });
    };
    
    me.getPosts = function(postIds, callback){
         $.getJSON('api/posts/get/', {
            postIds : postIds
        }, function(data) {
           callback(data);
        });
    }
    
    me.convertDate = function(date){
        return date.substring(0, date.length - 3);
    }
        
    me._private = _private; // for inheritance      
    return me;
}();
if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.LoginBox = function(){
        var me = {},
        _private = {};
                        
        _private.$ = null;
        _private.$name = null;
        _private.$password = null;
        _private.onLoggedIn = function(){
                _private.$.detach();
        };
        
        _private.onLoginFailure = function(){
                _private.$name.parent().parent().addClass('error');
                _private.$password.parent().parent().addClass('error');
        };
        
        me.init = function(){
                _private.$ = $('#login-box');
                _private.$name = $('#login-box-name');
                _private.$password = $('#login-box-password');
				
                _private.$password.keydown(function(e){
                        if(e.keyCode == 13){
                             _private.onSubmit(e);
                        }
                });
                _private.$.submit(_private.onSubmit)
        };
        
        
        me.show = function (){
                _private.$.fadeIn();
                _private.$name.focus();
        };
        
        _private.onSubmit = function(e){
            e.preventDefault();
            Dashbird.User.login(_private.$name.val(),_private.$password.val(), _private.onLoggedIn,  _private.onLoginFailure);
               
        }
        
        me._private = _private; // for inheritance
        return me;
}();
if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Modal = function(){
    var me = {},
    _private = {};
        
    _private.$overlay = null;
    
    _private.options = null;
        
    me.init = function(){
        _private.$modal = $('#modal');
        _private.$defaultModalBody = _private.$modal.find('.modal-body').clone();
    }
    
    _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
           // _private.$modal.modal('hide');
            _private.$modal.find('.cancel-button').click(function(e){
                e.preventDefault();
                _private.$modal.modal('hide');
            });
       
            _private.$modal.find('.submit-button').click(function(e){
                e.preventDefault();
                if(_private.options.callback!=null){
                    var returnValue = _private.options.callback(_private.$modal);
                    if(returnValue !== false){
                        _private.$modal.modal('hide');
                    }
                }
                else {
                    _private.$modal.modal('hide');
                }
                
            });
            
            _private.$modal.on('shown',function(){
                if(_private.options.onShown!=null){
                    _private.options.onShown(_private.$modal);
                }
            });
            
            _private.isOnDemandInited = true;
        }
    }
        
    me.show = function(options){
        _private.options = $.extend({
            headline : '',
            text: '',
            '$modal-body' : null,
            callback : null,
            'cancel-button-text' : 'Cancel',
            'submit-button-text' : 'Submit'
        }, options);
        _private.onDemandInit();
        _private.$modal.find('.headline').html(_private.options.headline);
        if(_private.options['$modal-body']==null){
            _private.$defaultModalBody.find()
            _private.$modal.find('.modal-body').replaceWith(_private.$defaultModalBody.clone());
            _private.$modal.find('.text').html(_private.options.text);
        }
        else {
            _private.$modal.find('.modal-body').replaceWith(_private.options['$modal-body']);
        }
        _private.$modal.find('.cancel-button').html(_private.options['cancel-button-text']);
        _private.$modal.find('.submit-button').html(_private.options['submit-button-text']);
        _private.$modal.modal('show');
    }
   
    
    return me;
}();
if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.NewPost = function(){
    var me = SimpleJSLib.EventHandler(),
    _private = {};
    _private.tags = [];
    _private.bbcode= {};
    
    me.init = function(){
        // new post
        $('#navbar .nav .show-new-post').on('show', _private.onShow);  
    };
    
    _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            _private.$ = $('#new-post');
            
            _private.$.find('.submit-button').click(_private.onSaveClick);
            _private.$.find('.cancel-button').click(_private.onCancelClick);
       
            _private.$.find('.add-tag-input button').click(_private.addTag);
            _private.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _private.addTag(e);
                }
            });
        
            _private.$.find('.tag-alert').alert();
            
            _private.bbcode.link = Dashbird.BBCode.Link()
            _private.bbcode.link.init(_private.$.find('.command-bar .command-link'), _private.$.find('textarea'));
            
            _private.bbcode.video = Dashbird.BBCode.Video()
            _private.bbcode.video.init(_private.$.find('.command-bar .command-video'), _private.$.find('textarea'));
            
            _private.bbcode.image = Dashbird.BBCode.Image()
            _private.bbcode.image.init(_private.$.find('.command-bar .command-image'), _private.$.find('textarea'));
            
            _private.bbcode.bold = Dashbird.BBCode.Bold()
            _private.bbcode.bold.init(_private.$.find('.command-bar .command-bold'), _private.$.find('textarea'));
             
            _private.isOnDemandInited = true;
        }
    };
    
   
    
    _private.onShow = function(){
        _private.onDemandInit();
        _private.tags = [];
        _private.postShares = [];
        _private.$.find('textarea').val('');
        _private.hideTagAlert();
        _private.drawShares();
        _private.$.find('.add-tag-input input').val('');
        _private.$.find('.tags').empty();
        _private.$.find('textarea').focus();
    };
    
    _private.onSaveClick = function(e){
        e.preventDefault();
        _private.addTag();
        $.getJSON('api/post/add/', {
            text :  _private.$.find('textarea').val(),
            tags :  _private.tags,
            shares : _private.postShares
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.fire('newPost', data[AJAX.DATA]);
                Dashbird.Board.refreshPosts();
                $('#navbar .nav .show-board').tab('show');
                  
            }
        });
    }
    
    _private.onCancelClick = function(e){
        e.preventDefault();
        $('#navbar .nav .show-board').tab('show');
    }
    
    _private.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag =_private.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _private.tags);
            if(position===-1){ // only add if not already in tags
                _private.drawTag(tag);
                _private.tags.push(tag);
                _private.$.find('.add-tag-input input').val('');
                _private.hideTagAlert();
            }
            else {
                _private.showTagAlert();
            }
            
        }
    };
    
    _private.showTagAlert = function(){
        _private.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        _private.$.find('.tag-alert').append($alert);
    };
    _private.hideTagAlert = function(){
        _private.$.find('.tag-alert').html('');
    };
    
    
    _private.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _private.tags);
            if(position!== -1){ 
                _private.tags.splice(position, 1);
            }
               
        });
        _private.$.find('.tags').append($tag);
    };
    
    _private.drawShares = function(){
        var $share = null;
        _private.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_private.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _private.shareChange($(this), element.userId);
            });
            _private.$.find('.shares').append($share);
        });
    }
    
    _private.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _private.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _private.postShares);
            if(position!== -1){ 
                _private.postShares.splice(position, 1);
            }
        }
    }
    
    
    
    return me;
}();

if(Dashbird===undefined){
        var Dashbird = {};
}

Dashbird.PluginManager = function (){
    var me = {},
    _private = {};
    
    me.init = function(){
        Dashbird.Plugins.Notifications.init();
        
    };
    
    me.loadData = function(name, callback){
         $.getJSON('api/plugin/data/get/', {
                    name : name
                  
         }, function(data) {
               if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    callback.call(this, data[AJAX.DATA].data);
               }
         });
    }
    
     me.saveData = function(name, data){
         $.post('api/plugin/data/save/', {
                    name : name,
                    data : JSON.stringify(data)
                  
         }, function(data) {
             
         }, 'json');
    }
    
    
    
    return me;
}();
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
    _private.MAXCOUNT = 50;
        
    me.init = function(){
        _private.originalTitle = $('title').text();
        _private.$count = $('#navbar .show-notifications span');
        $('#navbar .nav .show-notifications').on('show', _private.onShow);
        _private.$ = $('#notifications');
        me.loadData(function(){
            me.onFirstTime(function(isFirstTime){
                me.refresh();
                if(!isFirstTime){
                    _private.cleanUp();
                }
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
            Dashbird.Board.apiPostsUpdatedGet({'post-count' : _private.MAXCOUNT},function(data){
                _private.data = {
                    options : {},
                    lastViews : data.dates
                }
                me.saveData();
                callback(true);
            });
        }
        else {
            callback(false);
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
        Dashbird.Board.apiPostsUpdatedGet({'post-count' : _private.MAXCOUNT},function(data){
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
    
    _private.cleanUp = function(){
        if(_private.data.lastViews.length > _private.MAXCOUNT){
            _private.data.lastViews.sort(function (a, b) {
                var contentA = a.updated;
                var contentB = b.updated;
                return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
            });
            var newLastViews = [];
            for(var i = 0; i < _private.MAXCOUNT; i++){
                newLastViews.push(_private.data.lastViews[i]);
            }
            _private.data.lastViews = newLastViews;
            me.saveData();
        }
    }
        
    return me;
    
}();
if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Search = function(){
    var me = {},
    _private = {};
    _private.searchRequestQueue = null;
        
        
    me.init = function(){
        // searching
        _private.$searchBox = $('#search-box');
        _private.searchRequestQueue = SimpleJSLib.SingleRequestQueue();
        _private.searchRequestQueue.setTimeout(300);
        _private.$searchBox.keypress(function(e){
            if(e.keyCode == 13){
                e.preventDefault();
            }
            else {
                _private.searchRequestQueue.addToQueue({}, function(data){
                     Dashbird.Board.refreshPosts();
                     $('#navbar .nav .show-board').tab('show');
                });
            }
        });
    };
        
    me.getSearchPhrase = function(){
        return _private.$searchBox.val();
    };
    
    return me;
}();

if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Settings = function(){
    var me = {},
    _private = {};
   
    _private.persons = {};
    _private.password = {};
    
    me.init = function(){
       $('#navbar .nav .show-settings').on('show', _private.onShow);
       _private.persons.$ =  $('#settings-persons');
       _private.password.$ =  $('#settings-password');
    }
    
     _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
           _private.persons.$.find('.add-person-input .btn').click(_private.persons.addPersonClick);
           var $user = null;
           $.each(Dashbird.User.getUserShares(), function(key, element){
                $user = $('#templates #template-user li').clone();
                $user.find('span').html(element.name);
                _private.persons.$.find('ul').append($user);
           });
           
           
           _private.password.$.find('.submit-button').click( _private.password.changePasswordClick);
           
           
           _private.isOnDemandInited = true;
        }
         _private.password.$.find('.alerts').html('');
    }
    
    _private.onShow = function(){
       _private.onDemandInit();
    }
    
    _private.persons.addPersonClick = function(e){
        e.preventDefault();
        var name = _private.persons.$.find('.add-person-input input').val();
        Dashbird.User.addUserShare(name, function(){
               var $user = $('#templates #template-user li').clone();
               $user.find('span').html(name);
               _private.persons.$.find('ul').append($user);
        });
    };
    
    _private.password.changePasswordClick = function(e){
         e.preventDefault();
         _private.password.$.find('.alerts').html('');
         var oldPassword = _private.password.$.find('.old-password').val();
         var newPassword = _private.password.$.find('.new-password').val();
         Dashbird.User.changePassword(oldPassword, newPassword, function(data){
             var $alert = null;
             if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                 $alert = $('#templates #template-settings-password-alert-success .alert').clone();
             }
             else {
                 $alert = $('#templates #template-settings-password-alert-error .alert').clone();
             }
             _private.password.$.find('.alerts').append($alert);
         })
    }
   
    
    return me;
}();
if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.User = function (){
        var me = SimpleJSLib.EventHandler(),
        _private = {};
        _private.user = null;
        
        me.getUser = function(){
            return _private.user;
        }
                
        me.isLoggedIn=  false;
        me.init = function(){
                me.attach('onLoggedIn', _private.onLoggedIn);
                // check if logged in
                $.getJSON('api/auth/is/logged/in/', function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                if(data[AJAX.MESSAGE] === AJAX.IS_NOT_LOGGED_IN){
                                        Dashbird.LoginBox.show();
                                }
                                else {
                                        _private.user = data[AJAX.DATA].user;
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                        }
                });
                
                 $('#logout').click(function(event){
                    me.logout();
                    event.preventDefault();
                });
        };
        
         me.logout = function(){
         $.getJSON('api/auth/logout/',{}, function(data) {
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    // refresh page
                    document.location.reload();
                }
         });	
        };
        me.login = function(name, password, callbackOnSuccess, callbackOnFailure){
                if(!this.isLoggedIn){
                        $.post('api/auth/login', {
                                name : name, 
                                password : password
                        }, function(data) {
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                        _private.user = data[AJAX.DATA].user;
                                        callbackOnSuccess();
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                                else {
                                      callbackOnFailure();
                                }
                        }, 'json');
                }
        };
        
        _private.onLoggedIn = function(){
            setInterval(function(){
                $.getJSON('api/auth/is/logged/in/', function(data) {
                       if(data[AJAX.MESSAGE] === AJAX.IS_NOT_LOGGED_IN){
                               document.location.href = "";
                        }
                });
            }, 600000)
                
                
        }
        
        me.getUserShares = function(){
            return _private.user.userShares;
        }
        
        me.addUserShare = function(name, callback){
            $.getJSON('api/user/shares/add/', {
                name : name
            }, function(data){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.user.userShares.push(data[AJAX.DATA].user);
                    callback();
                }
            });
        };
        
        me.changePassword = function(oldPassword, newPassword, callback){
            $.post('api/user/password/change/', {
                'old-password' : oldPassword,
                'new-password' : newPassword
            }, function(data){
                callback(data);
            }, 'json');
        };
        
        me.getNameForUser = function(userId){
            var name = 'A person you do not know';
            if(_private.user.userId == userId){
                name = 'You';
            }
            else {
                $.each(_private.user.userShares, function(){
                    if(this.userId == userId){
                        name = this.name;
                        return false;
                    }
                    return true;
                });
            }
            return name;
        };
        
        // for inheritance
        me._private = _private;
        return me;
}();
if(typeof Dashbird == "undefined"){var Dashbird = {};}
Dashbird.Templates = function(){
    var me = {}, _private = {};
    
    _private.$ = null;
    _private.templates = {};
    
    me.init = function(){
        _private.$ = $('#templates');
        _private.templates.$post = _private.$.find('#template-post');
        _private.templates.$post.remove();
        _private.templates.$post = _private.templates.$post.find('.post');
        
        
        _private.templates.$foreignPost = _private.templates.$post.clone();
        // remove unneccessary stuff
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-edit').remove();
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-share').remove();
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-remove').remove();
        _private.templates.$foreignPost.find('.content .command.command-edit').remove();
        _private.templates.$foreignPost.find('.content .command.command-share').remove();
        
        _private.templates.$postComment = _private.$.find('#template-post-comment');
        _private.templates.$postComment.remove();
        _private.templates.$postComment = _private.templates.$postComment.find('.comment');
    };
    
    me.getTemplate = function(name){
        switch (name){ 
            case "post":
                return _private.templates.$post.clone();
                break;
            case "foreign-post":
                return _private.templates.$foreignPost.clone();
                break;
            case "post-comment":
                return _private.templates.$postComment.clone();
                break;
        }
        return null;
    }
    
    return me;
}();
jQuery.fn.extend({
insertAtCaret: function(myValue){
  return this.each(function(i) {
    if (document.selection) {
      //For browsers like Internet Explorer
      this.focus();
      sel = document.selection.createRange();
      sel.text = myValue;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == '0') {
      //For browsers like Firefox and Webkit based
      var startPos = this.selectionStart;
      var endPos = this.selectionEnd;
      var scrollTop = this.scrollTop;
      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
      this.focus();
      this.selectionStart = startPos + myValue.length;
      this.selectionEnd = startPos + myValue.length;
      this.scrollTop = scrollTop;
    } else {
      this.value += myValue;
      this.focus();
    }
  })
}
});
$(document).ready(function (){
    Dashbird.LoginBox.init();
    Dashbird.Templates.init();
    Dashbird.User.attach('onLoggedIn',  function (){
        $('#content').show();
        $('#navbar .nav').show();
        $('#navbar .navbar-search').show();
        Dashbird.Modal.init();
        Dashbird.Settings.init();
        Dashbird.Board.init();
        Dashbird.Search.init();
        Dashbird.Board.loadPosts();
        Dashbird.NewPost.init();
        Dashbird.PluginManager.init();
       
    });
    Dashbird.User.init();
});
