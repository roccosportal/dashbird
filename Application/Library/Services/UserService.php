<?php
namespace Dashbird\Library\Services;
use Dashbird\Library\Constants\SESSION;
class UserService {

    /**
     *
     * @var UserService 
     */
    protected static $Instance = null;

    /**
     * 
     * @return UserService
     */
    public static function Instance() {
        if (self::$Instance == null) {
            self::$Instance = new \Dashbird\Library\Services\UserService();
        }
        return self::$Instance;
    }

    protected function __construct() {
        
    }

    public function IsLoggedIn() {
        // TODO: quick fix
        if(session_id() == '') {
            // session isn't started
            session_start();
        }
        if (isset($_SESSION[SESSION::LOGGED_IN]) && $_SESSION[SESSION::LOGGED_IN] === true) {
            return true;
        }
        return false;
    }

    public function GetUserId() {
        if ($_SESSION[SESSION::LOGGED_IN] === true) {
            return $_SESSION[SESSION::USER_ID];
        }
        return null;
    }

    /**
     * 
     * @return \Dashbird\Model\Entities\User
     */
    public function GetUser() {
        return \Pvik\Database\Generic\ModelTable::Get('Users')->LoadByPrimaryKey($this->GetUserId());
    }

}