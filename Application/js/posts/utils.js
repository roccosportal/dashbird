Dashbird.Utils = SimpleJSLib.BaseObject.inherit(function(me, _protected){
    me.htmlEntities = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
    
    me.convertDate = function(date){
        return date.substring(0, date.length - 3);
    }
    
    me.convertLineBreaks = function(string){
        return string.replace(/\n/g,'<br />');
    }
    return me;
}).construct();