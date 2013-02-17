Dashbird.Commands.Share = Dashbird.Commands.Base.inherit(function(me, _protected){
    
    var _parent = {
        construct :  _protected.construct
    };
        
    _protected.isOnDemandInited = false;
   
    
     _protected.construct = function(parameters){
        _parent.construct(parameters);
       
        _protected.postHtmlLayer.getCommandBar().find('.command-share').click(me.show);
        _protected.set$('command-share');
    };
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$.find('.submit-button').click(function(e){
                e.preventDefault();
                _protected.postHtmlLayer.getPost().setPostShares(_protected.postShares);
                _protected.$.fadeOut();
            });

            _protected.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                _protected.$.fadeOut();
            });
            _protected.isOnDemandInited = true;
        }
    };
    
    
    me.show = function(e){
        e.preventDefault();
        _protected.onDemandInit();
        _protected.hideCommands(function(){
            _protected.postShares = _protected.postHtmlLayer.getPost().getPostData().postShares.get();
            _protected.draw();
            // show option
            _protected.$.fadeIn(function(){
                
            });
            _protected.postHtmlLayer.getPost().setLastView();
        });
    };
    
    _protected.draw = function(){
        var $share = null;
        _protected.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_protected.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _protected.shareChange($(this), element.userId);
            });
            _protected.$.find('.shares').append($share);
        });
    }
    
    _protected.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _protected.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _protected.postShares);
            if(position!== -1){ 
                _protected.postShares.splice(position, 1);
            }
        }
    }
     
    return me;
        
});
