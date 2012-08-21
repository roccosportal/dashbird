if(SimpleJSLib===undefined){
        var SimpleJSLib = {};
}
SimpleJSLib.EventHandler = function(){
        var me = {},
        _private = {};
    
        _private.listeners = [];

        me.fire = function (name){
                for (var i = 0; i < _private.listeners.length; i++) {
                        if(_private.listeners[i].name==name){
                                _private.listeners[i].callback();
                        }
                }
        };
        me.attach = function (name, callback){
                _private.listeners.push({
                        name : name, 
                        callback : callback
                });
        };
        me.detach = function (name, callback){
                var indexes = [];
                for (var i = 0; i < _private.listeners.length; i++) {
                        if(_private.listeners[i].name==name && _private.listeners[i].callback==callback){
                                indexes.push(i);
                        }
                }

                for (var j = 0; j < indexes.length; j++) {
                        _private.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
                }
        };
        
           // for inheritance
        me._private = _private;
        
        return me;	
};