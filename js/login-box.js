var LoginBox = function(){
        var me = {},
        _private = {};
                        
        _private.$handle = null;
        _private.$name = null;
        _private.$password = null;

        _private.onLoggedIn = function(){
                _private.$handle.detach();
        };
        
        me.init = function(){
                _private.$handle = $('#login-box');
                _private.$name = $('#login-box-name');
                _private.$password = $('#login-box-password');
				
                _private.$password.keydown(function(event){
                        if(event.keyCode == 13){
                                Auth.login(_private.$name.val(),_private.$password.val(), _private.onLoggedIn);
                                event.preventDefault();
                        }
                });

        };
        
        
        me.show = function (){
                _private.$handle.fadeIn();
        };
        
        me._private = _private; // for inheritance
        return me;
}();