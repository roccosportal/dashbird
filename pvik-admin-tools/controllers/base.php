<?php
namespace PvikAdminTools\Controllers;
use \Pvik\Web\Request;
/**
 * Basic functions for a controller in the PvikAdminTools project.
 */
abstract class Base extends \Pvik\Web\Controller{
    
    /**
     * 
     * @param \Pvik\Web\Request $Request
     * @param string $ControllerName
     */
    public function __construct(Request $Request, $ControllerName) {
        parent::__construct($Request, $ControllerName);
        // strip out  \PvikAdminTools\Controllers\
        $this->ControllerName = str_replace('\\PvikAdminTools\\Controllers\\', '', $ControllerName);
    }
    /**
     * Checks if the user is logged in.
     * @return bool 
     */
    protected function LoggedIn(){
        $this->Request->SessionStart();
        // logged in
        if(isset($_SESSION['AdminPvikToolsLoggedIn']) && $_SESSION['AdminPvikToolsLoggedIn']  == true){
            return true;
        }
        // check if login data send
        return $this->CheckLoginData();
    }
    
    /**
     * Checks if the login data send and log user in.
     * @return bool 
     */
    protected function CheckLoginData(){
       if($this->Request->IsPOST('login')&&$this->Request->IsPOST('password')){
            // check log in data
            $LoginData = \Pvik\Core\Config::$Config['PvikAdminTools']['Login'];
            if($this->Request->GetPOST('username')==$LoginData['Username']&&md5($this->Request->GetPOST('password'))==$LoginData['PasswordMD5']){
                // log in data correct
                // log in
                $_SESSION['AdminPvikToolsLoggedIn'] = true;
                return true;
            }
            else {
                // log in data false
                return false;
            }
        }
    }
    
    /**
     * Checks if the user have permission and redirects you to the login page.
     * @param bool $AutoRedirect if off the user doesn't get redirect to the log in page.
     * @return bool 
     */
    protected function CheckPermission($AutoRedirect = true){
        if(!$this->LoggedIn()){
                if($AutoRedirect){
                    // call action login
                    $this->RedirectToController('\\PvikAdminTools\\Controllers\\Account', 'Login');
                }
                return false;
        }
        return true;
    }
    
    /**
     * Searches a view in the PvikAdminTools views folder and executes it.
     * @param string $Folder 
     */
    protected function ExecuteView($Folder = '') {
        if($Folder == ''){
            $Folder = \Pvik\Core\Config::$Config['PvikAdminTools']['BasePath']. 'views/';
        }
        parent::ExecuteView($Folder);
    }
    
    /**
     * Searches a view in the PvikAdminTools view folder and executes it.
     * @param string $ActionName
     * @param string $Folder 
     */
    protected function ExecuteViewByAction($ActionName, $Folder = '') {
        if($Folder == ''){
            $Folder = \Pvik\Core\Config::$Config['PvikAdminTools']['BasePath']. 'views/';
        }
        parent::ExecuteViewByAction($ActionName, $Folder);
    }
    
    /**
     * Returns an array of a url parameter that is build like /param:param2:param3/
     * @param string $ParameterName
     * @return array 
     */
    protected function GetParameters($ParameterName){
        if($this->Request->GetParameters()->ContainsKey($ParameterName)){
            return preg_split('/:/' ,$this->Request->GetParameters()->Get($ParameterName));
        }
        return null;
    }

}
