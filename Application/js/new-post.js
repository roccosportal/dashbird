if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.NewPost = function(){
    var me = SimpleJSLib.EventHandler(),
    _private = {};
    _private.tags = [];
    _private.bbcode= {};
    
    me.init = function(){
        // new post
        $('#navbar .nav .show-new-post').on('show', _private.onShow);  
    };
    
    _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
            _private.$ = $('#new-post');
            
            _private.$.find('.submit-button').click(_private.onSaveClick);
            _private.$.find('.cancel-button').click(_private.onCancelClick);
       

            _private.$.find('.add-tag-input button').click(_private.addTag);
            _private.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _private.addTag(e);
                }
            });
        
            _private.$.find('.tag-alert').alert();
            
            _private.bbcode.link = Dashbird.BBCode.Link()
            _private.bbcode.link.init(_private.$.find('.command-bar .command-link'), _private.$.find('textarea'));
            
            _private.bbcode.video = Dashbird.BBCode.Video()
            _private.bbcode.video.init(_private.$.find('.command-bar .command-video'), _private.$.find('textarea'));
            
            _private.bbcode.image = Dashbird.BBCode.Image()
            _private.bbcode.image.init(_private.$.find('.command-bar .command-image'), _private.$.find('textarea'));
            
            _private.bbcode.bold = Dashbird.BBCode.Bold()
            _private.bbcode.bold.init(_private.$.find('.command-bar .command-bold'), _private.$.find('textarea'));
             
            _private.isOnDemandInited = true;
        }
    };
    
   
    
    _private.onShow = function(){
        _private.onDemandInit();
        _private.tags = [];
        _private.postShares = [];
        _private.$.find('textarea').val('');
        _private.hideTagAlert();
        _private.drawShares();
        _private.$.find('textarea').focus();
    };
    
    _private.onSaveClick = function(e){
        e.preventDefault();
        _private.addTag();
        $.getJSON('api/post/add/', {
            text :  _private.$.find('textarea').val(),
            tags :  _private.tags,
            shares : _private.postShares
        }, function(data) {
            if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                me.fire('newPost', data[AJAX.DATA]);
                Dashbird.Board.refreshPosts();
                $('#navbar .nav .show-board').tab('show');
                  
            }
        });
    }
    
    _private.onCancelClick = function(e){
        e.preventDefault();
        $('#navbar .nav .show-board').tab('show');
    }
    
    _private.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag =_private.$.find('.add-tag-input input').val();
        if(tag != ''){
            var position = $.inArray(tag, _private.tags);
            if(position===-1){ // only add if not already in tags
                _private.drawTag(tag);
                _private.tags.push(tag);
                _private.$.find('.add-tag-input input').val('');
                _private.hideTagAlert();
            }
            else {
                _private.showTagAlert();
            }
            
        }
    };
    
    _private.showTagAlert = function(){
        _private.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        _private.$.find('.tag-alert').append($alert);
    };

    _private.hideTagAlert = function(){
        _private.$.find('.tag-alert').html('');
    };
    

    
    _private.drawTag = function(tag){
        var $tag = $('#templates #template-tag-editable .tag').clone();
        $tag.find('span').html(tag);
        $tag.find('.delete-button').click(function(){
            $(this).parent().remove();
            var position = $.inArray(tag, _private.tags);
            if(position!== -1){ 
                _private.tags.splice(position, 1);
            }
               
        });
        _private.$.find('.tags').append($tag);
    };
    
    _private.drawShares = function(){
        var $share = null;
        _private.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_private.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _private.shareChange($(this), element.userId);
            });
            _private.$.find('.shares').append($share);
        });
    }
    
    _private.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _private.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _private.postShares);
            if(position!== -1){ 
                _private.postShares.splice(position, 1);
            }
        }
    }
    
    
    
    return me;
}();


