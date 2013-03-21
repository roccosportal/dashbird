/**
 * This controller allows to add user shares and change the password for a user.
 *
 */
Dashbird.Controllers.UserSettings = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    // Adds a user share to the current user.
    // @param name <string> the user name
    // @param callback <function(<object>)> returns user object
    me.addUserShare = function(name, callback){
      $.getJSON('api/user/shares/add/', {
          name : name
      }, function(data){
          var ajaxResponse = Dashbird.Controllers.Utils.AjaxResponse.construct(data);
          if(ajaxResponse.isSuccess){
              Dashbird.Controllers.User.getUserShares().push(ajaxResponse.data.user);
              callback();
          }
      });
    };
    // Changes the password.
    // @param oldPassword <string>
    // @param newPassword <string>
    // @param callback <function(<Dashbird.Controllers.Utils.AjaxResponse>)>
    me.changePassword = function(oldPassword, newPassword, callback){
          $.post('api/user/password/change/', {
              'old-password' : oldPassword,
              'new-password' : newPassword
          }, function(data){
              callback(Dashbird.Controllers.Utils.AjaxResponse.construct(data));
          }, 'json');
    };
    return me;
}).construct();