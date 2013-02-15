<?php

namespace Dashbird\Controllers;

use Dashbird\Library\Constants\AJAX;
use Dashbird\Library\Constants\SESSION;
use Pvik\Database\Generic\Query;

class Auth extends Base {

    public function LoginAction() {
        if ($this->IsLoggedIn()) {
            return $this->RedirectToPath('~/');
        }
        $Username = $this->Request->GetPOST('username');
        $Password = $this->Request->GetPOST('password');

        $ValidationState = new \Pvik\Utils\ValidationState();
        if ($Username != null && $Password != null) {

            $Query = new Query('Users');
            $Query->SetConditions('WHERE Users.Name = "%s"');
            $Query->AddParameter($Username);
            $User = $Query->SelectSingle();
            /* @var $User \Dashbird\Model\Entities\User */
            if ($User != null && crypt($Password, $User->Password) == $User->Password) {

                $_SESSION[SESSION::LOGGED_IN] = true;
                $_SESSION[SESSION::USER_ID] = $User->UserId;

                return $this->RedirectToPath('~/');
            }
            $ValidationState->SetError('nameandpassword', '');
        }
        $this->ViewData->Set('ValidationState', $ValidationState);
        $this->ViewData->Set('Username', $Username);



        $this->ExecuteView();
    }
    
    public function LogoutAction(){
         // TODO: quick fix
        if(session_id() == '') {
            // session isn't started
            session_start();
        }
        
        $_SESSION[SESSION::LOGGED_IN] = false;
        return $this->RedirectToPath('~/login');       
    }

    // action
    public function ApiAuthLoginAction() {
        if (!$this->IsLoggedIn()) {
            $Name = $this->Request->GetPOST('name');
            $Password = $this->Request->GetPOST('password');

            $Query = new Query('Users');
            $Query->SetConditions('WHERE Users.Name = "%s"');
            $Query->AddParameter($Name);
            $User = $Query->SelectSingle();
            /* @var $User \Dashbird\Model\Entities\User */
            if (!$User) {
                return $this->ResponseWrongData();
            }
            if (crypt($Password, $User->Password) != $User->Password) {
                return $this->ResponseWrongData();
            }

            $_SESSION[SESSION::LOGGED_IN] = true;
            $_SESSION[SESSION::USER_ID] = $User->UserId;
            return $this->ResponseSuccess(array('user' => $this->GetUser()->ToArray()));
        } else {
            return $this->ResponseAlreadyLoggedIn();
        }
    }

    public function ApiAuthIsLoggedInAction() {
        header('Content-type: application/json');
        $Response = array();
        $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
        if ($this->IsLoggedIn()) {
            $Response[AJAX::MESSAGE] = AJAX::IS_LOGGED_IN;
            $Response[AJAX::DATA] = array('user' => $this->GetUser()->ToArray());
        } else {
            $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
        }
        echo json_encode($Response);
        exit();
    }

    // action
    public function ApiAuthLogoutAction() {
        $Response = array();
        header('Content-type: application/json');
        if ($this->IsLoggedIn()) {
            $_SESSION[SESSION::LOGGED_IN] = false;
            $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
        } else {
            $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
            $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
        }
        echo json_encode($Response);
        exit();
    }

    protected function ResponseAlreadyLoggedIn() {
        header('Content-type: application/json');
        $Response = array();
        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
        $Response[AJAX::MESSAGE] = AJAX::ALREADY_LOGGED_IN;
        echo json_encode($Response);
        return true;
    }

}
