var DashboardEntry = function(){
        var me = {},
        _private = {};
        
        me.entryData = null;
        me.moduleEntry = null;
        me.$entry = null;
        me.$meta = null;
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
                html =	html + '<div class="dashboard-entry-left-column">' + htmlConfig.leftColumn +'</div>';
                html =	html + '<div class="dashboard-entry-middle-column">' + htmlConfig.middleColumn +'</div>';
                html =	html + '<div class="dashboard-entry-right-column"><img class="delete-button" src="' + Dashboard.baseUrl + '/images/button-delete-disabled-small.png" alt="" /></div>';
                html =  html + '<div class="dashboard-entry-footer-column clear-fix" >';
                html =  html + '<div class="dashboard-entry-meta" ><div class="tags"></div></div>';
                html =	html + '</div>';
                html =	html + '</div>';
                me.$entry = $(html);
                me.$middleColumn = me.$entry.find('.dashboard-entry-middle-column');
                me.$meta = me.$entry.find('.dashboard-entry-meta');
                me.drawNormalTags();
                        
                me.$entry.mouseover(function (){
                        Dashboard.$selectedEntry = me.$entry;
                        $('#content > .dashboard-entry.selected').removeClass('selected');
                        Dashboard.$selectedEntry.addClass('selected');
                        Dashboard.$selectedEntry.focus();
                });
                me.$entry.mouseleave(function (){
                        if(Dashboard.$selectedEntry.data('dashboardEntry').entryData.dashboardEntryId == me.entryData.dashboardEntryId){
                                $('#content > .dashboard-entry.selected').removeClass('selected');
                                Dashboard.$selectedEntry = null;
                        }
                });
                        
                var $deleteButton = me.$entry.find('.delete-button');
                       
                $deleteButton.click(function (){
                        me.deleteEntry();
                });
                $deleteButton.mouseenter(function (){
                        $(this).attr('src', Dashboard.baseUrl + '/images/button-delete-small.png');
                });
                $deleteButton.mouseleave(function (){
                        $(this).attr('src', Dashboard.baseUrl + '/images/button-delete-disabled-small.png');
                });
                
                me.$entry.data('dashboardEntry', me);
                return me.$entry;
        },
        me.switchToEditMode = function(){
                
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
                
                if(me.editMode == false){
                        me.switchToEditMode();
                }
                else {
                        me.switchToNormalMode()
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
                        tag = Tag();
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
                                var tag = Tag();
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
                
        me._private = _private; // for inheritance
        return me;
};


var Tag = function(){
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