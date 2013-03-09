Dashbird.BBCode.Link = function(){
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
             _private.$textarea.insertAtCaret('[url]' + selection.text +'[/url]')
        }
        else {
            var $modalBody = $('#templates #template-bbcode-link-modal .modal-body').clone();
            Dashbird.Views.Utils.Modal.show({
                headline : 'Link',
                'submit-button-text' : 'Add',
                '$modal-body' : $modalBody,
                callback : function($modal){
                    _private.$textarea.insertAtCaret('[url]' + $modal.find('.link').val() +'[/url]');
                },
                onShown : function($modal){
                    $modal.find('.link').focus();
                    $modal.find('.link').keydown(function(e){
                        if(e.keyCode == 13){ // enter
                            e.preventDefault();
                            $modal.find('.submit-button').click();
                        }
                    });
                }
            });
        }
    };
    return me;
};