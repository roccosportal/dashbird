if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.DashboardEntry = function(){
    var me = {},
    _private = {};
        
    me.entryData = null;
    me.moduleEntry = null;
    me.$entry = null;
    me.$meta = null;
    me.$comments = null;
    me.$middleColumn = null;
    me.changedEntryData = null;
    me.editMode = false;
    me.hasUnsavedChanges = false;
        
    me.init = function (entryData, moduleEntry){
        me.entryData = entryData;
        me.moduleEntry = moduleEntry;
    }

    me.create = function(htmlConfig){
                        
        var html = '<div class="dashboard-entry ' + htmlConfig.cssClass +'">';
        html =	html + '<div class="dashboard-entry-top-column">' + me.entryData.user.name + ' on ' +  me.entryData.datetime +'</div>';
        html =	html + '<div class="dashboard-entry-left-column">' + htmlConfig.leftColumn +'</div>';
        html =	html + '<div class="dashboard-entry-middle-column">' + htmlConfig.middleColumn +'</div>';
        html =	html + '<div class="dashboard-entry-right-column">';
        html =	html + '<a class="comment-button" href="#">c</a>';
        if(Dashbird.Auth.getUser().userId === me.entryData.user.userId){
            html =	html + '<a class="edit-button" href="#">e</a>';
            html =	html + '<a class="entry-shares-button" href="#">s</a>';
            html =	html + '<a class="delete-button"  href="#">x</a>';
        }
        html =	html + '</div>';
        html =  html + '<div class="dashboard-entry-footer-column clear-fix" >';
        html =  html + '<div class="dashboard-entry-meta" ><div class="tags"></div></div>';
        html =  html + '<div class="dashboard-entry-comments" ></div>';
        html =	html + '</div>';
        html =	html + '</div>';
        me.$entry = $(html);
        me.$middleColumn = me.$entry.find('.dashboard-entry-middle-column');
        me.$meta = me.$entry.find('.dashboard-entry-meta');
        
        me.drawNormalTags();
        me.$comments = me.$entry.find('.dashboard-entry-comments');
        _private.drawComments();
                        
        me.$entry.mouseover(function (){
            Dashbird.Dashboard.$selectedEntry = me.$entry;
            $('#content > .dashboard-entry.selected').removeClass('selected');
            Dashbird.Dashboard.$selectedEntry.addClass('selected');
            Dashbird.Dashboard.$selectedEntry.focus();
        });
        me.$entry.mouseleave(function (){
            if(Dashbird.Dashboard.$selectedEntry.data('dashboardEntry').entryData.dashboardEntryId == me.entryData.dashboardEntryId){
                $('#content > .dashboard-entry.selected').removeClass('selected');
                Dashbird.Dashboard.$selectedEntry = null;
            }
        });
                        
        var $deleteButton = me.$entry.find('.dashboard-entry-top-column .delete-button'); 
        $deleteButton.click(function (){
            me.deleteEntry();
        });
        
        me.$entry.find('.comment-button').click(function(e){
            e.preventDefault();
            me.showCommentBox();
        });
       
        me.$entry.find('.entry-shares-button').click(function(e){
             e.preventDefault();
            me.showEntryShares()
            });
        me.$entry.find('.edit-button').click(function(e){
            e.preventDefault();
            me.toggleMode()
        });
                 
                
        me.$entry.data('dashboardEntry', me);
        return me.$entry;
    },
    me.switchToEditMode = function(){
        if(Dashbird.Auth.getUser().userId === me.entryData.user.userId){
            me.editMode = true
            me.changedEntryData = {};
            me.changedEntryData = $.extend(true, {}, me.entryData);
            me.drawEditableTags();
            me.moduleEntry.switchToEditMode();
            me.$middleColumn.find('input').keydown(function(){
                me.unsavedChanges();
            });
            me.$middleColumn.find('textarea').keydown(function(){
                me.unsavedChanges();
            });
        }
    },
    me.switchToNormalMode = function(){
                
        me.editMode = false;
                        
        // forget changes
        me.changedEntryData = {};
        me.drawNormalTags();
                        
        me.hideUnsavedChages();
        me.moduleEntry.switchToNormalMode();
    },
    me.toggleMode = function(){
        if(Dashbird.Auth.getUser().userId === me.entryData.user.userId){
            if(me.editMode == false){
                me.switchToEditMode();
            }
            else {
                me.switchToNormalMode()
            }
        }
    },
    me.save = function(){
                
        if(me.editMode){
            me.moduleEntry.save();
        }
    },
                
    me.deleteEntry= function(params){
        var me = this;
        if(params===undefined){
            params = {};
        }
        var _params = {
            beforeDetach : null,
            afterDetach : null        
        };
        $.extend(_params, params);
                        
        if(confirm('Do you really want to delete this entry?')){
            me.moduleEntry.deleteEntry();
            me.$entry.fadeOut("slow", function(){
                if(_params.beforeDetach!=null){
                    _params.beforeDetach();
                }
                me.$entry.detach();
                if(_params.afterDetach!=null){
                    _params.afterDetach();
                }
            });
        }
                       
    };
                
    me.drawNormalTags = function(){
        var tagHtml = '';
        var tags = [];
        if(!jQuery.isEmptyObject(me.changedEntryData)){
            tags = me.changedEntryData.tags;
        }
        else {
            tags = me.entryData.tags;
        }
                        
        $.each(tags,function(index, value){
            tagHtml = tagHtml + '<span class="tag">' + value + '</span>';
        });
        me.$meta.find('.tags').html(tagHtml);
    };
                
    me.drawEditableTags = function(){
        me.$meta.find('.tags').html('<div class="tags-box"><input class="tag-field" type="name" /></div>');
        var $tagField = me.$meta.find('.tag-field');
        var width = 500;
        var tag = null;
        $.each(me.entryData.tags,function(index, value){
            tag = Dashbird.Tag();
            tag.init(me, value);
            $tagField.before(tag.$tag);
            width = width - tag.$tag.width() - 4 - 6; // 4px margin 6px padding 
        });
        $tagField.css('width', width + 'px');
                        
        $tagField.keydown(function(event){
            if(event.keyCode == 32){
                if($tagField.val()!==''){
                    me.addTag($tagField.val());
                }
                event.preventDefault();
            }
            else if(event.ctrlKey == true && event.keyCode == 83) {
                me.addTag($tagField.val());
            ///event.preventDefault();
            }
            else if(event.keyCode == 60){
                event.preventDefault();
            }
            else if(event.ctrlKey == true && event.keyCode == 86){
                setTimeout(function(){
                    $tagField.val($tagField.val().replace(/</g, '').replace(/>/g, ''));
                }, 100)
                           
            }
                
        });
        me.$meta.find('.tags-box').focusout(function(){
            if($tagField.val()!==''){
                me.addTag($tagField.val());
            }
        });
    };
                
    me.addTag = function(tagTitle){
                
        if(jQuery.isEmptyObject(me.changedEntryData)||me.changedEntryData.tags === undefined){
            throw "changedEntryData can't be empty. Please fill it with the entryData's before you call this function.";
        }
                     
                        
        var position = $.inArray(tagTitle, me.changedEntryData.tags);
        if(position===-1){ // only add if not already in tags
            me.changedEntryData.tags.push(tagTitle);
            if(me.editMode){
                var $tagField = me.$meta.find('.tag-field');
                var tag = Dashbird.Tag();
                tag.init(me, tagTitle);
                $tagField.val('');
                $tagField.before(tag.$tag);
                var width = $tagField.width() - tag.$tag.width() - 4 - 6; // 4px margin 6px padding
                $tagField.css('width', width + 'px');
                me.unsavedChanges();
            }
            else {
                me.drawNormalTags(); // redraw
            }
        }
                
    };
                
    me.deleteTag = function(tagTitle){
                
        if(jQuery.isEmptyObject(me.changedEntryData)||me.changedEntryData.tags === undefined){
            throw "changedEntryData can't be empty. Please fill it with the entryData's before you call this function.";
        }
        var position = $.inArray(tagTitle, me.changedEntryData.tags);
        if(position!== -1){ 
                           
            me.changedEntryData.tags.splice(position, 1);
            if(me.editMode){
                var $tagField = me.$meta.find('.tag-field');
                var $tag = me.$meta.find(".tag:contains('" + tagTitle +"'):first");
                var width = $tagField.width() + $tag.width() + 4 + 6; // 4px margin 6px padding 
                $tagField.css('width', width + 'px');
                $tag.detach();
                me.unsavedChanges();
            }
            else {
                me.drawNormalTags(); // redraw
            }
        }
    };
                
    me.unsavedChanges = function(){
                
        if(!me.hasUnsavedChanges){
            me.hasUnsavedChanges = true;
            me.$meta.append('<div class="unsaved-changes">Unsaved changes!</div>');
        }
    };
                
    me.hideUnsavedChages = function(){
                
        if(me.hasUnsavedChanges){
            me.$meta.find('.unsaved-changes').detach();
            me.hasUnsavedChanges = false;
        }
    };
        
    me.showEntryShares = function(){
        Dashbird.EntrySharesBox.show(me);
    };
        
    me.setEntryShares = function(userIds){
        $.getJSON('ajax/set/entry/shares/', {
            entryId : me.entryData.dashboardEntryId, 
            userIds : userIds
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.entryData.entryShares = userIds;
            }
        });
    }
    
     me.showCommentBox = function(){
        Dashbird.EntryCommentsBox.show(me);
    };
    
    me.addComment = function(text){
        $.getJSON('ajax/add/comment/', {
            entryId : me.entryData.dashboardEntryId, 
            text : text
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.entryData.comments.push(data[AJAX.DATA]);
                _private.drawComments();
            }
        });
    }
    
    me.deleteComment = function(id){
        $.getJSON('ajax/delete/comment/', {
            commentId : id
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
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
        var html = '';
        $.each(me.entryData.comments,function(index, comment){
            html = html + '<div class="comment" data-id="' + comment.commentId + '">';
            html = html + '<div class="comment-header">' + comment.user.name + ' on ' +  comment.datetime;
            if(Dashbird.Auth.getUser().userId === comment.user.userId){
                html = html + '<a href="#" class="delete-button">x</a>';
            }
            html = html + '</div>';
            html = html + '<div class="comment-content">' + comment.text + '</div>';
            html = html + '</div>';
        });
        me.$comments.html(html);
        me.$comments.find('.delete-button').click(function(){
            var id = $(this).closest('.comment').data('id');
            me.deleteComment(id);
        });
    }
                
    me._private = _private; // for inheritance
    return me;
};


Dashbird.Tag = function(){
    var me = {},
    _private = {};
        
    _private.dashboardEntry = null;
  
    me.$tag = null;
    me.title = null;
    me.init = function(dashboardEntry, title){
                
        _private.dashboardEntry = dashboardEntry;
        me.$tag =  $('<span class="tag">' + title + '<span class="delete-tag"></span></span>');
        me.$tag.find('.delete-tag').click( function(){
            _private.dashboardEntry.deleteTag(title);
        });
    };
              
    me._private = _private; // for inheritance
    return me;
};

Dashbird.EntryCommentsBox = function(){
  var me = {},
      _private = {};
      _private.isInitiated = false;
      _private.$entryCommentBox = null;
      _private.currentDashboardEntry = null;
  
       me.init = function(){
        if(!_private.isInitiated){
            _private.$entryCommentBox = $('#entry-comment-box');
            _private.$entryCommentBoxText = _private.$entryCommentBox.find('#entry-comment-box-text');
            _private.$entryCommentBox.find('.save-button').click(_private.onSave);
            _private.$entryCommentBox.find('.cancel-button').click(function(e){
                e.preventDefault();
                _private.$entryCommentBox.hide();
                _private.currentDashboardEntry = null;
                
            })
            _private.isInitiated = true;
        }
    }
    
     _private.onSave = function(e){
        e.preventDefault();
        if(_private.currentDashboardEntry !== null){
           _private.currentDashboardEntry.addComment(_private.$entryCommentBoxText.val());
           _private.currentDashboardEntry = null;
        }
        _private.$entryCommentBox.hide();
    }
      
      me.show = function(dashboardEntry){
          me.init();
          _private.currentDashboardEntry = dashboardEntry;
          _private.$entryCommentBoxText.val('');
          _private.$entryCommentBox.css('top', dashboardEntry.$entry.position().top)
          _private.$entryCommentBox.css('left', dashboardEntry.$entry.position().left)
          _private.$entryCommentBox.show();
      }
      
      return me;
}();