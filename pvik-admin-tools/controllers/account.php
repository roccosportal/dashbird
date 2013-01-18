<?php
namespace PvikAdminTools\Controllers;
/**
 * Logic for login and logout.
 */
class Account extends Base {
    /**
     * The logic for login.
     */
    public function LoginAction(){
        // checks if already logged in or login data send
        // turn off auto redirect
        if($this->CheckPermission(false)){
            // if already logged in redirect to admin root
            $this->RedirectToPath('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url']);
        }
        elseif($this->Request->IsPOST('login')&&$this->Request->IsPOST('username')&&$this->Request->IsPOST('password')){
            // log in data sended but were wrong
             $this->ViewData->Set('Username',$this->Request->GetPOST('username'));
             $this->ViewData->Set('Error', true);
             $this->ExecuteView();
        }
        else {
            // show view to log in
             $this->ExecuteView();
        }
    }
    /**
     * The logic for logout.
     */
    public function LogoutAction(){
        $this->Request->SessionStart();
        unset($_SESSION['AdminPvikToolsLoggedIn']);
        // redirect
        $this->RedirectToPath('~/');
    }
}
