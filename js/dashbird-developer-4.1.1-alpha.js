if(typeof(SimpleJSLib)==='undefined'){
    var SimpleJSLib = {};
}
SimpleJSLib.BaseObject = function(){
    var createInheritObject = function(inheritFunctions){
        return {
            construct : function(){
                var me = {}, _protected = {};
                for(var i = 0; i < inheritFunctions.length; i++){
                    me = inheritFunctions[i].call(me, me, _protected);
                    if(typeof(me)==='undefined'){
                        throw 'Inherit function did not return "me"';
                    }
                }
                if(typeof(_protected.construct)!=='undefined'){
                    _protected.construct.call(me, arguments);
                }
                return me;
            },
            inherit : function(inheritFunction){
                var _inheritFunctions = inheritFunctions.slice(0); // clone
                _inheritFunctions.push(inheritFunction);
                return createInheritObject.call(window, _inheritFunctions);
            }
        };
    };
    return {
        inherit : function(inheritFunction){
            return createInheritObject.call(window, [inheritFunction]);
        }
    }
}();
SimpleJSLib.EventHandler = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.listeners = [];
    me.fireEvent = function (name, data){
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].name==name){
                _protected.listeners[i].callback(data);
            }
        }
    };
    me.attachEvent = function (name, callback){
        _protected.listeners.push({
            name : name, 
            callback : callback
        });
    };
    me.detachEvent = function (name, callback){
        var indexes = [];
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].name==name && _protected.listeners[i].callback==callback){
                indexes.push(i);
            }
        }
        for (var j = 0; j < indexes.length; j++) {
            _protected.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
        }
    };
    return me;
});
SimpleJSLib.SingleRequestQueue = SimpleJSLib.BaseObject.inherit(function(me, _protected){
        _protected.latestRequestId = 0;
        _protected. timeout = null;
        me.setTimeout = function (timeout){
                _protected. timeout = timeout;
        };
        
        me.getLatestRequestId = function(){
                return _protected.latestRequestId;
        }
        
        me.addToQueue = function(data, callback){
                if(_protected. timeout===null){
                        throw "Timeout was not set. Use the setTimeout function of this object."
                }
                _protected.latestRequestId++;
                var currentRequestId = _protected.latestRequestId;
                setTimeout(function (){
                        if(_protected.latestRequestId===currentRequestId){
                                callback(data);
                        }
                }, _protected. timeout);
        };
        
        me.runAsynchronRequest = function(){
                _protected.latestRequestId++;
                var currentRequestId = _protected.latestRequestId;
                var requestObject = {
                        currentRequestId : currentRequestId,
                        isLatestRequest : function(){
                                return _protected.latestRequestId===currentRequestId;
                        }
                };
                return requestObject;
        };
        return me;	
});
SimpleJSLib.Observable = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.data = {};
    _protected.listeners = []
    
    _protected.construct = function(parameters){
        _protected.data = parameters[0];
    }
    
    me.get = function(){
        return  _protected.data;
    };
    
    me.set = function(data){
        _protected.data = data;
        me.trigger();
    };
    
    me.trigger = function(){
        for (var i = 0; i < _protected.listeners.length; i++) {
            _protected.listeners[i].callback(_protected.data);
        }
    }
    
    me.listen = function(callback){
        _protected.listeners.push({
            callback : callback
        });
    };
    
    me.unlisten = function (callback){
        var indexes = [];
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].callback==callback){
                indexes.push(i);
            }
        }
        for (var j = 0; j < indexes.length; j++) {
            _protected.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
        }
    };
    
    return me;
});
Dashbird.AJAX = {
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
Dashbird.AjaxResponse = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.responseData = {};
    
    me.isSuccess = false;
    me.data = {};
    _protected.construct = function(parameters){
        _protected.responseData = parameters[0];
        me.isSuccess = (_protected.responseData[Dashbird.AJAX.STATUS] === Dashbird.AJAX.STATUS_SUCCESS)
        if(me.isSuccess){
            me.data = _protected.responseData[Dashbird.AJAX.DATA];
        }
        return me;
    };
    return me;
});
if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.User = SimpleJSLib.BaseObject.inherit(function (me, _protected){
        _protected.user = null;
        
        me.getUser = function(){
            return _protected.user;
        }
        
        me.isCurrentUser = function(userId){
            return (_protected.user.userId.toString() === userId.toString());
        }
        
        me.init = function(){
            _protected.user = Dashbird.InitialData.User;
        }
                    
        me.getUserShares = function(){
            return _protected.user.userShares;
        }
        
        me.getNameForUser = function(userId){
            var name = 'A person you do not know';
            if(_protected.user.userId == userId){
                name = 'You';
            }
            else {
                $.each(_protected.user.userShares, function(){
                    if(this.userId == userId){
                        name = this.name;
                        return false;
                    }
                    return true;
                });
            }
            return name;
        };
       return me;
}).construct();
Dashbird.Utils = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
    
    me.convertDate = function(date){
        return date.substring(0, date.length - 3);
    }
    
    me.convertLineBreaks = function(string){
        return string.replace(/\n/g,'<br />');
    }
    
    // http://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling
    me.isOnScreen = function(elem)
    {
        var docViewTop = $(window).scrollTop();
        docViewTop += 40; // there is a border
        //var docViewBottom = docViewTop + $(window).height();
        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();
        return (elemBottom >= docViewTop);
    }
    return me;
}).construct();
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
Dashbird.Post = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.postData = null;
    me.isFromCurrentUser = false;
    _protected.construct = function (parameters){
        var postData = parameters[0];
        _protected.postData = {
            postId :  postData.postId,
            created : postData.created,
            user : postData.user,
            updated : SimpleJSLib.Observable.construct(postData.updated),
            tags : SimpleJSLib.Observable.construct(postData.tags),
            comments : SimpleJSLib.Observable.construct(postData.comments),
            postShares : SimpleJSLib.Observable.construct(postData.postShares),
            text : SimpleJSLib.Observable.construct(postData.text)
        }
        me.isFromCurrentUser = Dashbird.User.isCurrentUser(_protected.postData.user.userId);
    };
    
    me.getPostData = function(){
        return _protected.postData;
    };
  
    
    me.update = function(text, tags){
        $.getJSON('api/post/edit/', {
            postId : _protected.postData.postId, 
            text : text,
            tags: tags
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                //Dashbird.Board.fire('post#save', data[AJAX.DATA]);
                // save status
                _protected.postData.tags.set(ajaxResponse.data.tags);
                _protected.postData.text.set(ajaxResponse.data.text);
                _protected.postData.updated.set(ajaxResponse.data.updated);
            }
        });
    };
              
    me.deletePost = function(){
        $.getJSON('api/post/delete/', {
            postId : _protected.postData.postId
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
            // do nothing
            }
        });
        me.undrawPost();                  
    };
    
    me.setPostShares = function(userIds){
        $.getJSON('api/post/shares/set/', {
            postId : _protected.postData.postId, 
            userIds : userIds
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                _protected.postData.postShares = userIds;
                me.drawPostShares();
            }
        });
    }
    
    
    me.addComment = function(text, callback){
        $.getJSON('api/post/comment/add/', {
            postId : _protected.postData.postId, 
            text : text
        }, function(data) {
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                _protected.postData.updated.set(ajaxResponse.data.post.updated);
                _protected.postData.comments.get().push(ajaxResponse.data.comment);
                _protected.postData.comments.trigger();
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
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
               _protected.postData.updated.set(ajaxResponse.data.post.updated);
                
//                Dashbird.Board.fire('post#deleteComment', {
//                    post : me,
//                    data : data[AJAX.DATA]
//                })
                // rebuild comments
                var comments = [];
                $.each(_protected.postData.comments.get(),function(index, comment){
                    if(comment.commentId.toString() !== id.toString()){
                        comments.push(comment);
                    }
                });
                _protected.postData.comments.set(comments);
            }
        });
    }
                      
    return me;
});
Dashbird.PostHtmlLayer =  SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.$post = null;
    _protected.$meta = null;
    _protected.$comments = null;
    
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
        
        if(_protected.post.isFromCurrentUser){
            //_protected.commands.edit = Dashbird.Commands.Edit.construct(me);
            
            
//            _protected.commands.share = Dashbird.Commands.Share(me);
//            _protected.commands.share.init();
            
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
        
        
        // attach listener
        _protected.post.getPostData().text.listen(function(){
            _protected.drawText();
        });
        _protected.post.getPostData().comments.listen(function(){
            _protected.drawComments();
        });
        _protected.post.getPostData().tags.listen(function(){
            _protected.drawTags();
        });
        _protected.post.getPostData().postShares.listen(function(){
            _protected.drawPostShares();
        });
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
    
    
  
    me.undrawPost = function(callback){
        _protected.$post.fadeOut("slow", function(){       
            _protected.$post.detach();
            callback.call();
        });   
    };
    
    
    
    
    _protected.drawText = function(){
        _protected.$post.find('.content .text').html(_protected.bbcode(Dashbird.Utils.convertLineBreaks(_protected.post.getPostData().text.get())));
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
            $.each(_protected.post.getPostData().postShares, function(key, element){
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
                    Dashbird.Modal.show({
                        headline: 'Deleting comment', 
                        text : 'Do you really want to delete this comment?',
                        'cancel-button-text' : 'No, no, I am sorry', 
                        'submit-button-text' : 'Remove the rubish!', 
                        callback : function(){
                            me.getPost().deleteComment(comment.commentId);
                        }
                    })
                });
            }
            _protected.$comments.append($comment);
        });
        
    }
    
    _protected.bbcode = function(text){
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
});
if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Base = SimpleJSLib.BaseObject.inherit(function(me, _protected){
   _protected.postHtmlLayer = null;
   _protected.$ = null;
   
   _protected.construct = function(parameters){
       _protected.postHtmlLayer = parameters[0];
   }
   
    _protected.set$ = function(selector){
         _protected.$ = _protected.postHtmlLayer.getLayer().find('.content .command.' + selector);
    }
    
    _protected.hideCommands = function(callback){
         _protected.postHtmlLayer.getLayer().find('.content .command').fadeOut().promise().done(callback);
    }
    
    return me;
});
Dashbird.Commands.Comment = Dashbird.Commands.Base.inherit(function(me, _protected){
    var _parent = {
        construct :  _protected.construct
    };
    
    _protected.isOnDemandInited = false;
     
    _protected.construct = function(parameters){
        _parent.construct(parameters);
        _protected.postHtmlLayer.getCommandBar().find('.command-comment').click(me.show);
        _protected.set$('command-comment');
    };
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$.find('.cancel-button').click(function(){
                _protected.$.fadeOut();
            });
        
            _protected.$.find('.submit-button').click(function(e){
                e.preventDefault();
                _protected.postHtmlLayer.getPost().addComment(_protected.$.find('textarea').val(), function(){
                    _protected.$.fadeOut(); 
                })
            });
            _protected.isOnDemandInited = true;
        }
    };
    
    me.show = function(e){
        e.preventDefault();
        _protected.onDemandInit();
        // fade out all opend options
        _protected.hideCommands(function(){
            // show option
            _protected.$.fadeIn(function(){
                _protected.$.find('textarea').focus();
            });
        });
    };
    return me;
});
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
Dashbird.Commands.Remove = Dashbird.Commands.Base.inherit(function(me, _protected){
    var _parent = {
        construct :  _protected.construct
    };
     
    _protected.construct = function(parameters){
        _parent.construct(parameters);
        _protected.postHtmlLayer.getCommandBar().find('.command-remove').click(me.show);
    };
    
    me.show = function(e){
        e.preventDefault();
        Dashbird.Modal.show({
            headline: 'Deleting post', 
            text : 'Do you really want to remove this post?',
            'cancel-button-text' : 'No, no, I am sorry', 
            'submit-button-text' : 'Remove the rubish!', 
            callback : function(){
                _protected.postHtmlLayer.getPost().deletePost();
            }
        });
    }
    
    return me;
});
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
Dashbird.Stack = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected.$posts = null;
    _protected.posts = [];
    _protected.postHtmlLayers = [];
    _protected.pager = {};
    _protected.pager.$morePosts = null;
    _protected.pager.postCount = 10;
    
    _protected.$loading = null;
    
    _protected.getCreateDateOfLastPost = function(){
        return _protected.posts[_protected.posts.length - 1].getPostData().created;
    }
    
    me.addPosts = function(posts){
        for (var i = 0; i <  posts.length; i++) {
            var postHtmlLayer = Dashbird.PostHtmlLayer.construct(posts[i]);
            _protected.posts.push(posts[i]);
            _protected.postHtmlLayers.push(postHtmlLayer);
            _protected.$posts.append(postHtmlLayer.getLayer());
        }
    }

    me.init = function (){    
        _protected.$posts = $('#stack .posts');
        _protected.$loading = $('#stack .loading');
        _protected.$loading.show();      
        _protected.pager.$morePosts = $('#stack .more-posts')
        _protected.pager.$morePosts.click(function(e){
            e.preventDefault();
            me.showMorePosts();
        });
        
        $(window).scroll(function() {
            _protected.hidePostsWhichAreNotOnScreen();
            _protected.showHiddenPosts();
        });

        Dashbird.Posts.loadPostsByCreated(Dashbird.InitialData.LoadedAt, _protected.pager.postCount, function(result){
            var posts = result.newPosts;
            me.addPosts(posts);
            _protected.$loading.hide();
            _protected.pager.$morePosts.show();
        });
    };
    
    me.showMorePosts = function(){
        _protected.pager.$morePosts.hide();
        _protected.$loading.show();
        var currentCreateDateOfLastPost = _protected.getCreateDateOfLastPost();
        var posts = Dashbird.Posts.getListByCreated(currentCreateDateOfLastPost, _protected.pager.postCount);
        me.addPosts(posts);
        if(posts.length < _protected.pager.postCount){  // we need to load the rest of the data
            var remainingPostCount = _protected.pager.postCount - posts.length;
            Dashbird.Posts.loadPostsByCreated(_protected.getCreateDateOfLastPost(), remainingPostCount, function(result){
                me.addPosts(result.newPosts);
                if(result.newPosts.length == remainingPostCount){
                    _protected.pager.$morePosts.show();
                }
                _protected.$loading.hide();
            });
        }
        else {
            _protected.pager.$morePosts.show();
            _protected.$loading.hide();
        }
    };
    
    _protected.hidePostsWhichAreNotOnScreen = function(){
        var elementsToHide = [];
        var calculatedHeight = $('.posts .hidden-posts').height();
        if(calculatedHeight === null){
            calculatedHeight = 0;
        }    
        $.each(_protected.$posts.find('.post:visible'), function(index, element){
           element = $(element)
           if(!Dashbird.Utils.isOnScreen(element)){
               calculatedHeight += element.height();
               elementsToHide.push(element);
               return true
           }
           return false;
        });
        if(elementsToHide.length > 0){
            for(var i = 0; i < elementsToHide.length; i++){
                elementsToHide[i].hide();
            }
            $('.posts .hidden-posts').height(calculatedHeight)
        }

    };
    
    _protected.showHiddenPosts = function(){
       
        if(_protected.$posts.find('.post:hidden').length==0){
            $('.posts .hidden-posts').height(0);
        }
        else {
            var i = 0;
            while(Dashbird.Utils.isOnScreen($('.posts .hidden-posts'))){
                _protected.$posts.find('.post:hidden').last().show();
                $('.posts .hidden-posts').height( $('.posts .hidden-posts').height() - _protected.$posts.find('.post:hidden').last().height())
                i++;
                if(i > 50){
                    break;
                }
            }
        }
    };
                
    //    me.addToTop = function(postData){
    //        var post = Dashbird.Post();
    //        post.init(postData);
    //        var $post = post.create();      
    //                                             
    //        var first = $('#stack .posts .post:first');
    //        if(first.length != 0){
    //            first.before($post);
    //        }
    //        else {
    //            _protected.$posts.append($post);
    //        }     
    //    };
    //    
    //
    //    me.loadPosts = function(){
    //        me.pager.$morePosts.hide();
    //        _protected.$loading.show();              
    //        var request = _protected.loadPostsAjaxRequestQueue.runAsynchronRequest();
    //        $.getJSON('api/posts/load/', {
    //            search : Dashbird.Search.getSearchPhrase(),
    //            'start-position' : me.pager.startPosition,
    //            'post-count' : me.pager.postCount,
    //            'order-by' : 'CREATED'
    //        }, function(data) {
    //            var ajaxResponse = Dashbird.AjaxResponse.construct().init(data);
    //            if(request.isLatestRequest()){
    //                if(ajaxResponse.isSuccess){
    //                    _protected.$loading.hide();            
    //                    var posts = ajaxResponse.data.posts;
    //                    for (var i = 0; i <  posts.length; i++) {
    //                        var postData = posts[i];
    //                        var post = Dashbird.Post.construct(postData);
    //                        me.posts.push(post); // make me protected later
    //                       
    //                                           
    //                    }
    //                    if(posts.length>0){
    //                        me.pager.$morePosts.show();
    //                    }
    //                }
    //            }
    //        });
    //       
    //    };

    return me;
}).construct();
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
Dashbird.NewPost = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected = {};
    _protected.tags = [];
    _protected.bbcode= {};
    
    me.init = function(){
        // new post
        $('#navbar .nav .show-new-post').on('show', _protected.onShow);  
    };
    
    _protected.isOnDemandInited = false;
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$ = $('#new-post');
            
            _protected.$.find('.submit-button').click(_protected.onSaveClick);
            _protected.$.find('.cancel-button').click(_protected.onCancelClick);
       
            _protected.$.find('.add-tag-input button').click(_protected.addTag);
            _protected.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _protected.addTag(e);
                }
            });
        
            _protected.$.find('.tag-alert').alert();
            
            _protected.bbcode.link = Dashbird.BBCode.Link()
            _protected.bbcode.link.init(_protected.$.find('.command-bar .command-link'), _protected.$.find('textarea'));
            
            _protected.bbcode.video = Dashbird.BBCode.Video()
            _protected.bbcode.video.init(_protected.$.find('.command-bar .command-video'), _protected.$.find('textarea'));
            
            _protected.bbcode.image = Dashbird.BBCode.Image()
            _protected.bbcode.image.init(_protected.$.find('.command-bar .command-image'), _protected.$.find('textarea'));
            
            _protected.bbcode.bold = Dashbird.BBCode.Bold()
            _protected.bbcode.bold.init(_protected.$.find('.command-bar .command-bold'), _protected.$.find('textarea'));
             
            _protected.isOnDemandInited = true;
        }
    };
    
   
    
    _protected.onShow = function(){
        _protected.onDemandInit();
        _protected.tags = [];
        _protected.postShares = [];
        _protected.$.find('textarea').val('');
        _protected.hideTagAlert();
        _protected.drawShares();
        _protected.$.find('.add-tag-input input').val('');
        _protected.$.find('.tags').empty();
        _protected.$.find('textarea').focus();
    };
    
    _protected.onSaveClick = function(e){
        e.preventDefault();
        _protected.addTag();
        $.getJSON('api/post/add/', {
            text :  _protected.$.find('textarea').val(),
            tags :  _protected.tags,
            shares : _protected.postShares
        }, function(data) {
            var ajaxResponse = Dashird.AjaxResponse.construct().init(data);
            if(ajaxResponse.isSuccess){
                me.fireEvent('newPost', ajaxResponse.data);
                Dashbird.Board.refreshPosts();
                $('#navbar .nav .show-board').tab('show');
                  
            }
        });
    }
    
    _protected.onCancelClick = function(e){
        e.preventDefault();
        $('#navbar .nav .show-board').tab('show');
    }
    
    _protected.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag =_protected.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _protected.tags);
            if(position===-1){ // only add if not already in tags
                _protected.drawTag(tag);
                _protected.tags.push(tag);
                _protected.$.find('.add-tag-input input').val('');
                _protected.hideTagAlert();
            }
            else {
                _protected.showTagAlert();
            }
            
        }
    };
    
    _protected.showTagAlert = function(){
        _protected.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        _protected.$.find('.tag-alert').append($alert);
    };
    _protected.hideTagAlert = function(){
        _protected.$.find('.tag-alert').html('');
    };
    
    
    _protected.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _protected.tags);
            if(position!== -1){ 
                _protected.tags.splice(position, 1);
            }
               
        });
        _protected.$.find('.tags').append($tag);
    };
    
    _protected.drawShares = function(){
        var $share = null;
        _protected.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_protected.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _protected.shareChange($(this), element.userId);
            });
            _protected.$.find('.shares').append($share);
        });
    }
    
    _protected.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _protected.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _protected.postShares);
            if(position!== -1){ 
                _protected.postShares.splice(position, 1);
            }
        }
    }
    
    
    
    return me;
}).construct();;

