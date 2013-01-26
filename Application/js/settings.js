if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Settings = function(){
    var me = {},
    _private = {};
   
    _private.persons = {};
    _private.password = {};
    
    me.init = function(){
       $('#navbar .nav .show-settings').on('show', _private.onShow);
       _private.persons.$ =  $('#settings-persons');
       _private.password.$ =  $('#settings-password');
    }
    
     _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
           _private.persons.$.find('.add-person-input .btn').click(_private.persons.addPersonClick);
           var $user = null;
           $.each(Dashbird.User.getUserShares(), function(key, element){
                $user = $('#templates #template-user li').clone();
                $user.find('span').html(element.name);
                _private.persons.$.find('ul').append($user);
           });
           
           
           _private.password.$.find('.submit-button').click( _private.password.changePasswordClick);
           
           
           _private.isOnDemandInited = true;
        }
         _private.password.$.find('.alerts').html('');
    }
    
    _private.onShow = function(){
       _private.onDemandInit();
    }

    
    _private.persons.addPersonClick = function(e){
        e.preventDefault();
        var name = _private.persons.$.find('.add-person-input input').val();
        Dashbird.User.addUserShare(name, function(){
               var $user = $('#templates #template-user li').clone();
               $user.find('span').html(name);
               _private.persons.$.find('ul').append($user);
        });
    };
    
    _private.password.changePasswordClick = function(e){
         e.preventDefault();
         _private.password.$.find('.alerts').html('');
         var oldPassword = _private.password.$.find('.old-password').val();
         var newPassword = _private.password.$.find('.new-password').val();
         Dashbird.User.changePassword(oldPassword, newPassword, function(data){
             var $alert = null;
             if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                 $alert = $('#templates #template-settings-password-alert-success .alert').clone();
             }
             else {
                 $alert = $('#templates #template-settings-password-alert-error .alert').clone();
             }
             _private.password.$.find('.alerts').append($alert);
         })
    }
   
    
    return me;
}();
