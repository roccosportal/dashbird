if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Base = function(entry){
    var me = {};
    
    me.set$ = function(selector){
         me.$ = entry.$entry.find('.content .command.' + selector);
    }
    
    me.hideCommands = function(callback){
         entry.$entry.find('.content .command').fadeOut().promise().done(callback);
    }
    
    return me;
};