$(document).ready(function (){
    Dashbird.LoginBox.init();
    Dashbird.Dashboard.registerModule('Note', Dashbird.Modules.Note.Module);
    Dashbird.Dashboard.registerModule('Link', Dashbird.Modules.Link.Module);
    Dashbird.Dashboard.registerModule('Todo', Dashbird.Modules.Todo.Module);
    Dashbird.Auth.attach('onLoggedIn',  function (){
        Dashbird.UserShares.init();
        Dashbird.Dashboard.init();
    });
    Dashbird.Auth.init();

});