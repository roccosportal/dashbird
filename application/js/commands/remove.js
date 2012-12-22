if(typeof Dashbird == "undefined"){var Dashbird = {};}
if(typeof Dashbird.Commands == "undefined"){Dashbird.Commands  = {};}
Dashbird.Commands.Remove = function(entry){
     var me = Dashbird.Commands.Base(entry),
     _private = {};
     
     me.init = function(){
        entry.commands.$bar.find('.command-remove').click(_private.removeEntryClick);
    };
    
    _private.removeEntryClick = function(e){
        e.preventDefault();
        Dashbird.Modal.show({
            headline: 'Deleting entry', 
            text : 'Do you really want to remove this entry?',
            'cancel-button-text' : 'No, no, I am sorry', 
            'submit-button-text' : 'Remove the rubish!', 
            callback : function(){
                entry.deleteEntry();
            }
        });
    }
    
    return me;
};
