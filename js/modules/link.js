if(Dashbird===undefined){
        var Dashbird = {};
}
if(Dashbird.Modules===undefined){
        Dashbird.Modules = {};
}
Dashbird.Modules.Link = {};
Dashbird.Modules.Link.Module = function(){
        var me = {},
        _private = {};
        _private.$addForm = null;
        _private.$addFormLink = null;
        _private.$addFormIsImage = null;
        me.init = function(){
                _private.$addForm = $('#link-module-add-form');
                _private.$addFormLink = $('#link-module-add-form-link');
                _private.$addFormIsImage = $('#link-module-add-form-is-image');
        };
        me.newEntry = function(){
                me.addLink(_private.$addFormLink.val(), _private.$addFormIsImage.prop('checked'));
                Dashbird.Dashboard.hideCommandForms();
        };
        me.addLink = function (link, isImage){
                $.getJSON('ajax/link/add/', {
                        link : link, 
                        isImage : isImage ? '1' : '0'
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                Dashbird.Dashboard.addToTop(data[AJAX.DATA]);		
                        }
                });
        };
                
        me.createDashboardEntry = function(entry){
                var linkEntry = Dashbird.Modules.Link.Entry();
                linkEntry.init(entry);
                return linkEntry.create();
        };
        
        me.showCommandForm = function(){
                _private.$addForm.show();
                _private.$addFormLink.focus();
        };

        me._private = _private; // for inheritance
        return me;
}();




Dashbird.Modules.Link.Entry = function(){
        var me = {},
        _private = {};
        _private.dashboardEntry = null;
        me.init = function(entry){
                _private.dashboardEntry = Dashbird.DashboardEntry();
                _private.dashboardEntry.init(entry, this);
        };
        me.create = function(){
                var htmlConfig = {};
                htmlConfig.cssClass = 'link-module';
                htmlConfig.leftColumn = '<img src="' + Dashbird.baseUrl + '/images/button-link-small.png" alt="" />';
                if(_private.dashboardEntry.entryData.reference.isImage!=='1'){
                        htmlConfig.middleColumn = '<a href="' + _private.dashboardEntry.entryData.reference.link +'">' + _private.dashboardEntry.entryData.reference.link +'</a>';
                }
                else {
                        htmlConfig.middleColumn = '<img src="' + _private.dashboardEntry.entryData.reference.link + '" />';    
                }
                  
                return _private.dashboardEntry.create(htmlConfig);
        };
                
        me.switchToEditMode = function(){
                var checked = _private.dashboardEntry.entryData.reference.isImage == '1' ? 'checked="checked"' : '';
                _private.dashboardEntry.$middleColumn.html(
                        '<input type="text" class="link-module-link" value="' +  _private.dashboardEntry.entryData.reference.link + '" />'
                        + 'isImage<input type="checkbox" class="link-module-is-image" ' + checked + ' />'
                        );
                _private.dashboardEntry.$middleColumn.find('.link-module-link').focus();
        };
                
        me.switchToNormalMode = function(){
                if(_private.dashboardEntry.entryData.reference.isImage!=='1'){
                        _private.dashboardEntry.$middleColumn.html('<a href="' + _private.dashboardEntry.entryData.reference.link +'">' + _private.dashboardEntry.entryData.reference.link +'</a>');
                }
                else {
                        _private.dashboardEntry.$middleColumn.html('<img src="' + _private.dashboardEntry.entryData.reference.link + '" />');    
                }
        };
          
        me.save = function(){                                
                _private.dashboardEntry.changedEntryData.reference.link =  _private.dashboardEntry.$middleColumn.find('.link-module-link').val();
                _private.dashboardEntry.changedEntryData.reference.isImage =  _private.dashboardEntry.$middleColumn.find('.link-module-is-image').prop('checked') ? '1' : '0';

                // save status in db
                $.getJSON('ajax/link/edit/', {
                        linkId : _private.dashboardEntry.changedEntryData.reference.linkId, 
                        link : _private.dashboardEntry.changedEntryData.reference.link, 
                        isImage : _private.dashboardEntry.changedEntryData.reference.isImage,
                        tags : _private.dashboardEntry.changedEntryData.tags
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                // save status
                                _private.dashboardEntry.entryData =  _private.dashboardEntry.changedEntryData;
                                // convert (is converted on serverside aswell)
                                _private.dashboardEntry.entryData.reference.link = Dashbird.Dashboard.htmlEntities(_private.dashboardEntry.entryData.reference.link);
                                
                                _private.dashboardEntry.changedEntryData = {};
                                               
                                // switch to normal mode
                                _private.dashboardEntry.switchToNormalMode();
                        }
                });
                      
        };
                
        me.deleteEntry = function(){
                $.getJSON('ajax/link/delete/', {
                        linkId : _private.dashboardEntry.entryData.reference.linkId
                }, function(data) {
                        if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
	
                        }
                });
        };
        me._private = _private; // for inheritance
        return me;
};