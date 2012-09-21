<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Pvik\Database\Generic\ModelTable;
class Link extends Base {

        public function AjaxAddAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Link = htmlentities(utf8_decode($this->Request->GetGET('link')));

                        $IsImage = $this->Request->GetGET('isImage');

                        // everything else then true is false
                        if ($IsImage == '1') {
                                $IsImage = true;
                        } else {
                                $IsImage = false;
                        }

                        if (!empty($Link)) {
                                $LinkModel = new \Dashbird\Model\Entities\Link();
                                $LinkModel->Link = $Link;
                                $LinkModel->IsImage = $IsImage;
                                $LinkModel->UserId = $this->GetUserId();
                                $LinkModel->Insert();

                                $DashboardEntryModel = new \Dashbird\Model\Entities\DashboardEntry();
                                $DashboardEntryModel->Module = 'Link';
                                $DashboardEntryModel->ReferenceId = $LinkModel->LinkId;
                                $DashboardEntryModel->SearchHelper = '';
                                $DashboardEntryModel->UserId = $this->GetUserId();
                                $DashboardEntryModel->Insert();

                                $DashboardEntryModel->SetSearchHelpePart('link-link', $LinkModel->Link);
                                $DashboardEntryModel->AddTag('link');
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
                        $LinkId = $this->Request->GetGET('linkId');
                        $Link = ModelTable::Get('Links')->LoadByPrimaryKey($LinkId);
                        /* @var $Link LinkModel */
                        if ($Link != null) {
                                $DashboardEntry = $Link->GetDashboardEntry();
                                if ($DashboardEntry != null) {
                                        $DashboardEntry->Delete();
                                        $Link->Delete();
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
                        $LinkId = $this->Request->GetGET('linkId');
                        $Link = htmlentities(utf8_decode($this->Request->GetGET('link')));
                        $Tags = $this->Request->GetGET('tags');
                        $IsImage = $this->Request->GetGET('isImage');

                        // everything else then true is false
                        if ($IsImage == '1') {
                                $IsImage = true;
                        } else {
                                $IsImage = false;
                        }

                        $LinkModel = ModelTable::Get('Links')->LoadByPrimaryKey($LinkId);
                        /* @var $LinkModel LinkModel */
                        if ($LinkModel != null && !empty($Link)) {
                                $LinkModel->Link = $Link;
                                $LinkModel->IsImage = $IsImage;
                                $LinkModel->Update();

                                $DashboardEntry = $LinkModel->GetDashboardEntry();
                                if (is_array($Tags)) {
                                        $DashboardEntry->SetTags($Tags);
                                } else {
                                        $DashboardEntry->SetTags(array());
                                }
                                $DashboardEntry->SetSearchHelpePart('link-link', $LinkModel->Link);
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