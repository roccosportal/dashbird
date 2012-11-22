if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.Auth = function (){
        var me = SimpleJSLib.EventHandler(),
        _private = {};
        _private.user = null;
        
        me.getUser = function(){
            return _private.user;
        }
                
        me.isLoggedIn=  false;
        me.init = function(){
                // check if logged in
                $.getJSON('ajax/is/logged/in/', function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                if(data[AJAX.MESSAGE] === AJAX.IS_NOT_LOGGED_IN){
                                        Dashbird.LoginBox.show();
                                }
                                else {
                                        _private.user = data[AJAX.DATA].user;
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                        }
                });
        };

        me.login = function(name, password, callbackOnSuccess, callbackOnFailure){
                if(!this.isLoggedIn){
                        $.getJSON('ajax/login', {
                                name : name, 
                                password : password
                        }, function(data) {
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                        _private.user = data[AJAX.DATA].user;
                                        callbackOnSuccess();
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                                else {
                                      callbackOnFailure();
                                }
                        });
                }
        };
        
        // for inheritance
        me._private = _private;
        return me;

}();