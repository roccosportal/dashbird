Dashbird.NewPost = SimpleJSLib.EventHandler.inherit(function(me, _protected){
    _protected = {};
    _protected.tags = [];
    _protected.bbcode= {};
    
    me.init = function(){
        // new post
        $('#navbar .nav .show-new-post').on('show', _protected.onShow);  
    };
    
    _protected.isOnDemandInited = false;
    
    _protected.onDemandInit = function(){
        if(!_protected.isOnDemandInited){
            _protected.$ = $('#new-post');
            
            _protected.$.find('.submit-button').click(_protected.onSaveClick);
            _protected.$.find('.cancel-button').click(_protected.onCancelClick);
       

            _protected.$.find('.add-tag-input button').click(_protected.addTag);
            _protected.$.find('.add-tag-input input').keydown(function(e){
                if(e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 186 || e.keyCode == 188){ // space, enter, ";", ","
                    _protected.addTag(e);
                }
            });
        
            _protected.$.find('.tag-alert').alert();
            
            _protected.bbcode.link = Dashbird.BBCode.Link()
            _protected.bbcode.link.init(_protected.$.find('.command-bar .command-link'), _protected.$.find('textarea'));
            
            _protected.bbcode.video = Dashbird.BBCode.Video()
            _protected.bbcode.video.init(_protected.$.find('.command-bar .command-video'), _protected.$.find('textarea'));
            
            _protected.bbcode.image = Dashbird.BBCode.Image()
            _protected.bbcode.image.init(_protected.$.find('.command-bar .command-image'), _protected.$.find('textarea'));
            
            _protected.bbcode.bold = Dashbird.BBCode.Bold()
            _protected.bbcode.bold.init(_protected.$.find('.command-bar .command-bold'), _protected.$.find('textarea'));
             
            _protected.isOnDemandInited = true;
        }
    };
    
   
    
    _protected.onShow = function(){
        _protected.onDemandInit();
        _protected.tags = [];
        _protected.postShares = [];
        _protected.$.find('textarea').val('');
        _protected.hideTagAlert();
        _protected.drawShares();
        _protected.$.find('.add-tag-input input').val('');
        _protected.$.find('.tags').empty();
        _protected.$.find('textarea').focus();
    };
    
    _protected.onSaveClick = function(e){
        e.preventDefault();
        _protected.addTag();
        $.getJSON('api/post/add/', {
            text :  _protected.$.find('textarea').val(),
            tags :  _protected.tags,
            shares : _protected.postShares
        }, function(data) {
            var ajaxResponse = Dashird.AjaxResponse.construct().init(data);
            if(ajaxResponse.isSuccess){
                me.fireEvent('newPost', ajaxResponse.data);
                Dashbird.Board.refreshPosts();
                $('#navbar .nav .show-board').tab('show');
                  
            }
        });
    }
    
    _protected.onCancelClick = function(e){
        e.preventDefault();
        $('#navbar .nav .show-board').tab('show');
    }
    
    _protected.addTag = function(e){
        if(e!=null){
            e.preventDefault();
        }
            
        var tag =_protected.$.find('.add-tag-input input').val();
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
        _protected.hideTagAlert();
        var $alert = $('#templates #template-tag-alert .alert').clone();
        _protected.$.find('.tag-alert').append($alert);
    };

    _protected.hideTagAlert = function(){
        _protected.$.find('.tag-alert').html('');
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
    
    _protected.drawShares = function(){
        var $share = null;
        _protected.$.find('.shares').html('');
        $.each(Dashbird.User.getUserShares(), function(key, element){
            $share = $('#templates #template-share-editable .checkbox').clone();
            $share.find('span').html(element.name);
            if($.inArray(this.userId,_protected.postShares)!== -1){
                $share.find('input').attr('checked', 'checked');
            }
            $share.find('input').change(function(){
                _protected.shareChange($(this), element.userId);
            });
            _protected.$.find('.shares').append($share);
        });
    }
    
    _protected.shareChange = function($shareInput, userId){
        if($shareInput.attr('checked')){
            _protected.postShares.push(userId);
        }
        else {
            var position = $.inArray(userId, _protected.postShares);
            if(position!== -1){ 
                _protected.postShares.splice(position, 1);
            }
        }
    }
    
    
    
    return me;
}).construct();;


