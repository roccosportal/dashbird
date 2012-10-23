<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Dashbird\Library\Constants\SESSION;
use Pvik\Database\Generic\Query;
class Auth extends Base {
	// action
	public function AjaxLoginAction(){
		if(!$this->IsLoggedIn()){
			$Name = $this->Request->GetGET('name');
			$Password = $this->Request->GetGET('password');

			$Query = new Query('Users');
			$Query->SetConditions('WHERE Users.Name = "%s"');
			$Query->AddParameter($Name);
			//$Query->AddParameter(crypt($Password, \Pvik\Core\Config::$Config['Salt']));

			$User = $Query->SelectSingle();
                        /* @var $User \Dashbird\Model\Entities\User */
			if(!$User){
                            return $this->ResponseWrongData();
                        }
                        if (crypt($Password, $User->Password) != $User->Password) {
                             return $this->ResponseWrongData();
                        }

                        $_SESSION[SESSION::LOGGED_IN] = true;
                        $_SESSION[SESSION::USER_ID] = $User->UserId;
                        return $this->ResponseSuccess(array ('user' => array ('userId' => $User->UserId)));
		
			
		}
		else {
			return $this->ResponseAlreadyLoggedIn();
		}
		
	}



	public function AjaxIsLoggedInAction(){
		$Response = array ();
		$Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
		if($this->IsLoggedIn()){
			$Response[AJAX::MESSAGE] = AJAX::IS_LOGGED_IN;
                        $Response[AJAX::DATA] = array ('user' => array ('userId' => $this->GetUserId()));
		}
		else {
			$Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
		}
		echo json_encode($Response);
		exit();
	}

	// action
	public function AjaxLogoutAction(){
		$Response = array ();
		if($this->IsLoggedIn()){
			$_SESSION[SESSION::LOGGED_IN] = false;
			$Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
		}
		else {
			$Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
			$Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
		}
		echo json_encode($Response);
		exit();
	}
        
        protected function ResponseAlreadyLoggedIn(){
            $Response = array();
            $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
            $Response[AJAX::MESSAGE] = AJAX::ALREADY_LOGGED_IN;
            echo json_encode($Response);
            return true;
        }
}
?>