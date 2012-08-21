<?php

class BaseController extends Controller {

	public function IsLoggedIn(){
		Core::SessionStart();
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