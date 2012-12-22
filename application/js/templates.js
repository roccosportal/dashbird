if(typeof Dashbird == "undefined"){var Dashbird = {};}
Dashbird.Templates = function(){
    var me = {}, _private = {};
    
    _private.$ = null;
    _private.templates = {};
    
    me.init = function(){
        _private.$ = $('#templates');
        _private.templates.$entry = _private.$.find('#template-entry');
        _private.templates.$entry.remove();
        _private.templates.$entry = _private.templates.$entry.find('.entry');
        
        
        _private.templates.$foreignEntry = _private.templates.$entry.clone();
        // remove unneccessary stuff
        _private.templates.$foreignEntry.find('.content .command-bar.popup .command-edit').remove();
        _private.templates.$foreignEntry.find('.content .command-bar.popup .command-share').remove();
        _private.templates.$foreignEntry.find('.content .command-bar.popup .command-remove').remove();
        _private.templates.$foreignEntry.find('.content .command.command-edit').remove();
        _private.templates.$foreignEntry.find('.content .command.command-share').remove();
        
        _private.templates.$entryComment = _private.$.find('#template-entry-comment');
        _private.templates.$entryComment.remove();
        _private.templates.$entryComment = _private.templates.$entryComment.find('.comment');

    };
    
    me.getTemplate = function(name){
        switch (name){ 
            case "entry":
                return _private.templates.$entry.clone();
                break;
            case "foreign-entry":
                return _private.templates.$foreignEntry.clone();
                break;
            case "entry-comment":
                return _private.templates.$entryComment.clone();
                break;
        }
        return null;
    }
    
    return me;
}();