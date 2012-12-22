if(Dashbird===undefined){
    var Dashbird = {};
}
Dashbird.Modal = function(){
    var me = {},
    _private = {};
        
    _private.$overlay = null;
    
    _private.options = null;
        
    me.init = function(){
        _private.$modal = $('#modal');
        _private.$defaultModalBody = _private.$modal.find('.modal-body').clone();
    }
    
    _private.isOnDemandInited = false;
    
    _private.onDemandInit = function(){
        if(!_private.isOnDemandInited){
           // _private.$modal.modal('hide');
            _private.$modal.find('.cancel-button').click(function(e){
                e.preventDefault();
                _private.$modal.modal('hide');
            });
       
            _private.$modal.find('.submit-button').click(function(e){
                e.preventDefault();
                if(_private.options.callback!=null){
                    var returnValue = _private.options.callback(_private.$modal);
                    if(returnValue !== false){
                        _private.$modal.modal('hide');
                    }
                }
                else {
                    _private.$modal.modal('hide');
                }
                
            });
            
            _private.$modal.on('shown',function(){
                if(_private.options.onShown!=null){
                    _private.options.onShown(_private.$modal);
                }
            });
            
            _private.isOnDemandInited = true;
        }
    }
        
    me.show = function(options){
        _private.options = $.extend({
            headline : '',
            text: '',
            '$modal-body' : null,
            callback : null,
            'cancel-button-text' : 'Cancel',
            'submit-button-text' : 'Submit'
        }, options);
        _private.onDemandInit();
        _private.$modal.find('.headline').html(_private.options.headline);
        if(_private.options['$modal-body']==null){
            _private.$defaultModalBody.find()
            _private.$modal.find('.modal-body').replaceWith(_private.$defaultModalBody.clone());
            _private.$modal.find('.text').html(_private.options.text);
        }
        else {
            _private.$modal.find('.modal-body').replaceWith(_private.options['$modal-body']);
        }
        _private.$modal.find('.cancel-button').html(_private.options['cancel-button-text']);
        _private.$modal.find('.submit-button').html(_private.options['submit-button-text']);
        _private.$modal.modal('show');
    }
   
    
    return me;
}();
