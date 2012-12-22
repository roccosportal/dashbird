<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class User extends Base {

    public function AjaxGetUserSharesAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $Data = $this->GetUser()->UserSharesToArray();
        return $this->ResponseSuccess($Data);
    }

    public function AjaxAddUserShareAction() {
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
        
       

        $Entry = new \Dashbird\Model\Entities\Entry();
        $Entry->Text = 'Hello '.  $User->Name .",\nI started sharing with you!";
        $Entry->SearchHelper = '';
        $Entry->UserId = $this->GetUserId();
        $Entry->Insert();

        $Entry->SetSearchHelperPart('text', $Entry->Text);
        $Entry->SetEntryShares(array($User->UserId));
        $Entry->Update();
        
      
        
        
        
        return $this->ResponseSuccess(array ('user' => $User->ToArraySimple()));
    }
    
      public function AjaxChangePasswordAction(){
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