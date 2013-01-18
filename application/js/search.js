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
                     Dashbird.Dashboard.refreshEntries();
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

