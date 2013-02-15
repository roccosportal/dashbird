SimpleJSLib.EventHandler = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.listeners = [];
    me.fireEvent = function (name, data){
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].name==name){
                _protected.listeners[i].callback(data);
            }
        }
    };
    me.attachEvent = function (name, callback){
        _protected.listeners.push({
            name : name, 
            callback : callback
        });
    };
    me.detachEvent = function (name, callback){
        var indexes = [];
        for (var i = 0; i < _protected.listeners.length; i++) {
            if(_protected.listeners[i].name==name && _protected.listeners[i].callback==callback){
                indexes.push(i);
            }
        }

        for (var j = 0; j < indexes.length; j++) {
            _protected.listeners.splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
        }
    };
    return me;
});