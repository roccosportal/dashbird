if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.UserShares = function(){
    var me = {},
    _private = {};
    _private.$userSharesList = null;
    _private.$addUserShareBox = null;
    _private.$addUserShareButton = null;
    _private.$addUserShareButton = null;
    _private.userShares = null
    
    
    _private.refreshUserSharesList = function(){
        _private.$userSharesList.empty();
        $.getJSON('ajax/get/user/shares/', {
            }, function(data) {
                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                    _private.userShares = data[AJAX.DATA];
                    for (var i = 0; i <  _private.userShares.length; i++) {
                        _private.$userSharesList.append('<li>' +  _private.userShares[i].name + '</li>');
                    }
                }
            });  
    };
    
    
    _private.addUserShareButton_Click = function(){
        $.getJSON('ajax/add/user/share/', {
            name : _private.$addUserShareBox.val()
        }, function(data){
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                _private.refreshUserSharesList();
            }
        })
    };
    
    me.init = function(){
       
        _private.$userSharesList = $('#user-shares-list');
        _private.$addUserShareBox = $('#add-user-share-box');
        _private.$addUserShareButton = $('#add-user-share-button')
        _private.$addUserShareButton.click(_private.addUserShareButton_Click);
        _private.refreshUserSharesList();
    };
    
    me.getUserShares = function(){
        return _private.userShares;
    }
    
    
    me._private = _private; // for inheritance
    return me;
}();

Dashbird.EntrySharesBox = function(){
    var me = {},
    _private = {};
    _private.isInitiated = false;
    _private.$entrySharesList = null;
    _private.currentEntry = null;
    
    _private.onSave = function(e){
        e.preventDefault();
        if(_private.currentEntry !== null){
            var userIds = [];
            _private.$entrySharesList.find('li').each(function(){
                if($(this).find('input').attr('checked')){
                    userIds.push($(this).data('userid').toString());
                } 
            });
           _private.currentEntry.setEntryShares(userIds);
            _private.currentEntry = null;
        }
        _private.$entrySharesBox.hide();
    }
    
    
    me.init = function(){
        if(!_private.isInitiated){
            _private.$entrySharesList = $('#entry-shares-list');
            _private.$entrySharesBox = $('#entry-shares-box');
            _private.$entrySharesBox.find('.save-button').click(_private.onSave);
            _private.$entrySharesBox.find('.cancel-button').click(function(e){
                e.preventDefault();
                _private.$entrySharesBox.hide();
                _private.currentEntry = null;
                
            })
            _private.isInitiated = true;
        }
    }
    me.show = function(entry){
        me.init();
        _private.currentEntry = entry;
        _private.$entrySharesList.empty();
        var html = '';
        var currentUserShare = null;
        for (var i = 0; i <  Dashbird.UserShares.getUserShares().length; i++) {
            currentUserShare = Dashbird.UserShares.getUserShares()[i];
            html = html + '<li data-userid="'+ currentUserShare.userId +'">';
            html = html + '<input type="checkbox" ';
            if($.inArray(currentUserShare.userId, entry.entryData.entryShares)!== -1){
                html = html + 'checked="checked"';
            }
            html = html + '/>';
            html = html + currentUserShare.name;
            html = html + '</li>';
        }
        _private.$entrySharesList.append(html);
        _private.$entrySharesBox.css('top', entry.$entry.position().top)
        _private.$entrySharesBox.css('left', entry.$entry.position().left)
        _private.$entrySharesBox.show();
    };
    me._private = _private; // for inheritance
    return me;
}();



