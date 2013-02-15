Dashbird.AjaxResponse = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.responseData = {};
    
    me.isSuccess = false;
    me.data = {};
    _protected.construct = function(parameters){
        _protected.responseData = parameters[0];
        me.isSuccess = (_protected.responseData[Dashbird.AJAX.STATUS] === Dashbird.AJAX.STATUS_SUCCESS)
        if(me.isSuccess){
            me.data = _protected.responseData[Dashbird.AJAX.DATA];
        }
        return me;
    };
    return me;
});