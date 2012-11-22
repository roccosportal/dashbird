$(document).ready(function (){
    Dashbird.LoginBox.init();
    Dashbird.Auth.attach('onLoggedIn',  function (){
        Dashbird.UserShares.init();
        Dashbird.Dashboard.init();
    });
    Dashbird.Auth.init();

});