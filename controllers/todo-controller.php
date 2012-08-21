<?php

class TodoController extends BaseController {

        public function AjaxAdd() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Text = Core::GetGET('text');


                        if (!empty($Text)) {
                                $TodoModel = new TodoModel();
                                $TodoModel->Text = htmlentities(utf8_decode($Text));
                                $TodoModel->IsDone = false;
                                $TodoModel->UserId = $this->GetUserId();
                                $TodoModel->Insert();

                                $DashboardEntryModel = new DashboardEntryModel();
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

        public function AjaxDelete() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = Core::GetGET('todoId');
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

        public function AjaxEditIsDone() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = Core::GetGET('todoId');
                        $IsDone = Core::GetGET('isDone');

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

        public function AjaxEdit() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $TodoId = Core::GetGET('todoId');
                        $Text = Core::GetGET('text');
                        $Tags = Core::GetGET('tags');
                        $IsDone = Core::GetGET('isDone');

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