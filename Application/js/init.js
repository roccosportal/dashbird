$(document).ready(function (){
    Dashbird.LoginBox.init();
    Dashbird.Templates.init();
    Dashbird.User.attach('onLoggedIn',  function (){
        $('#content').show();
        $('#navbar .nav').show();
        $('#navbar .navbar-search').show();
        Dashbird.Modal.init();
        Dashbird.Settings.init();
        Dashbird.Dashboard.init();
        Dashbird.Search.init();
        Dashbird.Dashboard.loadEntries();
        Dashbird.NewEntry.init();
        Dashbird.PluginManager.init();
       
    });
    Dashbird.User.init();

});