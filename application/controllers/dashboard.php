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
    
    public function AjaxGetHashesAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

       
        $Query = new \Pvik\Database\Generic\Query('Entries');
        $ConditionString = 'LEFT JOIN EntryShares as EntryShares ON EntryShares.EntryId = Entries.EntryId';
        $ConditionString .= ' WHERE (Entries.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        $ConditionString .= ' OR EntryShares.UserId = "%s")';
        $Query->AddParameter($this->GetUserId());
      
        $Query->SetConditions($ConditionString);
        $Entries = $Query->Select();
        
     
       
        // optimization for tags
        $Entries->LoadList('EntriesTags->Tag');
      
        $Data = array();
        $Data['hashes'] = array();
        foreach ($Entries as $Entry) {
            $Data['hashes'][] = array ('entryId' => $Entry->EntryId,
                    'hash' => $Entry->GetHash());
        }
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
        /* @var $Entry \Dashbird\Model\Entities\Entry */
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


        $Entry = new \Dashbird\Model\Entities\Entry();
        $Entry->Text = $Text;
        $Entry->SearchHelper = '';
        $Entry->UserId = $this->GetUserId();
        
        $Entry->Insert();
        $Entry->SetSearchHelperPart('text',  $Entry->Text);
        $Entry->SetSearchHelperPart('user-name',  $this->GetUser()->Name);
         $Tags = $this->Request->GetGET('tags');
        if (is_array($Tags)) {
            $Entry->SetTags($Tags);
        } else {
            $Entry->SetTags(array());
        }
        $Entry->Update();
        
        $Shares = $this->Request->GetGET('shares');
       
        if (is_array($Shares)) {
            $Entry->SetEntryShares($Shares);
        }
        
       
       


        return $this->ResponseSuccess($Entry->ToArray());
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
        return $this->ResponseSuccess($Entry->ToArray());
    }
    
    public function AjaxGetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
        $EntryId = $this->Request->GetGET('entryId');
        $Entry = ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
         /* @var $Entry \Dashbird\Model\Entities\Entry */
        if($Entry==null||!$Entry->CurrentUserIsAllowedToSee()){
            return $this->ResponseWrongData();
        }
        return $this->ResponseSuccess($Entry->ToArray());
    }
    
    public function AjaxGetMultipleAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $EntryIds = $this->Request->GetGET('entryIds');
        if(!is_array($EntryIds)){
              return $this->ResponseWrongData();
        }
        $Entries = ModelTable::Get('Entries')->LoadByPrimaryKeys($EntryIds);
        
        if($Entries==null){
            return $this->ResponseWrongData();
        }
        /* @var $Entry \Dashbird\Model\Entities\Entry */
        $Array = array();
        foreach($Entries as $Entry){
            if($Entry->CurrentUserIsAllowedToSee()){
                $Array[] = $Entry->ToArray();
            }
        }
        
        return $this->ResponseSuccess($Array);
    }
    
    
    
     public function AjaxGetHashAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
        $EntryId = $this->Request->GetGET('entryId');
        $Entry = ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
         /* @var $Entry \Dashbird\Model\Entities\Entry */
        if($Entry==null||!$Entry->CurrentUserIsAllowedToSee()){
            return $this->ResponseWrongData();
        }
        return $this->ResponseSuccess(array ('entryId' => $Entry->EntryId,'hash' => $Entry->GetHash()));
    }
    
    
}

?>