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

        $Query = new \Pvik\Database\Generic\Query('Entries');
        $ConditionString = 'LEFT JOIN EntryShares as EntryShares ON EntryShares.EntryId = Entries.EntryId';
        $ConditionString .= ' WHERE (Entries.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        $ConditionString .= ' OR EntryShares.UserId = "%s")';
        $Query->AddParameter($this->GetUserId());
        if (!empty($Search)) {
            $SearchWords = preg_split("/[\s]+/", $Search);
            foreach ($SearchWords as $SearchWord) {
                $Query->AddParameter('%' . $SearchWord . '%');
                $ConditionString .= ' AND Entries.SearchHelper LIKE "%s"';
            }
        }
        $Query->SetConditions($ConditionString);
        $HasMoreEntriesQuery = clone $Query;
        $Query->SetOrderBy('ORDER BY Entries.Datetime DESC LIMIT %s,%s');
        $Query->AddParameter($StartPosition);
        $Query->AddParameter($EntryCount);
        $Entries = $Query->Select();
        
     
       
        // optimization for tags
        $Entries->LoadList('EntriesTags->Tag');
      
        $Data = array();
        $Data['entries'] = array();
        foreach ($Entries as $Entry) {
            $Data['entries'][] = $Entry->ToArray();
        }
        
        
        $HasMoreEntriesQuery->SetOrderBy('ORDER BY Entries.Datetime DESC LIMIT %s,%s');
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

        $Entry = ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
        /* @var $DashboardEntry \Dashbird\Model\Entities\Entry */
        if ($Entry != null && !$Entry->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $Entry->SetEntryShares($UserIds);

        return $this->ResponseSuccess();
    }

    
   public function AjaxAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        if (empty($Text)) {
            return $this->ResponseWrongData();
        }


        $EntryModel = new \Dashbird\Model\Entities\Entry();
        $EntryModel->Text = $Text;
        $EntryModel->SearchHelper = '';
        $EntryModel->UserId = $this->GetUserId();
        $EntryModel->Insert();
        $EntryModel->SetSearchHelperPart('text',  $EntryModel->Text);
        $EntryModel->SetSearchHelperPart('user-name',  $this->GetUser()->Name);
        $EntryModel->Update();


        return $this->ResponseSuccess($EntryModel->ToArray());
    }

    public function AjaxDeleteAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $EntryId = $this->Request->GetGET('entryId');
        if (!is_numeric($EntryId)) {
            return $this->ResponseWrongData();
        }
        $Entry = ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
        /* @var $Entry \Dashbird\Model\Entities\Entry */
        if ($Entry == null) {
            return $this->ResponseWrongData();
        }
        if (!$Entry->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Entry->Delete();
        return $this->ResponseSuccess();
    }

    public function AjaxEditAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $EntryId = $this->Request->GetGET('entryId');
        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        $Tags = $this->Request->GetGET('tags');
        $Entry = ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
        /* @var $Entry \Dashbird\Model\Entities\Entry */
        if ($Entry == null || empty($Text) || !$Entry->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Entry->Text = $Text;
        $Entry->Update();
    
        if (is_array($Tags)) {
            $Entry->SetTags($Tags);
        } else {
            $Entry->SetTags(array());
        }
        $Entry->SetSearchHelperPart('text', $Entry->Text);

        $Entry->Update();
        return $this->ResponseSuccess();
    }
}

?>