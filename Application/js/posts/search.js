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

