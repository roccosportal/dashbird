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
         $.getJSON('api/plugin/data/get/', {
                    name : name
                  
         }, function(data) {
               if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    callback.call(this, data[AJAX.DATA].data);
               }
         });
    }
    
     me.saveData = function(name, data){
         $.post('api/plugin/data/save/', {
                    name : name,
                    data : JSON.stringify(data)
                  
         }, function(data) {
             
         }, 'json');
    }
    
    

    
    return me;
}();
