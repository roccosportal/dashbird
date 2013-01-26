if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.LoginBox = function(){
        var me = {},
        _private = {};
                        
        _private.$ = null;
        _private.$name = null;
        _private.$password = null;

        _private.onLoggedIn = function(){
                _private.$.detach();
        };
        
        _private.onLoginFailure = function(){
                _private.$name.parent().parent().addClass('error');
                _private.$password.parent().parent().addClass('error');
        };
        
        me.init = function(){
                _private.$ = $('#login-box');
                _private.$name = $('#login-box-name');
                _private.$password = $('#login-box-password');
				
                _private.$password.keydown(function(e){
                        if(e.keyCode == 13){
                             _private.onSubmit(e);
                        }
                });
                _private.$.submit(_private.onSubmit)

        };
        
        
        me.show = function (){
                _private.$.fadeIn();
                _private.$name.focus();
        };
        
        _private.onSubmit = function(e){
            e.preventDefault();
            Dashbird.User.login(_private.$name.val(),_private.$password.val(), _private.onLoggedIn,  _private.onLoginFailure);
               
        }
        
        me._private = _private; // for inheritance
        return me;
}();