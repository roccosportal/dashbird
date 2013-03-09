Dashbird.BBCode.Image = function(){
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
        var selection = _private.$textarea.getSelection();
        if(selection.text != ''){
             _private.$textarea.insertAtCaret('[img]' + selection.text +'[/img]')
        }
        else {
        
            var $modalBody = $('#templates #template-bbcode-image-modal .modal-body').clone();
            Dashbird.Views.Utils.Modal.show({
                headline : 'Image',
                'submit-button-text' : 'Add',
                '$modal-body' : $modalBody,
                callback : function($modal){
                    _private.$textarea.insertAtCaret('[img]' + $modal.find('.image').val() +'[/img]');
                },
                onShown : function($modal){
                    $modal.find('.image').focus();
                    $modal.find('.image').keydown(function(e){
                        if(e.keyCode == 13){ // enter
                            e.preventDefault();
                            $modal.find('.submit-button').click();
                        }
                    });
                }
            });
        }
    }

    return me;
};