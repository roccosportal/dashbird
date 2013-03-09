Dashbird.Controllers.Utils.AjaxResponse = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.responseData = {};
    me.isSuccess = false;
    me.data = {};

    // namespace
    var AJAX = Dashbird.Controllers.Utils.AJAX;

    _protected.construct = function(parameters){
        _protected.responseData = parameters[0];
        me.isSuccess = (_protected.responseData[AJAX.STATUS] === AJAX.STATUS_SUCCESS)
        if(me.isSuccess){
            me.data = _protected.responseData[AJAX.DATA];
        }
        return me;
    };
    return me;
});