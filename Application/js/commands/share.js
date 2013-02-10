if(typeof Dashbird == "undefined"){
    var Dashbird = {};

}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Share = function(post){
    var me = Dashbird.Commands.Base(post),
    _private = {};
        
    _private.isOnDemandInited = false;
        
    me.init = function(){
        post.commands.$bar.find('.command-share').click(me.show);
        me.set$('command-share');
       
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                post.setPostShares(_private.postShares);
                me.$.fadeOut();
            });

            me.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                me.$.fadeOut();
            });
            _private.isOnDemandInited = true;
        }
    };
    
    
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        me.hideCommands(function(){
            _private.postShares = post.postData.postShares;
            _private.draw();
            // show option
            me.$.fadeIn(function(){
                
                });
        });
    };
    
    _private.draw = function(){
        var $share = null;
        me.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_private.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _private.shareChange($(this), element.userId);
            });
            me.$.find('.shares').append($share);
        });
    }
    
    _private.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _private.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _private.postShares);
            if(position!== -1){ 
                _private.postShares.splice(position, 1);
            }
        }
    }
     
    return me;
        
};
