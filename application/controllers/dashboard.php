<?php
namespace Dashbird\Controllers;
use Dashbird\Library\Constants\AJAX;
use Pvik\Database\Generic\ModelTable;
class Dashboard extends Base {

        public function IndexAction() {
                // output of the basic html
                $this->ExecuteView();
        }

        public function AjaxGetDashboardEntriesAction() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Search = $this->Request->GetGET('search');

                        $Query = new \Pvik\Database\Generic\Query('DashboardEntries');
                        if (empty($Search)) {
                                $Query->SetConditions('WHERE DashboardEntries.UserId = "%s"');
                                $Query->AddParameter($this->GetUserId());
                        } else {                                
                                $SearchWords = preg_split("/[\s]+/", $Search);
                                $Query->AddParameter($this->GetUserId());
                                $ConditionString = 'WHERE DashboardEntries.UserId = "%s"';
                                foreach ($SearchWords as $SearchWord) {
                                        $Query->AddParameter('%' . $SearchWord . '%');
                                        $ConditionString .= ' AND DashboardEntries.SearchHelper LIKE "%s"';
                                }
                                $Query->SetConditions($ConditionString);
                        }

                        $Query->SetOrderBy('ORDER BY DashboardEntries.Date DESC');
                        $DashboardEntries = $Query->Select();
                        $Response[AJAX::DATA] = array();
                        // optimization for tags
                        $DashboardEntries->LoadList('DashboardEntriesTags->Tag');
                        // optimization for todo
                        $TodoKeys = array();
                        foreach ($DashboardEntries as $DashboardEntry) {
                                if ($DashboardEntry->Module == 'Todo') {
                                        $TodoKeys[] = $DashboardEntry->ReferenceId;
                                }
                        }
                        ModelTable::Get('Todos')->LoadByPrimaryKeys($TodoKeys);

                        // optimization for links
                        $Keys = array();
                        foreach ($DashboardEntries as $DashboardEntry) {
                                if ($DashboardEntry->Module == 'Link') {
                                        $Keys[] = $DashboardEntry->ReferenceId;
                                }
                        }
                        ModelTable::Get('Links')->LoadByPrimaryKeys($Keys);
                                                // optimization for links
                        $Keys = array();
                        foreach ($DashboardEntries as $DashboardEntry) {
                                if ($DashboardEntry->Module == 'Note') {
                                        $Keys[] = $DashboardEntry->ReferenceId;
                                }
                        }
                        ModelTable::Get('Notes')->LoadByPrimaryKeys($Keys);
                        
                        foreach ($DashboardEntries as $DashboardEntry) {
                                $Response[AJAX::DATA][] = $DashboardEntry->ToArray();
                        }
                        $Response[AJAX::STATUS] = AJAX::STATUS_SUCCESS;
                } else {
                        $Response[AJAX::STATUS] = AJAX::STATUS_ERROR;
                        $Response[AJAX::MESSAGE] = AJAX::IS_NOT_LOGGED_IN;
                }
                echo json_encode($Response);
                exit();
        }

}

?>