if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Search = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.searchRequestQueue = null;
    _protected.$searchBox = null;
        
        
    me.init = function(){
        _protected.$searchBox = $('#search-box');
        _protected.searchRequestQueue = SimpleJSLib.SingleRequestQueue.construct();
        _protected.searchRequestQueue.setTimeout(300);
        _protected.$searchBox.keypress(function(e){
            if(e.keyCode == 13){
                e.preventDefault();
            }
            else {
                _protected.searchRequestQueue.addToQueue({}, function(data){
                     Dashbird.Board.refreshPosts();
                     $('#navbar .nav .show-board').tab('show');
                });
            }
        });
    };
        
    me.getSearchPhrase = function(){
        // when Dashbird.Search is not initialized yet
        if(!_protected.$searchBox){
            return '';
        }
        return _protected.$searchBox.val();
    };
    
    return me;
}).construct();

Dashbird.Templates = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.$ = null;
    _protected.templates = {};
    
    me.init = function(){
        _protected.$ = $('#templates');
        _protected.templates.$post = _protected.$.find('#template-post');
        _protected.templates.$post.remove();
        _protected.templates.$post = _protected.templates.$post.find('.post');
        
        
        _protected.templates.$foreignPost = _protected.templates.$post.clone();
        // remove unneccessary stuff
        _protected.templates.$foreignPost.find('.content .command-bar.popup .command-edit').remove();
        _protected.templates.$foreignPost.find('.content .command-bar.popup .command-share').remove();
        _protected.templates.$foreignPost.find('.content .command-bar.popup .command-remove').remove();
        _protected.templates.$foreignPost.find('.content .command.command-edit').remove();
        _protected.templates.$foreignPost.find('.content .command.command-share').remove();
        
        _protected.templates.$postComment = _protected.$.find('#template-post-comment');
        _protected.templates.$postComment.remove();
        _protected.templates.$postComment = _protected.templates.$postComment.find('.comment');
    };
    
    me.getTemplate = function(name){
        switch (name){ 
            case "post":
                return _protected.templates.$post.clone();
                break;
            case "foreign-post":
                return _protected.templates.$foreignPost.clone();
                break;
            case "post-comment":
                return _protected.templates.$postComment.clone();
                break;
        }
        return null;
    }
    
    return me;
}).construct();
$(document).ready(function (){
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
    
  
    Dashbird.Templates.init();
    Dashbird.User.init();
    Dashbird.Stack.init();
    Dashbird.NewPost.init();
    Dashbird.Modal.init();
    Dashbird.Search.init();
    
});
