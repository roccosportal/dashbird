if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Dashboard = function(){
    var me = SimpleJSLib.EventHandler(),
    _private = {};
    _private.$commandBar = null;
    _private.$entries = null;
    _private.loadEntriesAjaxRequestQueue = null;
    _private.boardKeyCapture = false;
    _private.boardBarKeyCapture = false;
        
    me.pager = {};
    me.pager.$moreEntries = null;
    me.pager.startPosition = 0;
    me.pager.entryCount = 10;
    me.pager.hasMoreEntries = false;
    
    me.$loading = null;

    me.init = function (){    
        _private.$entries = $('#board .entries');
        _private.$loading = $('#board .loading');
        _private.$viewAll = $('#board .view-all')
        _private.$viewAll.click(function(e){
             e.preventDefault();
             _private.$viewAll.hide();
             me.refreshEntries();
        })
        me.pager.$moreEntries = $('#board .more-entries')
        me.pager.$moreEntries.click(function(e){
            e.preventDefault();
            me.pager.startPosition =  me.pager.startPosition + me.pager.entryCount;
            me.loadEntries();
        });

       
        
        $('#board').show();
        $('#side-bar').show();
        
        _private.loadEntriesAjaxRequestQueue = SimpleJSLib.SingleRequestQueue();
    };
                
    me.addToTop = function(entryData){
        var entry = Dashbird.DashboardEntry();
        entry.init(entryData);
        var $entry = entry.create();      
                                             
        var first = $('#board .entries .entry:first');
        if(first.length != 0){
            first.before($entry);
        }
        else {
            _private.$entries.append($entry);
        }     
    };
    
    me.refreshEntries = function(){
         me.pager.startPosition = 0;
        _private.$entries.html('');
        me.loadEntries();
    };
    
   
    
    me.getHashes = function(callback){
        $.getJSON('ajax/entries/hashes/get/', {}, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }
    
    me.getHash = function(entryId, callback){
        $.getJSON('ajax/entry/hash/get/', {entryId : entryId}, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                callback(data[AJAX.DATA]);
            }
        });
    }

    me.loadEntries = function(){
        me.pager.$moreEntries.hide();
        _private.$loading.show();                
        var request = _private.loadEntriesAjaxRequestQueue.runAsynchronRequest();
        $.getJSON('ajax/entries/load/', {
            search : Dashbird.Search.getSearchPhrase(),
            'start-position' : me.pager.startPosition,
            'entry-count' : me.pager.entryCount
        }, function(data) {
            if(request.isLatestRequest()){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.$loading.hide();            
                    me.pager.hasMoreEntries = data[AJAX.DATA]['has-more-entries'];
                    if(me.pager.hasMoreEntries===true){
                        me.pager.$moreEntries.show();
                    }
                    else {
                        me.pager.$moreEntries.hide();
                    }
                    var entries = data[AJAX.DATA]['entries'];
                    for (var i = 0; i <  entries.length; i++) {
                        var entryData = entries[i];
                        var entry = Dashbird.DashboardEntry();
                        entry.init(entryData);
                        var $entry = entry.create();
                        _private.$entries.append($entry);                         
                    }
                }
            }
        });		
    };
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
     
    me.showSingleEntry = function(entryId){
        me.pager.$moreEntries.hide();
        _private.$viewAll.show();
        _private.$entries.html('');
        $('#navbar .nav .show-board').tab('show');
        _private.$loading.show();        
         me.getEntry(entryId, function(data) {
            _private.$loading.hide();     
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.fire('showSingleEntry', data[AJAX.DATA]);
                me.addToTop(data[AJAX.DATA]);
            }
        });
    }
    
    
    me.getEntry = function(entryId, callback){
        $.getJSON('ajax/entry/get/', {
            entryId : entryId
        }, function(data) {
           callback(data);
        });
    };
    
    me.getEntries = function(entryIds, callback){
         $.getJSON('ajax/entries/get/', {
            entryIds : entryIds
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