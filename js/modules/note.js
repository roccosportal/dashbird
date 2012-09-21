if(Dashbird===undefined){
        var Dashbird = {};
}
if(Dashbird.Modules===undefined){
        Dashbird.Modules = {};
}
Dashbird.Modules.Note = {};
Dashbird.Modules.Note.Module = function(){
        var me = {},
        _private = {};
        _private.$addForm = null;
        _private.$addFormText = null;
        
        me.init = function(){

                _private.$addForm = $('#note-module-add-form');
                _private.$addFormText = $('#note-module-add-form-text');
        };
        me.addNote = function (text){
                $.getJSON('ajax/note/add/', {
                        text : text
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                Dashbird.Dashboard.addToTop(data[AJAX.DATA]);
                        }
                });
        };
        me.createDashboardEntry = function(entry){
                var noteEntry = Dashbird.Modules.Note.Entry();
                noteEntry.init(entry);
                return noteEntry.create();
        };

        me.showCommandForm = function(){
                _private.$addForm.show();
                _private.$addFormText.focus();
        };
        me.newEntry = function(){
                me.addNote(_private.$addFormText.val());
                Dashbird.Dashboard.hideCommandForms();
        };
        me._private = _private; // for inheritance
        return me;
}();



Dashbird.Modules.Note.Entry = function(){
        var me = {},
        _private = {};
        _private.dashboardEntry = null;
        me.init = function(entry){
                _private.dashboardEntry = Dashbird.DashboardEntry();
                _private.dashboardEntry.init(entry, this);
        };
        me.create = function(){
                var htmlConfig = {};
                htmlConfig.cssClass = 'note-module';
                htmlConfig.leftColumn = '<img src="' + Dashbird.baseUrl + '/images/button-note-small.png" alt="" />';
                htmlConfig.middleColumn = '<p>' + _private.dashboardEntry.entryData.reference.text.replace(/\n/g,'<br />') + '</p>';
                  
                return _private.dashboardEntry.create(htmlConfig);
        };
                
        me.switchToEditMode = function(){
                _private.dashboardEntry.$middleColumn.html('<textarea class="note-module-text">' + _private.dashboardEntry.entryData.reference.text + '</textarea>');
                _private.dashboardEntry.$middleColumn.find('.note-module-text').focus();
        };
                
        me.switchToNormalMode = function(){
                _private.dashboardEntry.$middleColumn.html('<p>' + _private.dashboardEntry.entryData.reference.text.replace(/\n/g,'<br />') + '</p>');
        };
          
        me.save = function(){                                
                // find text
                _private.dashboardEntry.changedEntryData.reference.text =  _private.dashboardEntry.$middleColumn.find('.note-module-text').val();


                // save status in db
                $.getJSON('ajax/note/edit/', {
                        noteId : _private.dashboardEntry.changedEntryData.reference.noteId, 
                        text : _private.dashboardEntry.changedEntryData.reference.text,
                        tags: _private.dashboardEntry.changedEntryData.tags
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                // save status
                                _private.dashboardEntry.entryData =  _private.dashboardEntry.changedEntryData;
                                 // convert (is converted on serverside aswell)
                                _private.dashboardEntry.entryData.reference.text = Dashbird.Dashboard.htmlEntities(_private.dashboardEntry.entryData.reference.text);
                                
                                _private.dashboardEntry.changedEntryData = {};
                                // switch to normal mode
                                _private.dashboardEntry.switchToNormalMode();
                        }
                });

        };
        me.deleteEntry = function(){

                $.getJSON('ajax/note/delete/', {
                        noteId : _private.dashboardEntry.entryData.reference.noteId
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                // do nothing
                        }
                });
        };
        me._private = _private; // for inheritance
        return me;

};