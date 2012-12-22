if(Dashbird===undefined){
        var Dashbird = {};
}


Dashbird.PluginManager = function (){
    var me = {},
    _private = {};
    
    me.init = function(){
        Dashbird.Plugins.Notifications.init();
        
    };
    
    me.loadData = function(name, callback){
         $.getJSON('ajax/plugin/data/get/', {
                    name : name
                  
         }, function(data) {
               if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    callback.call(this, data[AJAX.DATA].data);
               }
         });
    }
    
     me.saveData = function(name, data){
         $.getJSON('ajax/plugin/data/save/', {
                    name : name,
                    data : JSON.stringify(data)
                  
         }, function(data) {
             
         });
    }
    
    

    
    return me;
}();
