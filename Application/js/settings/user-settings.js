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

