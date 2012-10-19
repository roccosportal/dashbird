<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class Note extends Base {

    public function AjaxAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        if (empty($Text)) {
            return $this->ResponseWrongData();
        }


        $NoteModel = new \Dashbird\Model\Entities\Note();
        $NoteModel->Text = $Text;
        $NoteModel->UserId = $this->GetUserId();
        $NoteModel->Insert();

        $DashboardEntryModel = new \Dashbird\Model\Entities\DashboardEntry();
        $DashboardEntryModel->Module = 'Note';
        $DashboardEntryModel->ReferenceId = $NoteModel->NoteId;
        $DashboardEntryModel->SearchHelper = '';
        $DashboardEntryModel->UserId = $this->GetUserId();
        $DashboardEntryModel->Insert();

        $DashboardEntryModel->AddTag('note');
        $DashboardEntryModel->SetSearchHelpePart('note-text', $NoteModel->Text);
        $DashboardEntryModel->Update();


        return $this->ResponseSuccess($DashboardEntryModel->ToArray());
    }

    public function AjaxDeleteAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $NoteId = $this->Request->GetGET('noteId');
        if (!is_numeric($NoteId)) {
            return $this->ResponseWrongData();
        }
        $Note = ModelTable::Get('Notes')->LoadByPrimaryKey($NoteId);
        /* @var $Note \Dashbird\Model\Entities\Note */
        if ($Note == null) {
            return $this->ResponseWrongData();
        }
        $DashboardEntry = $Note->GetDashboardEntry();
        if ($DashboardEntry == null || !$DashboardEntry->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        return $this->ResponseSuccess();
    }

    public function AjaxEditAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $NoteId = $this->Request->GetGET('noteId');
        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        $Tags = $this->Request->GetGET('tags');
        $Note = ModelTable::Get('Notes')->LoadByPrimaryKey($NoteId);
        /* @var $Note \Dashbird\Model\Entities\Note */
        if ($Note == null || empty($Text) || !$Note->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Note->Text = $Text;
        $Note->Update();
        $DashboardEntry = $Note->GetDashboardEntry();
        if (is_array($Tags)) {
            $DashboardEntry->SetTags($Tags);
        } else {
            $DashboardEntry->SetTags(array());
        }
        $DashboardEntry->SetSearchHelpePart('note-text', $Note->Text);

        $DashboardEntry->Update();
        return $this->ResponseSuccess();
    }

}

?>