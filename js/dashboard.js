if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Dashboard = function(){
    var me = {},
    _private = {};
    _private.$commandBar = null;
    _private.$content = null;
    _private.refreshAjaxRequestQueue = null;
    _private.searchRequestQueue = null;
    _private.boardKeyCapture = false;
    _private.boardBarKeyCapture = false;
        
    me.pager = {};
    me.pager.$nextEntries = null;
    me.pager.startPosition = 0;
    me.pager.entryCount = 5;
    me.pager.hasMoreEntries = false;
       
   
        
    _private.bindBoardKeys = function(){
      
               
               
        $('#board').hover(function(){
            _private.boardKeyCapture = true; 
        },
        function (){
            _private.boardKeyCapture = false; 
        });
               
        // on key down in board
        $(document).bind("keydown", function(event){
            if(_private.boardKeyCapture){
                if(event.ctrlKey == true && event.keyCode == 69){ // CTRL + E
                    if(me.$selectedEntry!=null){
                        me.$selectedEntry.data('dashboardEntry').toggleMode();
                    }
                    event.preventDefault();
                }
                else if(event.ctrlKey == true && event.keyCode == 83){ // CTRL + S
                    if(me.$selectedEntry!=null){
                        me.$selectedEntry.data('dashboardEntry').save();
                    }
                    event.preventDefault();
                }
                else if(event.ctrlKey == true && event.keyCode == 68){  // CTRL + D
                    event.preventDefault();
                    if(me.$selectedEntry!=null){
                        var next = [];
                        me.$selectedEntry.data('dashboardEntry').deleteEntry({
                            beforeDetach :  function(){ // before detach
                                if(me.$selectedEntry!=null){
                                    next = me.$selectedEntry.next();
                                }
                            },
                            afterDetach :  function(){ // after detach
                                if(next.length != 0){
                                    // select next one
                                    me.$selectedEntry = next;
                                    me.$selectedEntry.addClass('selected');
                                    me.$selectedEntry.focus();
                                }
                                else {
                                    me.$selectedEntry = null;
                                }
                            }
                        });
                    }
                }
            }
        });
                
    }
        
        
        
    me.$selectedEntry = null;
    me.init = function (){
               
        _private.$content = $('#content');
               
        me.pager.$nextEntries = $('#next-entries')
        me.pager.$nextEntries.click(function(e){
            e.preventDefault();
            me.pager.startPosition =  me.pager.startPosition + me.pager.entryCount;
            me.refresh();
            me.pager.$backEntries.show();
        });
        me.pager.$backEntries = $('#back-entries')
        me.pager.$backEntries.click(function(e){
            e.preventDefault();
            me.pager.startPosition =  me.pager.startPosition - me.pager.entryCount;
            me.refresh();
            if(me.pager.startPosition == 0){
                me.pager.$backEntries.hide();
            }
        });
            
        // searching
        _private.$searchBox = $('#search-box');
        _private.$searchBarButton = $('#side-bar .search');
        _private.$searchBar = $('#search-bar');
        _private.searchRequestQueue = SimpleJSLib.SingleRequestQueue();
        _private.searchRequestQueue.setTimeout(200);
        _private.$searchBarButton.click(function(){
            _private.$searchBar.toggle();
        });
        _private.$searchBox.keyup(function(){
            _private.searchRequestQueue.addToQueue({}, function(data){
                me.pager.startPosition = 0;
                me.refresh();
            });
        });
                
        // new entry
        _private.$newEntryBox = $('#new-entry');
        _private.$newEntryButton = $('#side-bar .new');
        _private.$newEntryButton.click(function(){
            _private.$newEntryBox.toggle();
            if(_private.$newEntryBox.is(':visible') ) {
                document.location.href = '#new-entry';
            }
        });
        $('#new-entry .commands .save-button').click(function(){
            $.getJSON('ajax/entry/add/', {
                text :  $('#new-entry #new-entry-text').val()
            }, function(data) {
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    Dashbird.Dashboard.addToTop(data[AJAX.DATA]);
                    _private.$newEntryBox.hide();
                }
            }); 
        });
        $('#new-entry .commands .cancel-button').click(function(){
            _private.$newEntryBox.hide();
        });
                
        _private.$settings =  $('#settings');
        $('#side-bar .settings').click(function(){
            _private.$settings.toggle();
        });
  
        _private.bindBoardKeys();

            
        $('#board').show();
        $('#side-bar').show();
                        
            
        _private.refreshAjaxRequestQueue = SimpleJSLib.SingleRequestQueue();
        me.refresh();
                        
        $('#logout').click(function(event){
            $.getJSON('ajax/logout/',{}, function(data) {
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    // refresh page
                    document.location.reload();
                }
            });	
            event.preventDefault();
        });
    };
                
    me.addToTop = function(entryData){
        var entry = Dashbird.DashboardEntry();
        entry.init(entryData);
        var $entry = entry.create();      
                              
                              
        var first = $('#board #content .entry:first');
        if(first.length != 0){
            first.before($entry);
        }
        else {
            _private.$content.append($entry);
        }
                
    };

    me.refresh = function(){
                        
        var request = _private.refreshAjaxRequestQueue.runAsynchronRequest();
        $.getJSON('ajax/get/dashboard/entries/', {
            search : _private.$searchBox.val(),
            'start-position' : me.pager.startPosition,
            'entry-count' : me.pager.entryCount
        }, function(data) {
            if(request.isLatestRequest()){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    me.pager.hasMoreEntries = data[AJAX.DATA]['has-more-entries'];
                    if(me.pager.hasMoreEntries===true){
                        me.pager.$nextEntries.show();
                    }
                    else {
                        me.pager.$nextEntries.hide();
                    }
                    _private.$content.html('');
                    var entries = data[AJAX.DATA]['entries'];
                    for (var i = 0; i <  entries.length; i++) {
                        var entryData = entries[i];
                        var entry = Dashbird.DashboardEntry();
                        entry.init(entryData);
                        var $entry = entry.create();
                        _private.$content.append($entry);
                                                
                    }
                }
            }
        });		
    };
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
        
     
    me._private = _private; // for inheritance
           
    return me;
}();