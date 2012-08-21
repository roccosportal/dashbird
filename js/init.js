$(document).ready(function (){
    LoginBox.init();
    Dashboard.registerModule('Note', NoteModule);
    Dashboard.registerModule('Link', LinkModule);
    Dashboard.registerModule('Todo', TodoModule);
    Auth.attach('onLoggedIn',  function (){
        Dashboard.init();
    });
    Auth.init();

});