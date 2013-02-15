Dashbird.Commands.Edit = Dashbird.Commands.Base.inherit(function(me, _protected){
    var _parent = {
        construct :  _protected.construct
    };
     
    _protected.construct = function(parameters){
        _parent.construct(parameters);
       
        _protected.postHtmlLayer.getCommandBar().find('.command-edit').click(me.show);
        _protected.set$('command-edit');
    };
        
    _protected.tags = null;
    _protected.bbcode = {};
     
    _protected.isOnDemandInited = false;

    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$.find('.submit-button').click(function(e){
                e.preventDefault();
                _protected.addTag();
                _protected.postHtmlLayer.getPost().update(_protected.$.find('textarea').val(), _protected.tags);
                _protected.$.fadeOut();
            });
            
            _protected.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                _protected.$.fadeOut();
            });

            _protected.$.find('.add-tag-input button').click(_protected.addTag);
            _protected.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _protected.addTag(e);
                }
            });
        
            _protected.bbcode.link = Dashbird.BBCode.Link()
            _protected.bbcode.link.init(_protected.$.find('.command-bar .command-link'), _protected.$.find('textarea'));

            _protected.bbcode.video = Dashbird.BBCode.Video()
            _protected.bbcode.video.init(_protected.$.find('.command-bar .command-video'), _protected.$.find('textarea'));

            _protected.bbcode.image = Dashbird.BBCode.Image()
            _protected.bbcode.image.init(_protected.$.find('.command-bar .command-image'), _protected.$.find('textarea'));

            _protected.bbcode.bold = Dashbird.BBCode.Bold()
            _protected.bbcode.bold.init(_protected.$.find('.command-bar .command-bold'), _protected.$.find('textarea'));
        
        
            _protected.$.find('.tag-alert').alert();
            _protected.isOnDemandInited = true;
        }
    };
    
     
    me.show = function(e){
        e.preventDefault();
        _protected.onDemandInit();
        _protected.tags = _protected.postHtmlLayer.getPost().getPostData().tags.get();
        // fade out all opend options
        _protected.hideCommands(function(){
            _protected.$.find('textarea').html(_protected.postHtmlLayer.getPost().getPostData().text.get());
            _protected.hideTagAlert();
            _protected.drawTags();
            // show option
            _protected.$.fadeIn(function(){
                _protected.$.find('textarea').focus();
            });
        });

    };
    
    _protected.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag = _protected.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _protected.tags);
            if(position===-1){ // only add if not already in tags
                _protected.drawTag(tag);
                _protected.tags.push(tag);
                _protected.$.find('.add-tag-input input').val('');
                _protected.hideTagAlert();
            }
            else {
                _protected.showTagAlert();
            }
            
        }
    };
    
    _protected.showTagAlert = function(){
        me.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        _protected.$.find('.tag-alert').append($alert);
    };

    _protected.hideTagAlert = function(){
        _protected.$.find('.tag-alert').html('');
    };
    
      
    _protected.drawTags = function(){
        _protected.$.find('.tags').html('');
        $.each(_protected.tags, function(key, element){
            _protected.drawTag(element);
        });
    };
    
    _protected.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _protected.tags);
            if(position!== -1){ 
                _protected.tags.splice(position, 1);
            }
               
        });
        _protected.$.find('.tags').append($tag);
    };
    

     
    return me;
});