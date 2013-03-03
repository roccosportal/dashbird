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
Dashbird.AJAX = {
   'STATUS': 'STATUS',
   'STATUS_SUCCESS': 'STATUS_SUCCESS',
   'STATUS_ERROR': 'STATUS_ERROR',
   'MESSAGE': 'MESSAGE',
   'DATA': 'DATA',
   'IS_LOGGED_IN': 'IS_LOGGED_IN',
   'IS_NOT_LOGGED_IN': 'IS_NOT_LOGGED_IN',
   'ALREADY_LOGGED_IN': 'ALREADY_LOGGED_IN',
   'WRONG_DATA': 'WRONG_DATA'
};
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
if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.User = SimpleJSLib.BaseObject.inherit(function (me, _protected){
        _protected.user = null;
        
        me.getUser = function(){
            return _protected.user;
        }
        
        me.isCurrentUser = function(userId){
            return (_protected.user.userId.toString() === userId.toString());
        }
        
        me.init = function(){
            _protected.user = Dashbird.InitialData.User;
        }
                    
        me.getUserShares = function(){
            return _protected.user.userShares;
        }
        
        me.getNameForUser = function(userId){
            var name = 'A person you do not know';
            if(_protected.user.userId == userId){
                name = 'You';
            }
            else {
                $.each(_protected.user.userShares, function(){
                    if(this.userId == userId){
                        name = this.name;
                        return false;
                    }
                    return true;
                });
            }
            return name;
        };

        _protected.savedGravatarHashes = {};
        me.getGravatarHashForUser = function(name){
            if(typeof(_protected.savedGravatarHashes[name]) !== 'undefined')
                return _protected.savedGravatarHashes[name];
            _protected.savedGravatarHashes[name] = Dashbird.Utils.md5(name + '@' + location.hostname);
            return _protected.savedGravatarHashes[name];
        }
        me.getGravatarUrlForUser = function(name, size){
            return 'http://www.gravatar.com/avatar/' + me.getGravatarHashForUser(name) +'?f=y&d=identicon&s=' + size;
        }
       return me;
}).construct();
Dashbird.UserSettings = SimpleJSLib.BaseObject.inherit(function(me, _protected){
     me.addUserShare = function(name, callback){
        $.getJSON('api/user/shares/add/', {
            name : name
        }, function(data){
            var ajaxResponse = Dashbird.AjaxResponse.construct(data);
            if(ajaxResponse.isSuccess){
                Dashbird.User.getUserShares().push(ajaxResponse.data.user);
                callback();
            }
        });
    };
    me.changePassword = function(oldPassword, newPassword, callback){
          $.post('api/user/password/change/', {
              'old-password' : oldPassword,
              'new-password' : newPassword
          }, function(data){
              callback(Dashbird.AjaxResponse.construct(data));
          }, 'json');
    };
    return me;
}).construct();

Dashbird.Settings = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    _protected.persons = {};
    _protected.password = {};
    
    me.init = function(){
       _protected.persons.$ =  $('#settings-persons');
       _protected.password.$ =  $('#settings-password');
       _protected.persons.$.find('.add-person-input .btn').click(_protected.persons.addPersonClick);
       _protected.password.$.find('.submit-button').click( _protected.password.changePasswordClick);
        
        var $user = null;
        $.each(Dashbird.User.getUserShares(), function(key, element){
             $user = $('#templates #template-user li').clone();
             $user.find('span').html(element.name);
             _protected.persons.$.find('ul').append($user);
        });
        
        
    }
    
    _protected.persons.addPersonClick = function(e){
        e.preventDefault();
        var name = _protected.persons.$.find('.add-person-input input').val();
        Dashbird.UserSettings.addUserShare(name, function(){
               var $user = $('#templates #template-user li').clone();
               $user.find('span').html(name);
               _protected.persons.$.find('ul').append($user);
        });
    };
    
    _protected.password.changePasswordClick = function(e){
         e.preventDefault();
         _protected.password.$.find('.alerts').html('');
         var oldPassword = _protected.password.$.find('.old-password').val();
         var newPassword = _protected.password.$.find('.new-password').val();
         Dashbird.UserSettings.changePassword(oldPassword, newPassword, function(ajaxResponse){
             var $alert = null;
             if(ajaxResponse.isSuccess){
                 $alert = $('#templates #template-settings-password-alert-success .alert').clone();
             }
             else {
                 $alert = $('#templates #template-settings-password-alert-error .alert').clone();
             }
             _protected.password.$.find('.alerts').append($alert);
             _protected.password.$.find('.old-password').val('');
             _protected.password.$.find('.new-password').val('');
         });
    }
    return me;
}).construct();
$(document).ready(function (){
    Dashbird.User.init();
    Dashbird.Settings.init();
});
