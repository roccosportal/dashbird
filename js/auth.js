if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.Auth = function (){
        var me = SimpleJSLib.EventHandler(),
        _private = {};
                
        me.isLoggedIn=  false;
        me.init = function(){
                // check if logged in
                $.getJSON('ajax/is/logged/in/', function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                if(data[AJAX.MESSAGE] === AJAX.IS_NOT_LOGGED_IN){
                                        Dashbird.LoginBox.show();
                                }
                                else {
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                        }
                });
        };

        me.login = function(name, password, callbackOnSuccess){
                if(!this.isLoggedIn){
                        $.getJSON('ajax/login', {
                                name : name, 
                                password : password
                        }, function(data) {
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                        callbackOnSuccess();
                                        me.isLoggedIn = true;
                                        me.fire('onLoggedIn');
                                }
                        });
                }
        };
        
        // for inheritance
        me._private = _private;
        return me;

}();