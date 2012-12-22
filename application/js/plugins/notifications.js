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
    _private.changedEntryIds = [];
        
    me.init = function(){
        _private.$count = $('#navbar .show-notifications span');
        $('#navbar .nav .show-notifications').on('show', _private.onShow);
        _private.$ = $('#notifications');
        me.loadData(function(){
            me.onFirstTime(function(){
                me.refresh();
            });
        });
 
        setInterval(me.refresh, 30000);
              
              
              
        Dashbird.Dashboard.attach('showSingleEntry', _private.visitedEntry);
        Dashbird.NewEntry.attach('newEntry', _private.visitedEntry);
        Dashbird.Dashboard.attach('entry#save', _private.visitedEntry);
        Dashbird.Dashboard.attach('entry#addComment', _private.visitedEntryByChangingComment);
        Dashbird.Dashboard.attach('entry#deleteComment',_private.visitedEntryByChangingComment);
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
            Dashbird.Dashboard.getHashes(function(data){
                _private.data = {
                    options : {},
                    hashes : data.hashes
                }
                me.saveData();
                callback();
            });
        }
        else {
            callback();
        }
    }
        
            
    me.refresh = function(){
        _private.checkHashes(function(){
            _private.updateCountDisplay();
            _private.showNotifications();
        });
        
    }
    
    _private.checkHashes = function(callback){
        _private.changedEntryIds = [];
        Dashbird.Dashboard.getHashes(function(data){
            var oldHash = null;
            $.each(data.hashes, function(){
                oldHash = _private.getHashFromData(this.entryId)
                if(this.hash != oldHash){
                    _private.changedEntryIds.push(this.entryId);
                  
                }
            });
            if(callback != null){
                callback();
            }
        });
    };
    
    _private.updateCountDisplay = function(){
        _private.$count.html(_private.changedEntryIds.length);
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
        
        if(_private.changedEntryIds.length > 0){
            _private.showLoading();
    
            Dashbird.Dashboard.getEntries(_private.changedEntryIds, function(data){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    var entries = data[AJAX.DATA];
                    $.each(entries, function(key, element){
                        var $notification = $('#templates #template-notification .notification').clone();
                        var text = element.text;
                        if(text.length > 20){
                            text = text.substring(0,20);
                        }
                        text += '...';
                        $notification.find('.text').html(text);
                        $notification.find('.meta .info .username').html(element.user.name);
                        $notification.find('.meta .info .date').html(Dashbird.Dashboard.convertDate(element.datetime));
                    
                        $notification.find('.meta .comments span').html(element.comments.length);
                    
                        $notification.mouseover(function(){
                            $notification.find('.command-bar.popup').show();
                        });
                        $notification.mouseleave(function (){
                            $notification.find('.command-bar.popup').hide();
                        });
                        $notification.find('.command-bar.popup .command-mark-as-read').click(function(e){
                            e.preventDefault();
                            _private.visitedEntry(element);
                        });
                        $notification.find('.text').click(function(e){
                            e.preventDefault();
                            Dashbird.Dashboard.showSingleEntry(element.entryId);
                        });
                        $notification.find('.read').click(function(e){
                            e.preventDefault();
                            Dashbird.Dashboard.showSingleEntry(element.entryId);
                        });
                    
                        _private.$.find('.content').append($notification)
                    });
                }
                _private.hideLoading();
            });
        }
        
        
     
        
    }
    
    _private.getHashFromData = function(entryId){
        var hash = null;
        $.each(_private.data.hashes, function(){
            if(this.entryId == entryId){
                hash = this.hash;
                return false;
            }
            return true;
        });
        return hash;
    };
    
    _private.updateHash = function(entryId, hash){
        var inArray = false;
        $.each(_private.data.hashes, function(){
            if(this.entryId == entryId){
                this.hash = hash;
                inArray = true;
                return false;
            }
            return true;
        });
        if(!inArray){
            _private.data.hashes.push({
                entryId : entryId, 
                hash : hash
            });
        }
        me.saveData();
    };
    
    _private.visitedEntry = function(data){
        _private.updateHash(data.entryId, data.hash);
        me.refresh();
    }
    
    _private.visitedEntryByChangingComment = function(data){
        Dashbird.Dashboard.getHash(data.entryId, function(data){
            _private.updateHash(data.entryId, data.hash);
            me.refresh();
        })
    }
        
    return me;
    
}();
