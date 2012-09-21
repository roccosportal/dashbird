<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\SESSION;
class Base extends \Pvik\Web\Controller {

	public function IsLoggedIn(){
		$this->Request->SessionStart();
		if(isset($_SESSION[SESSION::LOGGED_IN]) && $_SESSION[SESSION::LOGGED_IN]===true){
			return true;
		}
		return false;
	}

	public function GetUserId(){
		if($_SESSION[SESSION::LOGGED_IN]===true){
			return $_SESSION[SESSION::USER_ID];
		}
		return null;
	}

}