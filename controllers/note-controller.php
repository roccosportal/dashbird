<?php

class NoteController extends BaseController {

        public function AjaxAdd() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Text = Core::GetGET('text');


                        if (!empty($Text)) {
                                $NoteModel = new NoteModel();
                                $NoteModel->Text = htmlentities(utf8_decode($Text));
                                $NoteModel->UserId = $this->GetUserId();
                                $NoteModel->Insert();

                                $DashboardEntryModel = new DashboardEntryModel();
                                $DashboardEntryModel->Module = 'Note';
                                $DashboardEntryModel->ReferenceId = $NoteModel->NoteId;
                                $DashboardEntryModel->SearchHelper = '';
                                $DashboardEntryModel->UserId = $this->GetUserId();
                                $DashboardEntryModel->Insert();

                                $DashboardEntryModel->AddTag('note');
                                $DashboardEntryModel->SetSearchHelpePart('note-text', $NoteModel->Text);
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
                        $NoteId = Core::GetGET('noteId');
                        $Note = ModelTable::Get('Notes')->LoadByPrimaryKey($NoteId);
                        /* @var $Note NoteModel */
                        if ($Note != null) {
                                $DashboardEntry = $Note->GetDashboardEntry();
                                if ($DashboardEntry != null) {
                                        $DashboardEntry->Delete();
                                        $Note->Delete();
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

        public function AjaxEdit() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $NoteId = Core::GetGET('noteId');
                        $Text = Core::GetGET('text');
                        $Tags = Core::GetGET('tags');
                        $Note = ModelTable::Get('Notes')->LoadByPrimaryKey($NoteId);
                        /* @var $Note NoteModel */
                        if ($Note != null && !empty($Text)) {
                                $Note->Text = htmlentities(utf8_decode($Text));
                                $Note->Update();
                                $DashboardEntry = $Note->GetDashboardEntry();
                                   if(is_array($Tags)){
                                        $DashboardEntry->SetTags($Tags);
                                }
                                 else {
                                        $DashboardEntry->SetTags(array());
                                }
                                $DashboardEntry->SetSearchHelpePart('note-text', $Note->Text);
                                
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