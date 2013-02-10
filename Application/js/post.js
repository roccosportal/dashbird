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
            /\[url\](http:\/\/[^ \\"\n\r\t<]*?)\[\/url\]/g,
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