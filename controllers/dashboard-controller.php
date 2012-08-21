<?php

Core::Depends('~/controllers/base-controller.php');

class DashboardController extends BaseController {

        public function Index() {
                // output of the basic html
                $this->ExecuteView();
        }

        public function AjaxGetDashboardEntries() {
                $Response = array();
                if ($this->IsLoggedIn()) {
                        $Search = Core::GetGET('search');

                        $Query = new Query('DashboardEntries');
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
                        $LinksKeys = array();
                        foreach ($DashboardEntries as $DashboardEntry) {
                                if ($DashboardEntry->Module == 'Link') {
                                        $LinksKeys[] = $DashboardEntry->ReferenceId;
                                }
                        }
                        ModelTable::Get('Links')->LoadByPrimaryKeys($LinksKeys);
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