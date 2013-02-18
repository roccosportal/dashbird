<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $PostId
 * @property string $Text
 * @property string $Created
 * @property string $Updated
 * @property string $SearchHelper
 * @property int $UserId
 * @property User $User
 * @property \Pvik\Database\Generic\EntityArray $PostsTags
 * @property \Pvik\Database\Generic\EntityArray $PostShares
 * @property \Pvik\Database\Generic\EntityArray $Comments
 */
class Post extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'Posts';
    }

    public function Insert() {
        if ($this->Created == null) {
            $this->Created = date('Y-m-d H:i:s');
            $this->Updated = $this->Created;
        }
        $this->SearchHelper = $this->User->Name . $this->Text;
        parent::Insert();
    }
    
 
    public function CurrentUserHasPermissionToChange() {
        return (\Dashbird\Library\Services\UserService::Instance()->GetUserId() == $this->UserId);
    }
    
    public function CurrentUserIsAllowedToSee() {
       if($this->CurrentUserHasPermissionToChange()){
           return true;
       }
       $UserId = \Dashbird\Library\Services\UserService::Instance()->GetUserId();
       
       foreach($this->PostShares as $PostShare){
           /* @var $PostShare PostShare */
           if($PostShare->UserId==$UserId){
               return true;
           }
       }
       return false;
    }

    public function ToArray() {
        $TagTitle = array();
        foreach ($this->PostsTags as $PostsTags) {
            $TagTitle[] = $PostsTags->Tag->Title;
        }
        $PostSharesUserIds = array();
        $LoggedInUserId = \Dashbird\Library\Services\UserService::Instance()->GetUserId();
        $LastView = null;
        //if ($this->CurrentUserHasPermissionToChange()) {
            foreach ($this->PostShares as $PostShare) {
                /* @var $PostShare \Dashbird\Model\Entities\PostShare */
                if($PostShare->UserId == $LoggedInUserId){
                    $LastView = $PostShare->LastView;
                }
                else if($PostShare->UserId != $this->UserId){
                    $PostSharesUserIds[] = $PostShare->UserId;
                }
            }
        //}

        $Comments = array();
        foreach ($this->Comments as $Comment) {
            /* @var $Comment Comment */
            $Comments[] = $Comment->ToArray();
        }


        return array(
            'postId' => $this->PostId,
            'created' => $this->Created,
            'updated' => $this->Updated,
            'text' => $this->Text,
            'tags' => $TagTitle,
            'user' => array('userId' => $this->UserId, 'name' => $this->User->Name),
            'lastView' => $LastView,
            'postShares' => $PostSharesUserIds,
            'comments' => $Comments
        );
    }
    


    public function Delete() {
        foreach ($this->Comments as $Comment) {
            /* @var $Comment Comment */
            $Comment->Delete();
        }

        foreach ($this->PostShares as $PostShare) {
            /* @var $PostShare PostShare */
            $PostShare->Delete();
        }

        foreach ($this->PostsTags as $PostsTag) {
            /* @var $DashboardPostsTag DashboardPostsTags */
            $PostsTag->Delete();
        }

        parent::Delete();
    }

    public function SetTags(array $TagTitles) {

        $TagTitles = $this->ValidateTagTitles($TagTitles);
        // delete previous tags refering on dashboardpost

        \Pvik\Database\SQL\Manager::GetInstance()->DeleteWithParameters('DELETE FROM PostsTags WHERE PostsTags.PostId = %s', array($this->PostId));

        // CAUTION: DashboardEntrieTags and Tags in the cache maybe still have a reference to this object
//                // manually delete cache posts
        $CachePostsTags = \Pvik\Database\Generic\ModelTable::Get('PostsTags')->GetCache()->GetAllCacheInstances();
        foreach ($CachePostsTags as $CachePostTag) {
            /* @var $CachePostTag PostsTags */
            if ($CachePostTag->PostId == $this->PostId) {
                \Pvik\Database\Generic\ModelTable::Get('PostsTags')->GetCache()->Delete($CachePostTag);
            }
        }

        // clear all reference keys 
        $this->SetFieldData('PostsTags', null);





        $TagModels = array();
        if (count($TagTitles) > 0) {
            // search for existing tags
            $TitleInStateMent = '';
            $First = true;
            $Query = new \Pvik\Database\Generic\Query('Tags');
            foreach ($TagTitles as $TagTitle) {
                if ($First) {
                    $First = false;
                } else {
                    $TitleInStateMent .= ',';
                }
                $TitleInStateMent .= '"%s"';
                $Query->AddParameter($TagTitle);
            }


            $Query->SetConditions('WHERE title IN (' . $TitleInStateMent . ')');
            $TagModels = $Query->Select(); // get existing tags
            foreach ($TagModels as $TagModel) {
                // delete found tag
                unset($TagTitles[array_search($TagModel->Title, $TagTitles)]);
            }

            // now iterate the non existing tag titles
            foreach ($TagTitles as $TagTitle) {
                // create tag
                $TagModel = new Tag();
                $TagModel->Title = $TagTitle;
                $TagModel->Insert();
                // append to list
                $TagModels->append($TagModel);
            }
        }

        // insert relations tags - dashboardpost
        foreach ($TagModels as $TagModel) {
            $PostsTags = new \Dashbird\Model\Entities\PostsTags();
            $PostsTags->PostId = $this->PostId;
            $PostsTags->TagId = $TagModel->TagId;
            $PostsTags->Insert();
        }
    }

    public function AddTag($TagTitle) {
        if (!$this->ValidateTagTitle($TagTitle)) {
            return false;
        }
        $Query = new \Pvik\Database\Generic\Query('Tags');
        $Query->SetConditions('WHERE Tags.Title = "%s"');
        $Query->AddParameter($TagTitle);

        $Tag = $Query->SelectSingle();


        if ($Tag == null) {

            $Tag = new Tag();
            $Tag->Title = $TagTitle;
            $Tag->Insert();

            $PostsTags = new \Dashbird\Model\Entities\PostsTags();
            $PostsTags->PostId = $this->PostId;
            $PostsTags->TagId = $Tag->TagId;
            $PostsTags->Insert();
        } else {
            $AlreadyExists = false;
            foreach ($this->PostsTags as $PostsTags) {
                if ($PostsTags->TagId == $Tag->TagId) {
                    $AlreadyExists = true;
                    break;
                }
            }
            if (!$AlreadyExists) {
                $PostsTags = new \Dashbird\Model\Entities\PostsTags();
                $PostsTags->PostId = $this->PostId;
                $PostsTags->TagId = $Tag->TagId;
                $PostsTags->Insert();
            }
        }
    }

    public function DeleteTag($TagTitle) {
        $Query = new \Pvik\Database\Generic\Query('Tags');
        $Query->SetConditions('WHERE Tags.Title = "%s"');
        $Query->AddParameter($TagTitle);

        $Tag = $Query->SelectSingle();
        if ($Tag != null) {
            foreach ($this->PostsTags as $PostsTags) {
                if ($PostsTags->TagId == $Tag->TagId) {
                    $PostsTags->Delete();
                    break;
                }
            }
        }
    }

    public function Update() {
        $this->Updated = date('Y-m-d H:i:s');

        $TagTitles = '';
        // for perfomance, we don't need lazy loading in this loop
        $this->PostsTags->LoadList('Tag');
        foreach ($this->PostsTags as $PostsTags) {
            $TagTitles .= $PostsTags->Tag->Title;
        }
      
        $CommentText = '';
        foreach ($this->Comments as $Comment){
            $CommentText .= $Comment->Text;
        }
        
        $this->SearchHelper = $this->User->Name . $this->Text . $TagTitles . $CommentText;
        parent::Update();
    }

    protected function ValidateTagTitle($TagTitle) {
        return (strpos($TagTitle, '<') === false) && (strpos($TagTitle, '>') === false);
    }

    protected function ValidateTagTitles(array $TagTitles) {
        $TagTitlesTemp = array();
        foreach ($TagTitles as $TagTitle) {
            if ($this->ValidateTagTitle($TagTitle)) {
                $TagTitlesTemp[] = $TagTitle;
            }
        }
        return $TagTitlesTemp;
    }

    public function SetPostShares($UserIds) {
        \Pvik\Database\SQL\Manager::GetInstance()->DeleteWithParameters('DELETE FROM PostShares WHERE PostShares.PostId = %s', array($this->PostId));

        // CAUTION: PostShares in the cache maybe still have a reference to this object
        // manually delete cache posts
        $CachePostShares = \Pvik\Database\Generic\ModelTable::Get('PostShares')->GetCache()->GetAllCacheInstances();
        foreach ($CachePostShares as $CachePostShare) {
            /* @var $CachePostShare PostShare */
            if ($CachePostShare->PostId == $this->PostId) {
                \Pvik\Database\Generic\ModelTable::Get('PostShares')->GetCache()->Delete($CachePostShare);
            }
        }

        // clear all reference keys 
        $this->SetFieldData('PostShares', null);


        // validate userIds
        $UserShares = \Dashbird\Library\Services\UserService::Instance()->GetUser()->UserShares;
        $FilteredUserIds = array();
        foreach ($UserIds as $UserId) {
            if ($UserShares->HasValue('ConnectedUserId', $UserId) && !in_array($UserId, $FilteredUserIds)) {
                $FilteredUserIds[] = $UserId;
            }
        }
        $UserIds = $FilteredUserIds;
        $UserIds[] = $this->UserId; // add him self to shared
        foreach ($UserIds as $UserId) {
            $PostShare = new \Dashbird\Model\Entities\PostShare();
            $PostShare->PostId = $this->PostId;
            $PostShare->UserId = $UserId;
            $PostShare->Insert();
        }
    }

}
