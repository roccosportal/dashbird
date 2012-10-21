<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class Dashboard extends Base {

    public function IndexAction() {
        // output of the basic html
        $this->ExecuteView();
    }

    public function AjaxGetDashboardEntriesAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $Search = $this->Request->GetGET('search');
        $StartPosition = $this->Request->GetGET('start-position');
        if($StartPosition==null) $StartPosition = 0;
        $EntryCount = $this->Request->GetGET('entry-count');
        if($EntryCount==null) $EntryCount = 9999999999;

        $Query = new \Pvik\Database\Generic\Query('DashboardEntries');
        $ConditionString = 'LEFT JOIN EntryShares as EntryShares ON EntryShares.DashboardEntryId = DashboardEntries.DashboardEntryId';
        $ConditionString .= ' WHERE (DashboardEntries.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        $ConditionString .= ' OR EntryShares.UserId = "%s")';
        $Query->AddParameter($this->GetUserId());
        if (!empty($Search)) {
            $SearchWords = preg_split("/[\s]+/", $Search);
            foreach ($SearchWords as $SearchWord) {
                $Query->AddParameter('%' . $SearchWord . '%');
                $ConditionString .= ' AND DashboardEntries.SearchHelper LIKE "%s"';
            }
        }
        $Query->SetConditions($ConditionString);
        $HasMoreEntriesQuery = clone $Query;
        $Query->SetOrderBy('ORDER BY DashboardEntries.Datetime DESC LIMIT %s,%s');
        $Query->AddParameter($StartPosition);
        $Query->AddParameter($EntryCount);
        $DashboardEntries = $Query->Select();
        
     
       
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
        $Data = array();
        $Data['entries'] = array();
        foreach ($DashboardEntries as $DashboardEntry) {
            $Data['entries'][] = $DashboardEntry->ToArray();
        }
        
        
        $HasMoreEntriesQuery->SetOrderBy('ORDER BY DashboardEntries.Datetime DESC LIMIT %s,%s');
        $HasMoreEntriesQuery->AddParameter($StartPosition + $EntryCount);
        $HasMoreEntriesQuery->AddParameter(1);
        $Entry = $HasMoreEntriesQuery->SelectSingle();
        //print_r($Entry);
        $Data['has-more-entries'] = ($Entry!=null);

        
        $this->ResponseSuccess($Data);
    }

    public function AjaxSetEntrySharesAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $EntryId = $this->Request->GetGET('entryId');

        $UserIds = $this->Request->GetGET('userIds');
        if (empty($UserIds)) {
            $UserIds = array();
        }
        if (empty($EntryId) || !is_array($UserIds)) {
            return $this->ResponseWrongData();
        }

        $DashboardEntry = ModelTable::Get('DashboardEntries')->LoadByPrimaryKey($EntryId);
        /* @var $DashboardEntry \Dashbird\Model\Entities\DashboardEntry */
        if ($DashboardEntry != null && !$DashboardEntry->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $DashboardEntry->SetEntryShares($UserIds);

        return $this->ResponseSuccess();
    }

}

?>