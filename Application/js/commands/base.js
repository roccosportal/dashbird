if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Base = function(post){
    var me = {};
    
    me.set$ = function(selector){
         me.$ = post.$post.find('.content .command.' + selector);
    }
    
    me.hideCommands = function(callback){
         post.$post.find('.content .command').fadeOut().promise().done(callback);
    }
    
    return me;
};