if(typeof Dashbird == "undefined"){
    var Dashbird = {};

}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Comment = function(entry){
    var me = Dashbird.Commands.Base(entry),
    _private = {};
     
    _private.isOnDemandInited = false;


    me.init = function(){
        entry.commands.$bar.find('.command-comment').click(me.show);
        me.set$('command-comment');
      
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.cancel-button').click(function(){
                me.$.fadeOut();
            });
        
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                entry.addComment(me.$.find('textarea').val(), function(){
                    me.$.fadeOut(); 
                })
            });
            _private.isOnDemandInited = true;
        }
    };
    
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        // fade out all opend options
        me.hideCommands(function(){
            // show option
            me.$.fadeIn(function(){
                me.$.find('textarea').focus();
            });
        });
    };
    return me;
};