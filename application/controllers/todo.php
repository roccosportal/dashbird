<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class Todo extends Base {

    public function AjaxAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));


        if (empty($Text)) {
            return $this->ResponseWrongData();
        }

        $TodoModel = new \Dashbird\Model\Entities\Todo();
        $TodoModel->Text = $Text;
        $TodoModel->IsDone = false;
        $TodoModel->UserId = $this->GetUserId();
        $TodoModel->Insert();

        $DashboardEntryModel = new \Dashbird\Model\Entities\DashboardEntry();
        $DashboardEntryModel->Module = 'Todo';
        $DashboardEntryModel->SearchHelper = '';
        $DashboardEntryModel->ReferenceId = $TodoModel->TodoId;
        $DashboardEntryModel->UserId = $this->GetUserId();
        $DashboardEntryModel->Insert();

        $DashboardEntryModel->SetSearchHelpePart('todo-text', $TodoModel->Text, false);
        $DashboardEntryModel->AddTag('todo');
        $DashboardEntryModel->AddTag('undone');
        // update to set field search helper part
        $DashboardEntryModel->Update();
        return $this->ResponseSuccess($DashboardEntryModel->ToArray());
    }

    public function AjaxDeleteAction() {

        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $TodoId = $this->Request->GetGET('todoId');
        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
        /* @var $Todo \Dashbird\Model\Entities\Todo */
        if ($Todo == null || !$Todo->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $DashboardEntry = $Todo->GetDashboardEntry();
        $DashboardEntry->Delete();
        $Todo->Delete();
        return $this->ResponseSuccess();
    }

    public function AjaxEditIsDoneAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $TodoId = $this->Request->GetGET('todoId');
        $IsDone = $this->Request->GetGET('isDone');

        if ($IsDone == '1') {
            $IsDone = true;
        } else {
            $IsDone = false;
        }
        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
        /* @var $Todo \Dashbird\Model\Entities\Todo */
        if ($Todo == null || !$Todo->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Todo->IsDone = $IsDone;
        $Todo->Update();

        $DashboardEntry = $Todo->GetDashboardEntry();

        $DashboardEntry->SetSearchHelpePart('todo-text', $Todo->Text);
        if ($Todo->IsDone) {
            $DashboardEntry->DeleteTag('undone');
            $DashboardEntry->AddTag('isdone');
        } else {
            $DashboardEntry->DeleteTag('isdone');
            $DashboardEntry->AddTag('undone');
        }


        $DashboardEntry->Update();
        return $this->ResponseSuccess();
    }

    public function AjaxEditAction() {

        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $TodoId = $this->Request->GetGET('todoId');
        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        $Tags = $this->Request->GetGET('tags');
        $IsDone = $this->Request->GetGET('isDone');

        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
        /* @var $Todo \Dashbird\Model\Entities\Todo */
        if (empty($Text) || $Todo == null || !$Todo->GetDashboardEntry()->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $Todo->Text = htmlentities(utf8_decode($Text));
        $Todo->IsDone = $IsDone;
        $Todo->Update();

        $DashboardEntry = $Todo->GetDashboardEntry();
        if (is_array($Tags)) {
            $DashboardEntry->SetTags($Tags);
        } else {
            $DashboardEntry->SetTags(array());
        }
        $DashboardEntry->SetSearchHelpePart('todo-text', $Todo->Text);
        $DashboardEntry->Update();
        return $this->ResponseSuccess();
    }

}

?>