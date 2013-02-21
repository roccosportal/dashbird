SimpleJSLib.EventHandler = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    var _private = {};
    _private.events = [];
    me.fireEvent = function (eventName, data){
        if(typeof(_private.events[eventName]) == 'undefined')
            return;

        var listeners = _private.events[eventName].slice(); // work with a copy
        for (var i = 0; i < listeners.length; i++) {
            if(listeners[i].eventName==eventName){
                listeners[i].callback(data, listeners[i].additionalData);
            }
        }
    };
    me.attachEvent = function (eventName, callback, additionalData){
        if(typeof(additionalData) == 'undefined') additionalData = {};
        if(typeof(_private.events[eventName]) == 'undefined') _private.events[eventName] = [];
        
        _private.events[eventName].push({
            eventName : eventName,
            callback : callback,
            additionalData : additionalData
        });
    };
    me.detachEvent = function (eventName, callback){
        if(typeof(_private.events[eventName]) == 'undefined')
            return;

        var indexes = [];
        var listeners = _private.events[eventName];
        
        for (var i = 0; i < listeners.length; i++) {
            if(listeners[i].name==name &&listeners[i].callback==callback){
                indexes.push(i);
            }
        }

        for (var j = 0; j < indexes.length; j++) {
            _private.events[eventName].splice(indexes[j] - j, 1); // the index is decreasing when we remove multiple items
        }
    };
    return me;
});