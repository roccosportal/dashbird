SimpleJSLib.SingleRequestQueue = SimpleJSLib.BaseObject.inherit(function(me, _protected){
        _protected.latestRequestId = 0;
        _protected. timeout = null;

        me.setTimeout = function (timeout){
                _protected. timeout = timeout;
        };
        
        me.getLatestRequestId = function(){
                return _protected.latestRequestId;
        }
        
        me.addToQueue = function(data, callback){
                if(_protected. timeout===null){
                        throw "Timeout was not set. Use the setTimeout function of this object."
                }
                _protected.latestRequestId++;
                var currentRequestId = _protected.latestRequestId;
                setTimeout(function (){
                        if(_protected.latestRequestId===currentRequestId){
                                callback(data);
                        }
                }, _protected. timeout);
        };
        
        me.runAsynchronRequest = function(){

                _protected.latestRequestId++;
                var currentRequestId = _protected.latestRequestId;
                var requestObject = {
                        currentRequestId : currentRequestId,
                        isLatestRequest : function(){
                                return _protected.latestRequestId===currentRequestId;
                        }
                };
                return requestObject;
        };
        return me;	
});