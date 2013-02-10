if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Remove = function(post){
     var me = Dashbird.Commands.Base(post),
     _private = {};
     
     me.init = function(){
        post.commands.$bar.find('.command-remove').click(_private.removePostClick);
    };
    
    _private.removePostClick = function(e){
        e.preventDefault();
        Dashbird.Modal.show({
            headline: 'Deleting post', 
            text : 'Do you really want to remove this post?',
            'cancel-button-text' : 'No, no, I am sorry', 
            'submit-button-text' : 'Remove the rubish!', 
            callback : function(){
                post.deletePost();
            }
        });
    }
    
    return me;
};
