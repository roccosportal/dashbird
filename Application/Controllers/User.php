<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class User extends Base {
    
    public function SettingsAction(){
        if(!$this->IsLoggedIn()){
            return $this->RedirectToPath('/login');
        }
        $this->ViewData->Set('UserData', json_encode($this->GetUser()->ToArray()));
        
        $this->ExecuteView();
    }

    public function ApiUserSharesAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $Name = $this->Request->GetGET('name');
        if (empty($Name)) {
            return $this->ResponseWrongData();
        }
        $UsersModelTable = ModelTable::Get('Users');
        /* @var $UserModelTable \Dashbird\Model\ModelTables\Users */
        $User = $UsersModelTable->FindByName($Name);
        if ($User == null || $User->UserId == $this->GetUserId()) {
            return $this->ResponseWrongData();
        }
        
        if($this->GetUser()->UserShares->HasValue('ConnectedUserId', $User->UserId)){
            return $this->ResponseWrongData();
        }


        $UserShare = new \Dashbird\Model\Entities\UserShare();
        $UserShare->UserId = $this->GetUserId();
        $UserShare->ConnectedUserId = $User->UserId;
        $UserShare->Insert();
        
       

        $Post = new \Dashbird\Model\Entities\Post();
        $Post->Text = 'Hello '.  $User->Name .",\nI started sharing with you!";
        $Post->SearchHelper = '';
        $Post->UserId = $this->GetUserId();
        $Post->Insert();

        $Post->SetSearchHelperPart('text', $Post->Text);
        $Post->SetPostShares(array($User->UserId));
        $Post->Update();
        
      
        
        
        
        return $this->ResponseSuccess(array ('user' => $User->ToArraySimple()));
    }
    
      public function ApiUserChangePasswordAction(){
		if(!$this->IsLoggedIn()){
                    return $this->ResponseNotLoggedIn();
                }
                $OldPassword = $this->Request->GetPOST('old-password');
                $NewPassword = $this->Request->GetPOST('new-password');
                if(empty($NewPassword)){
                    return $this->ResponseWrongData();
                }

               
                $User = $this->GetUser();
                /* @var $User \Dashbird\Model\Entities\User */
                if(!$User){
                    return $this->ResponseWrongData();
                }
                if (crypt($OldPassword, $User->Password) != $User->Password) {
                     return $this->ResponseWrongData();
                }
                
                $Random = md5(uniqid(mt_rand(), true));
                $Salt = '$2a$07$'. $Random .'$';
                $User->Password = crypt($NewPassword, $Salt);
                $User->Update();
                
               
                return $this->ResponseSuccess();
		
			
	
		
	}

}

?>