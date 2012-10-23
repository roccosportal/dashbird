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


        $UserShare = new \Dashbird\Model\Entities\UserShare();
        $UserShare->UserId = $this->GetUserId();
        $UserShare->ConnectedUserId = $User->UserId;
        $UserShare->Insert();
        
        $NoteModel = new \Dashbird\Model\Entities\Note();
        $NoteModel->Text = 'Hello '.  $User->Name .",\nI started sharing with you!";
        $NoteModel->UserId = $this->GetUserId();
        $NoteModel->Insert();

        $DashboardEntry = new \Dashbird\Model\Entities\DashboardEntry();
        $DashboardEntry->Module = 'Note';
        $DashboardEntry->ReferenceId = $NoteModel->NoteId;
        $DashboardEntry->SearchHelper = '';
        $DashboardEntry->UserId = $this->GetUserId();
        $DashboardEntry->Insert();

        $DashboardEntry->AddTag('note');
        $DashboardEntry->SetSearchHelpePart('note-text', $NoteModel->Text);
        $DashboardEntry->SetEntryShares(array($User->UserId));
        $DashboardEntry->Update();
        
      
        
        
        
        return $this->ResponseSuccess();
    }

}

?>