<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class Posts extends Base {
    static $OrderByTypes = array ('CREATED' => 'Created', 'UPDATED' => 'Updated');
    
    public function IndexAction() {
        if(!$this->IsLoggedIn()){
            return $this->RedirectToPath('/login');
        }
        $this->ViewData->Set('UserData', json_encode($this->GetUser()->ToArray()));
        
        $this->ExecuteView();
    }

    public function ApiPostsLoadAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $StartDate = $this->Request->GetGET('start-date');
        if($StartDate==null) $StartDate = 0;
        $NewerEqualsThanDate = $this->Request->GetGET('newer-equals-than-date');
        $PostCount = $this->Request->GetGET('post-count');
        if($PostCount==null) $PostCount = 9999999999;
        $OrderBy = $this->Request->GetGET('order-by');
        if($OrderBy==null|| !isset(self::$OrderByTypes[$OrderBy])) $OrderBy = self::$OrderByTypes['CREATED'];
        if($OrderBy == self::$OrderByTypes['CREATED'])
            $OrderBy2 = self::$OrderByTypes['UPDATED'];
        else 
            $OrderBy2 = self::$OrderByTypes['CREATED'];
        
        $Query = new \Pvik\Database\Generic\Query('Posts');
        $ConditionString = 'LEFT JOIN PostShares as PostShares ON PostShares.PostId = Posts.PostId';
        $ConditionString .= ' WHERE PostShares.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        if($StartDate!=null){
            $ConditionString .= ' AND Posts.' . $OrderBy .' < "%s"';
            $Query->AddParameter($StartDate);
        }
        if($NewerEqualsThanDate!=null){
            $ConditionString .= ' AND Posts.Updated >= "%s"';
            $Query->AddParameter($NewerEqualsThanDate);
        }
        $Query->SetConditions($ConditionString);
        $Query->SetOrderBy('ORDER BY Posts.' . $OrderBy .' DESC, ' . $OrderBy2 .' DESC LIMIT 0,%s');
        $Query->AddParameter($PostCount);
        $Posts = $Query->Select();
        
     
       
        // optimization for tags
        $Posts->LoadList('PostsTags->Tag');
        
        // optimization for postshare
        $Posts->LoadList('PostShares');
        
         // optimization for comments
        $Posts->LoadList('Comments');
      
        $Data = array();
        $Data['posts'] = array();
        foreach ($Posts as $Post) {
            $Data['posts'][] = $Post->ToArray();
        }
  
        $this->ResponseSuccess($Data);
    }
    
    public function ApiPostsSearchAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $Search = $this->Request->GetGET('search');
        if(!is_array($Search) || !isset($Search['keywords'])){
            return $this->ResponseWrongData();
        }
        
        $PostCount = $this->Request->GetGET('post-count');
        if($PostCount==null) $PostCount = 9999999999;
     
        $Query = new \Pvik\Database\Generic\Query('Posts');
        $ConditionString = 'LEFT JOIN PostShares as PostShares ON PostShares.PostId = Posts.PostId';
        $ConditionString .= ' WHERE PostShares.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
       
        foreach ($Search['keywords'] as $Keyword) {
            $Query->AddParameter('%' . $Keyword . '%');
            $ConditionString .= ' AND Posts.SearchHelper LIKE "%s"';
        }
        
        $Query->SetConditions($ConditionString);
        $Query->SetOrderBy('ORDER BY Posts.Updated DESC LIMIT 0,%s');
        $Query->AddParameter($PostCount);
        $Posts = $Query->Select();
        
     
       
        // optimization for tags
        $Posts->LoadList('PostsTags->Tag');
        
        // optimization for postshare
        $Posts->LoadList('PostShares');
        
         // optimization for comments
        $Posts->LoadList('Comments');
      
        $Data = array();
        $Data['posts'] = array();
        foreach ($Posts as $Post) {
            $Data['posts'][] = $Post->ToArray();
        }
  
        $this->ResponseSuccess($Data);
    }
    
  

    public function ApiPostSharesSetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $PostId = $this->Request->GetGET('postId');

        $UserIds = $this->Request->GetGET('userIds');
        if (empty($UserIds)) {
            $UserIds = array();
        }
        if (empty($PostId) || !is_array($UserIds)) {
            return $this->ResponseWrongData();
        }

        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
        /* @var $Post \Dashbird\Model\Entities\Post */
        if ($Post != null && !$Post->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }

        $Post->SetPostShares($UserIds);

        return $this->ResponseSuccess($Post->ToArray());
    }

    
   public function ApiPostAddAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        

        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        if (empty($Text)) {
            return $this->ResponseWrongData();
        }


        $Post = new \Dashbird\Model\Entities\Post();
        $Post->Text = $Text;
        $Post->SearchHelper = '';
        $Post->UserId = $this->GetUserId();
        
        $Post->Insert();
        $Tags = $this->Request->GetGET('tags');
        if (is_array($Tags)) {
            $Post->SetTags($Tags);
        } else {
            $Post->SetTags(array());
        }
        $Post->Update();
        
        $Shares = $this->Request->GetGET('shares');
       
        if (!is_array($Shares)) {
            $Shares = array();
        }
        $Post->SetPostShares($Shares);
       
       


        return $this->ResponseSuccess($Post->ToArray());
    }

    public function ApiPostDeleteAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        $PostId = $this->Request->GetGET('postId');
        if (!is_numeric($PostId)) {
            return $this->ResponseWrongData();
        }
        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
        /* @var $Post \Dashbird\Model\Entities\Post */
        if ($Post == null) {
            return $this->ResponseWrongData();
        }
        if (!$Post->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Post->Delete();
        return $this->ResponseSuccess();
    }

    public function ApiPostEditAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        

        $PostId = $this->Request->GetGET('postId');
        $Text = htmlentities(utf8_decode($this->Request->GetGET('text')));
        $Tags = $this->Request->GetGET('tags');
        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
        /* @var $Post \Dashbird\Model\Entities\Post */
        if ($Post == null || empty($Text) || !$Post->CurrentUserHasPermissionToChange()) {
            return $this->ResponseWrongData();
        }
        $Post->Text = $Text;

        if (is_array($Tags)) {
            $Post->SetTags($Tags);
        } else {
            $Post->SetTags(array());
        }

        $Post->Update();
        return $this->ResponseSuccess($Post->ToArray());
    }
    
    public function ApiPostGetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
        $PostId = $this->Request->GetGET('postId');
        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
         /* @var $Post \Dashbird\Model\Entities\Post */
        if($Post==null||!$Post->CurrentUserIsAllowedToSee()){
            return $this->ResponseWrongData();
        }
        return $this->ResponseSuccess($Post->ToArray());
    }
    
    public function ApiPostsGetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }

        $PostIds = $this->Request->GetGET('postIds');
        if(!is_array($PostIds)){
              return $this->ResponseWrongData();
        }
        $Posts = ModelTable::Get('Posts')->LoadByPrimaryKeys($PostIds);
        
        if($Posts==null){
            return $this->ResponseWrongData();
        }
        
        // optimization for tags
        $Posts->LoadList('PostsTags->Tag');
        
        // optimization for postshare
        $Posts->LoadList('PostShares');
        
         // optimization for comments
        $Posts->LoadList('Comments');
        
        
        /* @var $Post \Dashbird\Model\Entities\Post */
        $Array = array();
        foreach($Posts as $Post){
            if($Post->CurrentUserIsAllowedToSee()){
                $Array[] = $Post->ToArray();
            }
        }
        
        return $this->ResponseSuccess($Array);
    }
    
    public function ApiPostLastViewSetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
        
        $PostId = $this->Request->GetGET('postId');
        $LastView = $this->Request->GetGET('lastView');
        if($PostId==null||$LastView==null){
            return $this->ResponseWrongData();
        }
        
        
        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
         /* @var $Post \Dashbird\Model\Entities\Post */
        if($Post==null||!$Post->CurrentUserIsAllowedToSee()){
            return $this->ResponseWrongData();
        }
        foreach($Post->PostShares as $PostShare){
            if($PostShare->UserId == $this->GetUserId()){
                $PostShare->LastView = $LastView;
                $PostShare->Update();
                break;
            }
        }
        
       
        return $this->ResponseSuccess($Post->ToArray());
    }

}
