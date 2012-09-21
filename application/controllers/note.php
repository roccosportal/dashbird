<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Pvik\Database\Generic\ModelTable;
class Note extends Base {

        public function AjaxAddAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));


                        if (!empty($Text)) {
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
                        $NoteId = $this->Request->GetGET('noteId');
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

        public function AjaxEditAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $NoteId = $this->Request->GetGET('noteId');
                        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
                        $Tags = $this->Request->GetGET('tags');
                        $Note = ModelTable::Get('Notes')->LoadByPrimaryKey($NoteId);
                        /* @var $Note NoteModel */
                        if ($Note != null && !empty($Text)) {
                                $Note->Text = $Text;
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