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
