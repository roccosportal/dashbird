<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Pvik\Database\Generic\ModelTable;
class Todo extends Base {

        public function AjaxAddAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));


                        if (!empty($Text)) {
                                $TodoModel = new \Dashbird\Model\Entities\Todo();
                                $TodoModel->Text = htmlentities(utf8_decode($Text));
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

                                $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                                $Response[AJAX::DATA] = $DashboardEntryModel->ToArray();
                        } else {
                                $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                                $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
                        }
                } else {
                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                        $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
                }
                echo json_encode($Response);
                exit();
        }

        public function AjaxDeleteAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = $this->Request->GetGET('todoId');
                        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
                        /* @var $Todo TodoModel */
                        if ($Todo != null) {
                                $DashboardEntry = $Todo->GetDashboardEntry();
                                if ($DashboardEntry != null) {
                                        $DashboardEntry->Delete();
                                        $Todo->Delete();
                                        $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                                } else {
                                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                                        $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
                                }
                        } else {
                                $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                                $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
                        }
                } else {
                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                        $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
                }
                echo json_encode($Response);
                exit();
        }

        public function AjaxEditIsDoneAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = $this->Request->GetGET('todoId');
                        $IsDone = $this->Request->GetGET('isDone');

                        if ($IsDone == '1') {
                                $IsDone = true;
                        } else {
                                $IsDone = false;
                        }
                        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
                        /* @var $Todo TodoModel */
                        if ($Todo != null) {
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
                                $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                        } else {
                                $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                                $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
                        }
                } else {
                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                        $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
                }
                echo json_encode($Response);
                exit();
        }

        public function AjaxEditAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = $this->Request->GetGET('todoId');
                        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
                        $Tags = $this->Request->GetGET('tags');
                        $IsDone = $this->Request->GetGET('isDone');

                        $Todo = ModelTable::Get('Todos')->LoadByPrimaryKey($TodoId);
                        /* @var $Todo TodoModel */
                        if (!empty($Text) && $Todo != null) {
                                $Todo->Text = htmlentities(utf8_decode($Text));
                                $Todo->IsDone = $IsDone;
                                $Todo->Update();

                                $DashboardEntry = $Todo->GetDashboardEntry();
                                if (is_array($Tags)) {
                                        $DashboardEntry->SetTags($Tags);
                                }
                                 else {
                                        $DashboardEntry->SetTags(array());
                                }
                                $DashboardEntry->SetSearchHelpePart('todo-text', $Todo->Text);
                                $DashboardEntry->Update();
                                $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                        } else {
                                $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                                $Response[AJAX::MESSAGE] = AJAX::WRONG_DATA;
                        }
                } else {
                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                        $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
                }
                echo json_encode($Response);
                exit();
        }

}

?>