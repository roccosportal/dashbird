$(document).ready(function (){
    Dashbird.LoginBox.init();
    Dashbird.Templates.init();
    Dashbird.User.attach('onLoggedIn',  function (){
        $('#content').show();
        $('#navbar .nav').show();
        $('#navbar .navbar-search').show();
        Dashbird.Modal.init();
        Dashbird.Settings.init();
        Dashbird.Board.init();
        Dashbird.Search.init();
        Dashbird.Board.loadPosts();
        Dashbird.NewPost.init();
        Dashbird.PluginManager.init();
       
    });
    Dashbird.User.init();

});