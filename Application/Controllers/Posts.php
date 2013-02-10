<?php

namespace Dashbird\Controllers;

use Pvik\Database\Generic\ModelTable;

class Posts extends Base {

    public function IndexAction() {
        // output of the basic html
        $this->ExecuteView();
    }

    public function ApiPostsLoadAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }


        $Search = $this->Request->GetGET('search');
        $StartPosition = $this->Request->GetGET('start-position');
        if($StartPosition==null) $StartPosition = 0;
        $PostCount = $this->Request->GetGET('post-count');
        if($PostCount==null) $PostCount = 9999999999;

        $Query = new \Pvik\Database\Generic\Query('Posts');
        $ConditionString = 'LEFT JOIN PostShares as PostShares ON PostShares.PostId = Posts.PostId';
        $ConditionString .= ' WHERE (Posts.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        $ConditionString .= ' OR PostShares.UserId = "%s")';
        $Query->AddParameter($this->GetUserId());
        if (!empty($Search)) {
            $SearchWords = preg_split("/[\s]+/", $Search);
            foreach ($SearchWords as $SearchWord) {
                $Query->AddParameter('%' . $SearchWord . '%');
                $ConditionString .= ' AND Posts.SearchHelper LIKE "%s"';
            }
        }
        $Query->SetConditions($ConditionString);
        //$HasMorePostsQuery = clone $Query;
        $Query->SetOrderBy('ORDER BY Posts.Updated DESC LIMIT %s,%s');
        $Query->AddParameter($StartPosition);
        $Query->AddParameter($PostCount);
        $Posts = $Query->Select();
        
     
       
        // optimization for tags
        $Posts->LoadList('PostsTags->Tag');
      
        $Data = array();
        $Data['posts'] = array();
        foreach ($Posts as $Post) {
            $Data['posts'][] = $Post->ToArray();
        }
  
        $this->ResponseSuccess($Data);
    }
    
    public function ApiPostsUpdatedGetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
        $PostCount = $this->Request->GetGET('post-count');
        if($PostCount==null) $PostCount = 50;
    
        $Search = $this->Request->GetGET('search');
        $StartPosition = $this->Request->GetGET('start-position');
        if($StartPosition==null) $StartPosition = 0;
        $PostCount = $this->Request->GetGET('post-count');
        if($PostCount==null) $PostCount = 50;

        $Query = new \Pvik\Database\Generic\Query('Posts');
        $ConditionString = 'LEFT JOIN PostShares as PostShares ON PostShares.PostId = Posts.PostId';
        $ConditionString .= ' WHERE (Posts.UserId = "%s"';
        $Query->AddParameter($this->GetUserId());
        $ConditionString .= ' OR PostShares.UserId = "%s")';
        $Query->AddParameter($this->GetUserId());
        
        $Query->SetConditions($ConditionString);
        $Query->SetOrderBy('ORDER BY Posts.Updated DESC LIMIT %s,%s');
        $Query->AddParameter($StartPosition);
        $Query->AddParameter($PostCount);
        $Posts = $Query->Select();
        
        
        $Data = array();
        $Data['dates'] = array();
        foreach ($Posts as $Post) {
            $Data['dates'][] = array ('postId' => $Post->PostId,
                    'updated' => $Post->Updated);
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

        return $this->ResponseSuccess();
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
        $Post->SetSearchHelperPart('text',  $Post->Text);
        $Post->SetSearchHelperPart('user-name',  $this->GetUser()->Name);
        $Tags = $this->Request->GetGET('tags');
        if (is_array($Tags)) {
            $Post->SetTags($Tags);
        } else {
            $Post->SetTags(array());
        }
        $Post->Update();
        
        $Shares = $this->Request->GetGET('shares');
       
        if (is_array($Shares)) {
            $Post->SetPostShares($Shares);
        }
        
       
       


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
        $Post->Update();
    
        if (is_array($Tags)) {
            $Post->SetTags($Tags);
        } else {
            $Post->SetTags(array());
        }
        $Post->SetSearchHelperPart('text', $Post->Text);

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
        /* @var $Post \Dashbird\Model\Entities\Post */
        $Array = array();
        foreach($Posts as $Post){
            if($Post->CurrentUserIsAllowedToSee()){
                $Array[] = $Post->ToArray();
            }
        }
        
        return $this->ResponseSuccess($Array);
    }
    
    
    
//     public function AjaxGetHashAction() {
//        if (!$this->IsLoggedIn()) {
//            return $this->ResponseNotLoggedIn();
//        }
//        
//        $PostId = $this->Request->GetGET('postId');
//        $Post = ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
//         /* @var $Post \Dashbird\Model\Entities\Post */
//        if($Post==null||!$Post->CurrentUserIsAllowedToSee()){
//            return $this->ResponseWrongData();
//        }
//        return $this->ResponseSuccess(array ('postId' => $Post->PostId,'hash' => $Post->GetHash()));
//    }
    
    
}
