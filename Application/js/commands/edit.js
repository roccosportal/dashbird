if(typeof Dashbird == "undefined"){
    var Dashbird = {};

}
if(typeof Dashbird.Commands == "undefined"){
    Dashbird.Commands  = {};
    
}
Dashbird.Commands.Edit = function(entry){
    var me = Dashbird.Commands.Base(entry),
    _private = {};
        
    _private.tags = null;
    _private.bbcode = {};
     
    _private.isOnDemandInited = false;
     
    me.init = function(){
        entry.commands.$bar.find('.command-edit').click(me.show);
        me.set$('command-edit');
    };
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            me.$.find('.submit-button').click(function(e){
                e.preventDefault();
                me.addTag();
                entry.update(me.$.find('textarea').val(), _private.tags);
                me.$.fadeOut();
            });
            
            me.$.find('.cancel-button').click(function(e){
                e.preventDefault();
                me.$.fadeOut();
            });

            me.$.find('.add-tag-input button').click(me.addTag);
            me.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13){ // space and enter
                    me.addTag(e);
                }
            });
        
            _private.bbcode.link = Dashbird.BBCode.Link()
            _private.bbcode.link.init(me.$.find('.command-bar .command-link'), me.$.find('textarea'));

            _private.bbcode.video = Dashbird.BBCode.Video()
            _private.bbcode.video.init(me.$.find('.command-bar .command-video'), me.$.find('textarea'));

            _private.bbcode.image = Dashbird.BBCode.Image()
            _private.bbcode.image.init(me.$.find('.command-bar .command-image'), me.$.find('textarea'));

            _private.bbcode.bold = Dashbird.BBCode.Bold()
            _private.bbcode.bold.init(me.$.find('.command-bar .command-bold'), me.$.find('textarea'));
        
        
            me.$.find('.tag-alert').alert();
            _private.isOnDemandInited = true;
        }
    };
    
     
    me.show = function(e){
        e.preventDefault();
        _private.onDemandInit();
        _private.tags = entry.entryData.tags;
        // fade out all opend options
        me.hideCommands(function(){
            me.$.find('textarea').html(entry.entryData.text);
            me.hideTagAlert();
            me.drawTags();
            // show option
            me.$.fadeIn(function(){
                me.$.find('textarea').focus();
            });
        });

    };
    
    me.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag = me.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _private.tags);
            if(position===-1){ // only add if not already in tags
                me.drawTag(tag);
                _private.tags.push(tag);
                me.$.find('.add-tag-input input').val('');
                me.hideTagAlert();
            }
            else {
                me.showTagAlert();
            }
            
        }
    };
    
    me.showTagAlert = function(){
        me.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        me.$.find('.tag-alert').append($alert);
    };

    me.hideTagAlert = function(){
        me.$.find('.tag-alert').html('');
    };
    
      
    me.drawTags = function(){
        me.$.find('.tags').html('');
        $.each(_private.tags, function(key, element){
            me.drawTag(element);
        });
    };
    
    me.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _private.tags);
            if(position!== -1){ 
                _private.tags.splice(position, 1);
            }
               
        });
        me.$.find('.tags').append($tag);
    };
    

     
    return me;
};