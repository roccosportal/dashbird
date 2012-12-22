if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.BBCode == "undefined"){Dashbird.BBCode  = {};}
Dashbird.BBCode.Bold = function(){
    var me = {}, _private = {};
    
    _private.$button = null;
    _private.$textarea = null;
    me.init = function($button, $textarea){
        _private.$button = $button;
        _private.$textarea = $textarea;
        _private.$button.click( _private.onButtonClick);
    };
    
    _private.onButtonClick = function(e){
        e.preventDefault();
        _private.$textarea.insertAtCaret('[b]' + _private.$textarea.getSelection().text + '[/b]');
    }

    return me;
};