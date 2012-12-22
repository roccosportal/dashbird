if(typeof Dashbird == "undefined"){
    var Dashbird = {};
}
Dashbird.DashboardEntry = function(){
    var me = {},
    _private = {};
    
    _private.editEntryBoxTags = null;
    _private.entrySharesBoxEntryShares = null;
     
    me.entryData = null;
    me.$entry = null;
    me.$meta = null;
    me.$comments = null;

    me.commands = {
        $bar : null
    }

    me.init = function (entryData){
        me.entryData = entryData;
    }

    me.create = function(){
        if(Dashbird.User.getUser().userId === me.entryData.user.userId){
            me.$entry = Dashbird.Templates.getTemplate('entry');
        }
        else {
            me.$entry = Dashbird.Templates.getTemplate('foreign-entry');
        }
        
        // create jquery shortcuts
        me.$meta =  me.$entry.find('.content .meta');
        me.commands.$bar = me.$entry.find('.content .command-bar.popup');
        me.$comments = me.$entry.find('.comments');
        
        me.drawText();
        me.$meta.find('.info .username').html(me.entryData.user.name);
        me.$meta.find('.info .date').html(Dashbird.Dashboard.convertDate(me.entryData.datetime));
        
        if(Dashbird.User.getUser().userId === me.entryData.user.userId){
            me.commands.edit = Dashbird.Commands.Edit(me);
            me.commands.edit.init();
            
            me.commands.share = Dashbird.Commands.Share(me);
            me.commands.share.init();
            
            me.commands.remove = Dashbird.Commands.Remove(me);
            me.commands.remove.init();
        }
        me.drawTags();
        me.drawEntryShares();
        _private.drawComments();
        
        me.commands.comment = Dashbird.Commands.Comment(me);
        me.commands.comment.init();
      
        // show options
        me.$entry.mouseover(function(){
            me.commands.$bar.show();
        });
        me.$entry.mouseleave(function (){
            me.commands.$bar.hide();
        });     
        
        me.$entry.data('dashboardEntry', me);
        return me.$entry;
    },
    
    me.drawText = function(){
        me.$entry.find('.content .text').html(me.bbcode(me.entryData.text.replace(/\n/g,'<br />')));
    };
    
    me.update = function(text, tags){
        $.getJSON('ajax/entry/edit/', {
            entryId : me.entryData.entryId, 
            text : text,
            tags: tags
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                Dashbird.Dashboard.fire('entry#save', data[AJAX.DATA]);
                // save status
                me.entryData.tags =  data[AJAX.DATA].tags;
                me.entryData.text =  data[AJAX.DATA].text;
                me.drawText();
                me.drawTags();
            }
        });
    };
    
   
                
    me.deleteEntry = function(){
        $.getJSON('ajax/entry/delete/', {
            entryId : me.entryData.entryId
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
            // do nothing
            }
        });
        me.$entry.fadeOut("slow", function(){       
            me.$entry.detach();
        });                     
    };
    
    me.drawTags = function(){
        var $tag = null;
        me.$meta.find('.tags ul').html('');
        $.each(me.entryData.tags, function(key, element){
            $tag = $('#templates #template-tag .tag').clone();
            $tag.find('span').html(element);
            me.$meta.find('.tags ul').append($tag);
        });
      
    };
    
    me.drawEntryShares = function(){
        if(me.entryData.entryShares.length == 0){
            me.$meta.find('.sharing').hide();
            me.$meta.find('.private-sharing').css('display', '');
        }
        else {
            me.$meta.find('.private-sharing').hide();
           
            me.$meta.find('.sharing a span.count').html(me.entryData.entryShares.length);
            // if multiple persons add a trailing s;
           
            if(me.entryData.entryShares.length > 1 ){
                me.$meta.find('.sharing a span.persons').html('persons');
            }
            else{
                me.$meta.find('.sharing a span.persons').html('person');
            }
            
            var names = '';
            var first = true;
            $.each(me.entryData.entryShares, function(key, element){
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

        
    me.setEntryShares = function(userIds){
        $.getJSON('ajax/entry/shares/set/', {
            entryId : me.entryData.entryId, 
            userIds : userIds
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.entryData.entryShares = userIds;
                me.drawEntryShares();
            }
        });
    }
    

    
    me.addComment = function(text, callback){
        $.getJSON('ajax/entry/comment/add/', {
            entryId : me.entryData.entryId, 
            text : text
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                Dashbird.Dashboard.fire('entry#addComment', {
                    entryId : me.entryData.entryId, 
                    data : data[AJAX.DATA]
                })
                me.entryData.comments.push(data[AJAX.DATA]);
                _private.drawComments();
               
            }
            if(callback != null){
                callback();
            }
        });
    }

    me.deleteComment = function(id){
        $.getJSON('ajax/entry/comment/delete/', {
            commentId : id
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                Dashbird.Dashboard.fire('entry#deleteComment', {
                    entryId : me.entryData.entryId
                })
                // rebuild comments
                var comments = [];
                $.each(me.entryData.comments,function(index, comment){
                    if(comment.commentId !== id.toString()){
                        comments.push(comment);
                    }
                });
                me.entryData.comments = comments;
                _private.drawComments();
            }
        });
    }
    
    _private.drawComments = function(){
        me.$comments.empty();
        var $template = Dashbird.Templates.getTemplate('entry-comment');
        $.each(me.entryData.comments,function(index, comment){
            var $comment = $template.clone();
            $comment.find('.text').html(comment.text.replace(/\n/g,'<br />'));
            $comment.find('.meta .info .username').html(comment.user.name);
            $comment.find('.meta .info .date').html(Dashbird.Dashboard.convertDate(comment.datetime));
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