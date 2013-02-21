
Dashbird.Commands.Comment = SimpleJSLib.BaseObject.inherit(function(me, _protected){
  
    _protected.postHtmlLayer = null;
    _protected.isOnDemandInited = false;
    _protected.$layer = null;
    _protected.$button = null;
    _protected.$form = null;
     
    _protected.construct = function(parameters){
        _protected.postHtmlLayer = parameters[0];
        _protected.$layer = _protected.postHtmlLayer.getLayer().find('.new-comment');
        _protected.$button =  _protected.$layer.find('.button');
        _protected.$form =  _protected.$layer.find('.form');
        _protected.$button.click(me.show);
    };
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
             
            _protected.$form.find('.cancel-button').click(function(){
                _protected.$form.hide();
                _protected.$button.show();
            });
            _protected.$form.find('.submit-button').click(function(e){
                e.preventDefault();
                _protected.postHtmlLayer.getPost().addComment(_protected.$form.find('textarea').val(), function(){
                    _protected.$form.hide();
                    _protected.$button.show();
                    _protected.$form.find('textarea').val('');
                })
            });
            _protected.isOnDemandInited = true;
        }
    };
    
    me.show = function(e){
        e.preventDefault();
        _protected.onDemandInit();
        _protected.$button.hide();

        _protected.$form.show();
        _protected.$form.find('textarea').focus();
    };
    return me;
});