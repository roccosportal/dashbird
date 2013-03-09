Dashbird.BBCode.Video = function(){
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

        var $modalBody = $('#templates #template-bbcode-video-modal .modal-body').clone();
        Dashbird.Views.Utils.Modal.show({
            headline : 'Video',
            'submit-button-text' : 'Add',
            '$modal-body' : $modalBody,
            callback : _private.onCallback,
            onShown : function($modal){
                $modal.find('.video').focus();
                $modal.find('.video').keydown(function(e){
                      if(e.keyCode == 13){ // enter
                          e.preventDefault();
                          $modal.find('.submit-button').click();
                      }
                  });
            }
        });
        
    }
    
    _private.onCallback = function($modal){
        $modal.find('.alerts').html('');
        var video = $modal.find('.video').val();
        if(video.indexOf('youtube.com') !== -1){
            var pos = video.indexOf('=');
            if(pos !== -1){
                video = 'https://www.youtube.com/embed/' + video.substring(pos + 1, video.length);
                _private.$textarea.insertAtCaret('[youtube]' + video +'[/youtube]');
                return true;
            }
        }
        else if(video.indexOf('vimeo.com') !== -1){
            var pos = video.indexOf('vimeo.com/');
            if(pos !== -1){
                video = 'https://player.vimeo.com/video/' + video.substring(pos + 10, video.length);
                _private.$textarea.insertAtCaret('[vimeo]' + video +'[/vimeo]');
                return true;
            }
        }
        var $alert  = $('#templates #template-bbcode-video-modal-alert .alert').clone();
        $modal.find('.alerts').append($alert);
        return false;
    };

    return me;
};