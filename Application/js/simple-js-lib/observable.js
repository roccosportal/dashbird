SimpleJSLib.Observable = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.data = {};
    _protected.listeners = []
    
    _protected.construct = function(parameters){
        _protected.data = parameters[0];
    }
    
    me.get = function(){
        return  _protected.data;
    };
    
    me.set = function(data){
        var oldData = _protected.data;
        _protected.data = data;
        if(oldData !== data){
            me.trigger();
        }
    };
    
    me.trigger = function(){
        var listeners = _protected.listeners.slice(); // work with a copy
        for (var i = 0; i < listeners.length; i++) {
            listeners[i].callback(_protected.data);
        }
    }
    
    me.listen = function(callback){
       
        _protected.listeners.push({
            callback : callback
        });
    };
    
    me.unlisten = function (callback){
        var indexes = [];
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].callback==callback){
                indexes.push(i);
            }
        }

        for (var j = 0; j < indexes.length; j++) {
            _protected.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
        }
    };
    
    return me;
});