if(SimpleJSLib===undefined){
        var SimpleJSLib = {};
}
SimpleJSLib.SingleRequestQueue = function(){
        var me = {},
        _private = {};
                
        _private.latestRequestId = 0;
        _private. timeout = null;

        me.setTimeout = function (timeout){
                _private. timeout = timeout;
        };
        
        me.getLatestRequestId = function(){
                return _private.latestRequestId;
        }
        
        me.addToQueue = function(data, callback){
                if(_private. timeout===null){
                        throw "Timeout was not set. Use the setTimeout function of this object."
                }
                _private.latestRequestId++;
                var currentRequestId = _private.latestRequestId;
                setTimeout(function (){
                        if(_private.latestRequestId===currentRequestId){
                                callback(data);
                        }
                }, _private. timeout);
        };
        
        me.runAsynchronRequest = function(){

                _private.latestRequestId++;
                var currentRequestId = _private.latestRequestId;
                var requestObject = {
                        currentRequestId : currentRequestId,
                        isLatestRequest : function(){
                                return _private.latestRequestId===currentRequestId;
                        }
                };
                return requestObject;
        };
        
        // for inheritance
        me._private = _private;
        
        return me;	
};