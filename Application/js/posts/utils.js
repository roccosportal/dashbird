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
    
    // http://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling
    me.bottomIsOnScreen = function(elem)
    {
        var docViewTop = $(window).scrollTop();
        docViewTop += 40; // there is a border
        //var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return (elemBottom >= docViewTop);
    }
    
    me.topIsOnScreen = function(elem)
    {
        var docViewTop = $(window).scrollTop();
        docViewTop += 40; // there is a border
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        //var elemBottom = elemTop + $(elem).height();

        return (elemTop <= docViewBottom);
    }
    return me;
}).construct();