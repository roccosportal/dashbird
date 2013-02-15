
Dashbird.Commands.Comment = Dashbird.Commands.Base.inherit(function(me, _protected){
    var _parent = {
        construct :  _protected.construct
    };
    
    _protected.isOnDemandInited = false;
     
    _protected.construct = function(parameters){
        _parent.construct(parameters);
        _protected.postHtmlLayer.getCommandBar().find('.command-comment').click(me.show);
        _protected.set$('command-comment');
    };
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$.find('.cancel-button').click(function(){
                _protected.$.fadeOut();
            });
        
            _protected.$.find('.submit-button').click(function(e){
                e.preventDefault();
                _protected.postHtmlLayer.getPost().addComment(_protected.$.find('textarea').val(), function(){
                    _protected.$.fadeOut(); 
                })
            });
            _protected.isOnDemandInited = true;
        }
    };
    
    me.show = function(e){
        e.preventDefault();
        _protected.onDemandInit();
        // fade out all opend options
        _protected.hideCommands(function(){
            // show option
            _protected.$.fadeIn(function(){
                _protected.$.find('textarea').focus();
            });
        });
    };
    return me;
});