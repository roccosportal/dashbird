if(typeof(SimpleJSLib)==='undefined'){
    var SimpleJSLib = {};
}
SimpleJSLib.BaseObject = function(){
    var createInheritObject = function(inheritFunctions){
        return {
            construct : function(){
                var me = {}, _protected = {};
                for(var i = 0; i < inheritFunctions.length; i++){
                    me = inheritFunctions[i].call(me, me, _protected);
                    if(typeof(me)==='undefined'){
                        throw 'Inherit function did not return "me"';
                    }
                }
                if(typeof(_protected.construct)!=='undefined'){
                    _protected.construct.call(me, arguments);
                }
                return me;
            },
            inherit : function(inheritFunction){
                var _inheritFunctions = inheritFunctions.slice(0); // clone
                _inheritFunctions.push(inheritFunction);
                return createInheritObject.call(window, _inheritFunctions);
            }
        };
    };

    return {
        inherit : function(inheritFunction){
            return createInheritObject.call(window, [inheritFunction]);
        }
    }
}();