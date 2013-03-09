Dashbird.Commands.Base = SimpleJSLib.BaseObject.inherit(function(me, _protected){
   _protected.postHtmlLayer = null;
   _protected.$ = null;
   
   _protected.construct = function(parameters){
       _protected.postHtmlLayer = parameters[0];
   }
   
    _protected.set$ = function(selector){
         _protected.$ = _protected.postHtmlLayer.getLayer().find('.content .command.' + selector);
    }
    
    _protected.hideCommands = function(callback){
         _protected.postHtmlLayer.getLayer().find('.content .command').fadeOut().promise().done(callback);
    }
    
    return me;
});