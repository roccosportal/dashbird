if(Dashbird===undefined){
        var Dashbird = {};
}
if(Dashbird.Modules===undefined){
        Dashbird.Modules = {};
}
Dashbird.Modules.Todo = {};
Dashbird.Modules.Todo.Module = function(){
        var me = {},
        _private = {};
        _private.$addForm = null;
        _private.$addFormText = null;
        me.init = function(){
                
                _private.$addForm = $('#todo-module-add-form');
                _private.$addFormText = $('#todo-module-add-form-text');
        };
        me.newEntry = function(){
                me.addTodo(_private.$addFormText.val());
                Dashbird.Dashboard.hideCommandForms();
        };
        me.addTodo = function (text){
                $.getJSON('ajax/todo/add/', {
                        text : text
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                Dashbird.Dashboard.addToTop(data[AJAX.DATA]);	
                        }
                });
        };
        me.createDashboardEntry = function(entry){
                var todoEntry = Dashbird.Modules.Todo.Entry()
                todoEntry.init(entry);
                return todoEntry.create();
        };

        me.showCommandForm = function(){
                _private.$addForm.show();
                _private.$addFormText.focus();
        };
        me._private = _private; // for inheritance
        return me;
}();



Dashbird.Modules.Todo.Entry = function(){
        var me = {},
        _private = {};
        _private.dashboardEntry = null;
        me.init = function(entry){
                _private.dashboardEntry = Dashbird.DashboardEntry();
                _private.dashboardEntry.init(entry, this);
        };
        me.create = function(){
               
                        
                var htmlConfig = {};
                htmlConfig.cssClass = 'todo-module';
                if( _private.dashboardEntry.entryData.reference.isDone=='1'){
                        htmlConfig.leftColumn = '<img data-is-done="true" src="' + Dashbird.baseUrl + '/images/button-todo-done-small.png" alt="" />';
                }
                else {
                        htmlConfig.leftColumn = '<img data-is-done="false" src="' + Dashbird.baseUrl + '/images/button-todo-not-done-small.png" alt="" />';
                }
                htmlConfig.middleColumn = '<p>' + _private.dashboardEntry.entryData.reference.text.replace(/\n/g,'<br />') + '</p>';
                var $entry =  _private.dashboardEntry.create(htmlConfig);
                        
                           
                $entry.find('.dashboard-entry-left-column img').click(function(){
                        me.toggleStatus();
                });
                        
                return $entry;
        };
                
                
        me.toggleStatus = function(){
                        
                if(!_private.dashboardEntry.editMode){
                        _private.dashboardEntry.changedEntryData = $.extend(true, {}, _private.dashboardEntry.entryData);
                }
                    
                _private.dashboardEntry.changedEntryData.reference.isDone = _private.dashboardEntry.changedEntryData.reference.isDone == '1' ? '0' : '1';

                if( _private.dashboardEntry.changedEntryData.reference.isDone == '1'){
                        _private.dashboardEntry.$entry.find('.dashboard-entry-left-column img').attr('src', Dashbird.baseUrl + '/images/button-todo-done-small.png' );
                        _private.dashboardEntry.deleteTag( 'undone');
                        _private.dashboardEntry.addTag('isdone');
                }
                else {
                        _private.dashboardEntry.$entry.find('.dashboard-entry-left-column img').attr('src', Dashbird.baseUrl + '/images/button-todo-not-done-small.png' );
                        _private.dashboardEntry.deleteTag('isdone');
                        _private.dashboardEntry.addTag('undone');
                }
                        
                if(!_private.dashboardEntry.editMode){
                      
                        // save new status in db
                        $.getJSON('ajax/todo/edit/is/done', {
                                todoId :  _private.dashboardEntry.changedEntryData.reference.todoId, 
                                isDone :   _private.dashboardEntry.changedEntryData.reference.isDone
                        }, function(data) {
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){		
                                        // save status
                                        _private.dashboardEntry.entryData =  _private.dashboardEntry.changedEntryData;
                                        _private.dashboardEntry.changedEntryData = {};

                                }
                        });
                }
                else {
                                
                }
        };
                
        me.switchToEditMode = function(){
                _private.dashboardEntry.$middleColumn.html('<textarea class="todo-module-text">' + _private.dashboardEntry.entryData.reference.text + '</textarea>');
                var $textarea = _private.dashboardEntry.$middleColumn.find('.todo-module-text');
                $textarea.focus();
        };
                
        me.switchToNormalMode = function(){
                _private.dashboardEntry.$middleColumn.html('<p>' + _private.dashboardEntry.entryData.reference.text.replace(/\n/g,'<br />') + '</p>');
                        
                if( _private.dashboardEntry.entryData.reference.isDone == '1'){
                        _private.dashboardEntry.$entry.find('.dashboard-entry-left-column img').attr('src', Dashbird.baseUrl + '/images/button-todo-done-small.png' );
                }
                else {
                        _private.dashboardEntry.$entry.find('.dashboard-entry-left-column img').attr('src', Dashbird.baseUrl + '/images/button-todo-not-done-small.png' );
                }
                        
        };
          
        me.save = function(){                                
                // find text
                _private.dashboardEntry.changedEntryData.reference.text =  _private.dashboardEntry.$middleColumn.find('.todo-module-text').val();

                // save status in db
                $.getJSON('ajax/todo/edit/', {
                        todoId : _private.dashboardEntry.changedEntryData.reference.todoId, 
                        text : _private.dashboardEntry.changedEntryData.reference.text,
                        tags : _private.dashboardEntry.changedEntryData.tags,
                        isDone : _private.dashboardEntry.changedEntryData.reference.isDone
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                // save status
                                _private.dashboardEntry.entryData =  _private.dashboardEntry.changedEntryData;
                                _private.dashboardEntry.changedEntryData = {};
                                // convert (is converted on serverside aswell)
                                _private.dashboardEntry.entryData.reference.text = Dashbird.Dashboard.htmlEntities(_private.dashboardEntry.entryData.reference.text);
                                
                                // switch to normal mode
                                _private.dashboardEntry.switchToNormalMode();
                        }
                });
           

        };
                
        me.deleteEntry = function(){

                $.getJSON('ajax/todo/delete/', {
                        todoId : _private.dashboardEntry.entryData.reference.todoId
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){

                        }
                });
        };
        
        
        me._private = _private; // for inheritance
        return me;
};