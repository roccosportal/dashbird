if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.User = function (){
        var me = SimpleJSLib.EventHandler(),
        _private = {};
        _private.user = null;
        
        me.getUser = function(){
            return _private.user;
        }
                
        me.isLoggedIn=  false;
        me.init = function(){
                me.attach('onLoggedIn', _private.onLoggedIn);
                // check if logged in
                $.getJSON('ajax/auth/is/logged/in/', function(data) {
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
                
                 $('#logout').click(function(event){
                    me.logout();
                    event.preventDefault();
                });
        };
        
         me.logout = function(){
         $.getJSON('ajax/auth/logout/',{}, function(data) {
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    // refresh page
                    document.location.reload();
                }
         });	
        };

        me.login = function(name, password, callbackOnSuccess, callbackOnFailure){
                if(!this.isLoggedIn){
                        $.post('ajax/auth/login', {
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
                        }, 'json');
                }
        };
        
        _private.onLoggedIn = function(){
            setInterval(function(){
                $.getJSON('ajax/auth/is/logged/in/', function(data) {
                       if(data[AJAX.MESSAGE] === AJAX.IS_NOT_LOGGED_IN){
                               document.location.href = "";
                        }
                });
            }, 600000)
                
                
        }
        
        me.getUserShares = function(){
            return _private.user.userShares;
        }
        
        me.addUserShare = function(name, callback){
            $.getJSON('ajax/user/shares/add/', {
                name : name
            }, function(data){
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.user.userShares.push(data[AJAX.DATA].user);
                    callback();
                }
            });
        };
        
        me.changePassword = function(oldPassword, newPassword, callback){
            $.post('ajax/user/password/change/', {
                'old-password' : oldPassword,
                'new-password' : newPassword
            }, function(data){
                callback(data);
            }, 'json');
        };
        
        me.getNameForUser = function(userId){
            var name = 'A person you do not know';
            if(_private.user.userId == userId){
                name = 'You';
            }
            else {
                $.each(_private.user.userShares, function(){
                    if(this.userId == userId){
                        name = this.name;
                        return false;
                    }
                    return true;
                });
            }
            return name;
        };
        
        // for inheritance
        me._private = _private;
        return me;

}();