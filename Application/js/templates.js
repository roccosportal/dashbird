if(typeof Dashbird == "undefined"){var Dashbird = {};}
Dashbird.Templates = function(){
    var me = {}, _private = {};
    
    _private.$ = null;
    _private.templates = {};
    
    me.init = function(){
        _private.$ = $('#templates');
        _private.templates.$post = _private.$.find('#template-post');
        _private.templates.$post.remove();
        _private.templates.$post = _private.templates.$post.find('.post');
        
        
        _private.templates.$foreignPost = _private.templates.$post.clone();
        // remove unneccessary stuff
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-edit').remove();
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-share').remove();
        _private.templates.$foreignPost.find('.content .command-bar.popup .command-remove').remove();
        _private.templates.$foreignPost.find('.content .command.command-edit').remove();
        _private.templates.$foreignPost.find('.content .command.command-share').remove();
        
        _private.templates.$postComment = _private.$.find('#template-post-comment');
        _private.templates.$postComment.remove();
        _private.templates.$postComment = _private.templates.$postComment.find('.comment');

    };
    
    me.getTemplate = function(name){
        switch (name){ 
            case "post":
                return _private.templates.$post.clone();
                break;
            case "foreign-post":
                return _private.templates.$foreignPost.clone();
                break;
            case "post-comment":
                return _private.templates.$postComment.clone();
                break;
        }
        return null;
    }
    
    return me;
}();