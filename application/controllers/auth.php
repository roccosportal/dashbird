<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Dashbird\Library\Constants\SESSION;
use Pvik\Database\Generic\Query;
class Auth extends Base {
	// action
	public function AjaxLoginAction(){
		$Response = array ();
		if(!$this->IsLoggedIn()){
			$Name = $this->Request->GetGET('name');
			$Password = $this->Request->GetGET('password');

			$Query = new Query('Users');
			$Query->SetConditions('WHERE Users.Name = "%s" AND Users.Password = "%s"');
			$Query->AddParameter($Name);
			$Query->AddParameter(md5($Password));

			$User = $Query->SelectSingle();
			if($User){
				$Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                                $Response[AJAX::DATA] = array ('user' => array ('userId' => $User->UserId));
				$_SESSION[SESSION::LOGGED_IN] = true;
				$_SESSION[SESSION::USER_ID] = $User->UserId;
			}
			else {
				$Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
				$Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
			}
		}
		else {
			// no need to login
			$Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
			$Response[AJAX::MESSAGE] = AJAX::ALREADY_LOGGED_IN;
		}
		echo json_encode($Response);
		exit();
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
}
?>