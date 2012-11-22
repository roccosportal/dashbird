<?php

namespace Dashbird\Controllers;

class Comment extends Base {
    public function AjaxAddCommentAction(){
        if(!$this->IsLoggedIn()){
            return $this->ResponseNotLoggedIn();
        }
        $EntryId = $this->Request->GetGET('entryId');
        $Text = $this->Request->GetGET('text');
        
        if($EntryId == null || empty($Text)){
            return $this->ResponseWrongData();
        }
        
        $Entry = \Pvik\Database\Generic\ModelTable::Get('Entries')->LoadByPrimaryKey($EntryId);
        /* @var $Entry \Dashbird\Model\Entities\Entry */
        if($Entry == null){
            return $this->ResponseWrongData();
        }
        
        if(!$Entry->CurrentUserHasPermissionToChange()){
            // user is not owner, lets look if the entry is shared with him
            $IsShared = false;
            foreach($Entry->EntryShares as $EntryShare){
                 /* @var $EntryShare \Dashbird\Model\Entities\EntryShare */
                 if($EntryShare->UserId == $this->GetUserId()){
                     $IsShared = true;
                     break;
                 }
            }
            if(!$IsShared){
                return $this->ResponseWrongData();
            }
        }
        
        $Comment = new \Dashbird\Model\Entities\Comment();
        $Comment->EntryId = $Entry->EntryId;
        $Comment->Text = $Text;
        $Comment->UserId = $this->GetUserId();
        $Comment->Insert();
        $this->ResponseSuccess($Comment->ToArray());
    }
    
    public function AjaxDeleteCommentAction(){
        if(!$this->IsLoggedIn()){
            return $this->ResponseNotLoggedIn();
        }
        
        $CommentId = $this->Request->GetGET('commentId');
        if($CommentId == null || !is_numeric($CommentId)){
            return $this->ResponseWrongData();
        }
        
        $Comment = \Pvik\Database\Generic\ModelTable::Get('Comments')->LoadByPrimaryKey($CommentId);
        /* @var $Comment \Dashbird\Model\Entities\Comment */
        if($Comment == null || $Comment->UserId !== $this->GetUserId()){
            return $this->ResponseWrongData();
        }
        
        $Comment->Delete();
        return $this->ResponseSuccess();
        
        
        
    }
}
