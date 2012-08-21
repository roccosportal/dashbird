if(Dashbird===undefined){
        var Dashbird = {};
}
Dashbird.Dashboard = function(){
        var me = {},
        _private = {};
        _private.modules = [];
        _private.$commandBar = null;
        _private.$content = null;
        _private.$searchox = null;
        _private.refreshAjaxRequestQueue = null;
        _private.searchRequestQueue = null;
        _private.boardKeyCapture = false;
        _private.boardBarKeyCapture = false;
        
        _private.bindBoardKeys = function(){
                
                
                $('#board').hover(function(){
                        _private.boardKeyCapture = true; 
                },
                function (){
                        _private.boardKeyCapture = false; 
                });
                // on key down in board-bar
                $(document).bind("keydown", function(event){
                        if(_private.boardBarKeyCapture){
                                if(event.ctrlKey == true && event.keyCode == 83){ // CTRL + S
                                        if($('.command-bar-option.selected').length>0){ // adding a new entry
                                                var module = me.findModule($('.command-bar-option.selected:first').data('module'));
                                                module.newEntry();
                                        }
                                        event.preventDefault();
                                }
                                else if(event.ctrlKey == true && event.altKey == true){  // CTRL + ALT
                                        if($('.command-bar-option.selected').length>0){
                                                var next = $('.command-bar-option.selected').next('.command-bar-option');
                                                if(next.length != 0){
                                                        next.click();
                                                }
                                                else {
                                                        $('.command-bar-option.selected').click();
                                                }
                                        }
                                        else {
                                                $('#command-bar .command-bar-option:first').click()
                                        }
                                        event.preventDefault();
                                }
                        }
           
                });
    
        };
        
        _private.bindBoardBarKeys = function(){
                $('#board-bar').hover(function(){
                        _private.boardBarKeyCapture = true; 
                },
                function (){
                        _private.boardBarKeyCapture = false; 
                });
               
               
                // on key down in board
                $(document).bind("keydown", function(event){
                        if(_private.boardKeyCapture){
                                if(event.ctrlKey == true && event.keyCode == 69){ // CTRL + E
                                        if(me.$selectedEntry!=null){
                                                me.$selectedEntry.data('dashboardEntry').toggleMode();
                                        }
                                        event.preventDefault();
                                }
                                else if(event.ctrlKey == true && event.keyCode == 83){ // CTRL + S
                                        if(me.$selectedEntry!=null){
                                                me.$selectedEntry.data('dashboardEntry').save();
                                        }
                                        event.preventDefault();
                                }
                                else if(event.ctrlKey == true && event.keyCode == 68){  // CTRL + D
                                        event.preventDefault();
                                        if(me.$selectedEntry!=null){
                                                var next = [];
                                                me.$selectedEntry.data('dashboardEntry').deleteEntry({
                                                        beforeDetach :  function(){ // before detach
                                                                if(me.$selectedEntry!=null){
                                                                        next = me.$selectedEntry.next();
                                                                }
                                                        },
                                                        afterDetach :  function(){ // after detach
                                                                if(next.length != 0){
                                                                        // select next one
                                                                        me.$selectedEntry = next;
                                                                        me.$selectedEntry.addClass('selected');
                                                                        me.$selectedEntry.focus();
                                                                }
                                                                else {
                                                                        me.$selectedEntry = null;
                                                                }
                                                        }
                                                });
                                        }
                                }
                        }
                });
                
        }
        
        
        
        me.$selectedEntry = null;
        me.findModule = function(module){
                for (var i = 0; i < _private.modules.length; i++) {
                        if(_private.modules[i].name === module){
                                return _private.modules[i].module;
                        }
                }
                return null;
        };
        me.registerModule = function(name, module){
                _private.modules.push({
                        name: name, 
                        module : module
                });
                module.init();
        };

        me.init = function (){
               

                _private.$commandBar = $('#command-bar');
                _private.$commandBar.show();
                _private.$content = $('#content');
                _private.$searchBox = $('#search-box');
            
                _private.searchRequestQueue = SimpleJSLib.SingleRequestQueue();
                _private.searchRequestQueue.setTimeout(200);
                _private.$searchBox.keyup(function(){
                        _private.searchRequestQueue.addToQueue({}, function(data){
                                me.refresh();
                        });
                });
            
                // open command-bar
                $('#command-bar-bottom > .command-bar-option').click(function (){
                        if($(this).data('module')!== null){
                                if(!$(this).hasClass('selected')){
                                        me.hideCommandForms();
                                        $('#command-bar-top').removeClass('closed');
                                        $(this).addClass('selected');
                                        me.findModule($(this).data('module')).showCommandForm();
                                }
                                else {
                                        me.hideCommandForms();
                                }
                  
                        } 
                });
                
                _private.bindBoardKeys();
                _private.bindBoardBarKeys();
            
            
            
               
                        
                $('#board').show();
                $('#board-bar').show();
                        
            
                _private.refreshAjaxRequestQueue = SimpleJSLib.SingleRequestQueue();
                me.refresh();
                        
                $('#logout').click(function(event){
                        $.getJSON('ajax/logout/',{}, function(data) {
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                        // refresh page
                                        window.location.href = window.location.href;
                                }
                        });	
                        event.preventDefault();
                });
        };
                
        me.hideCommandForms = function(){
                $('#command-bar-top > .command-form').hide();
                $('#command-bar-bottom > .command-bar-option.selected').removeClass('selected');
                $('#command-bar-top').addClass('closed');
        };
        me.addToTop = function(entry){
                var module = this.findModule(entry.module);
                if(module!==null){
                        var $entry = module.createDashboardEntry(entry);
                                
                        var first = $('.dashboard-entry:first');
                        if(first.length != 0){
                                first.before($entry);
                        }
                        else {
                                _private.$content.append($entry);
                        }
                }
        };

        me.refresh = function(){
                        
                var request = _private.refreshAjaxRequestQueue.runAsynchronRequest();
                $.getJSON('ajax/get/dashboard/entries/', {
                        search : _private.$searchBox.val()
                }, function(data) {
                        if(request.isLatestRequest()){
                                if(data[AJAX.STATUS] === AJAX.STATUS_SUCCESS){
                                        _private.$content.html('');
                                        for (var i = 0; i <  data[AJAX.DATA].length; i++) {
                                                var entry = data[AJAX.DATA][i];
                                                var module = me.findModule(entry.module);
                                                if(module!==null){
                                                        var $entry = module.createDashboardEntry(entry);
                                                        _private.$content.append($entry);
                                                }
                                        }
                                }
                        }
                });		
        };
        me._private = _private; // for inheritance
        return me;
}();