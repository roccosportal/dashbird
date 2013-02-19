<?php

namespace Dashbird\Controllers;

class Comment extends Base {
    public function ApiPostCommentAddAction(){
        if(!$this->IsLoggedIn()){
            return $this->ResponseNotLoggedIn();
        }
        $PostId = $this->Request->GetGET('postId');
        $Text = $this->Request->GetGET('text');
        
        if($PostId == null || empty($Text)){
            return $this->ResponseWrongData();
        }
        
        $Post = \Pvik\Database\Generic\ModelTable::Get('Posts')->LoadByPrimaryKey($PostId);
        /* @var $Post \Dashbird\Model\Entities\Post */
        if($Post == null){
            return $this->ResponseWrongData();
        }
        
        if(!$Post->CurrentUserHasPermissionToChange()){
            // user is not owner, lets look if the post is shared with him
            $IsShared = false;
            foreach($Post->PostShares as $PostShare){
                 /* @var $PostShare \Dashbird\Model\Entities\PostShare */
                 if($PostShare->UserId == $this->GetUserId()){
                     $IsShared = true;
                     break;
                 }
            }
            if(!$IsShared){
                return $this->ResponseWrongData();
            }
        }
        
        $Comment = new \Dashbird\Model\Entities\Comment();
        $Comment->PostId = $Post->PostId;
        $Comment->Text = $Text;
        $Comment->UserId = $this->GetUserId();
        $Comment->Insert();
        
        $Post->Update();
        
        
        $this->ResponseSuccess(array ('post' => $Post->ToArray(), 'comment' => $Comment->ToArray()));
    }
    
    public function ApiPostCommentDeleteAction(){
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
        $Post = $Comment->Post;
        
        $Comment->Delete();
        $Post->Update();
        return $this->ResponseSuccess($Post->ToArray());
        
        
        
    }
}
