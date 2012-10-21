<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
class Base extends \Pvik\Web\Controller {

	public function IsLoggedIn(){
		return \Dashbird\Library\Services\UserService::Instance()->IsLoggedIn();
	}

	public function GetUserId(){
		return \Dashbird\Library\Services\UserService::Instance()->GetUserId();
	}
        
        /**
         * 
         * @return \Dashbird\Model\Entities\User
         */
        public function GetUser(){
            return \Dashbird\Library\Services\UserService::Instance()->GetUser();
        }
        
        protected function ResponseNotLoggedIn(){
            $Response = array();
            $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
            $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
            echo json_encode($Response);
            return true;
        }
        
        protected function ResponseWrongData(){
            $Response = array();
            $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
            $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
            echo json_encode($Response);
            return true;
        }
        
        protected function ResponseSuccess($Data = null){
            $Response = array();
            $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
            if($Data !== null){
                $Response[AJAX::DATA] = $Data;
            }
            echo json_encode($Response);
            return true;
        }

}