Dashbird.Commands.Remove = Dashbird.Commands.Base.inherit(function(me, _protected){
    var _parent = {
        construct :  _protected.construct
    };
     
    _protected.construct = function(parameters){
        _parent.construct(parameters);
        _protected.postHtmlLayer.getCommandBar().find('.command-remove').click(me.show);
    };
    
    me.show = function(e){
        e.preventDefault();
        Dashbird.Modal.show({
            headline: 'Deleting post', 
            text : 'Do you really want to remove this post?',
            'cancel-button-text' : 'No, no, I am sorry', 
            'submit-button-text' : 'Remove the rubish!', 
            callback : function(){
                _protected.postHtmlLayer.getPost().deletePost();
            }
        });
    }
    
    return me;
});
