<?php
Core::Depends('~/controllers/base-controller.php');
class AuthController extends BaseController {
	// action
	public function AjaxLogin(){
		$Response = array ();
		if(!$this->IsLoggedIn()){
			$Name = Core::GetGET('name');
			$Password = Core::GetGET('password');

			$Query = new Query('Users');
			$Query->SetConditions('WHERE Users.Name = "%s" AND Users.Password = "%s"');
			$Query->AddParameter($Name);
			$Query->AddParameter(md5($Password));

			$User = $Query->SelectSingle();
			if($User){
				$Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
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



	public function AjaxIsLoggedIn(){
		$Response = array ();
		$Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
		if($this->IsLoggedIn()){
			$Response[AJAX::MESSAGE] = AJAX::IS_LOGGED_IN;
		}
		else {
			$Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
		}
		echo json_encode($Response);
		exit();
	}

	// action
	public function AjaxLogout(){
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