<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $EntryId
 * @property string $Text
 * @property string $DateTime
 * @property string $SearchHelper
 * @property int $UserId
 * @property User $User
 * @property \Pvik\Database\Generic\EntityArray $EntriesTags
 * @property \Pvik\Database\Generic\EntityArray $SearchHelperParts
 * @property \Pvik\Database\Generic\EntityArray $EntryShares
 * @property \Pvik\Database\Generic\EntityArray $Comments
 */
class Entry extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'Entries';
    }

    public function Insert() {
        if ($this->DateTime == null) {
            $this->DateTime = date('Y.m.d  H:i:s');
        }

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
       
       foreach($this->EntryShares as $EntryShare){
           /* @var $EntryShare EntryShare */
           if($EntryShare->UserId==$UserId){
               return true;
           }
       }
       return false;
    }

    public function ToArray() {
        $TagTitle = array();
        foreach ($this->EntriesTags as $EntriesTags) {
            $TagTitle[] = $EntriesTags->Tag->Title;
        }
        $EntrySharesUserIds = array();
        //if ($this->CurrentUserHasPermissionToChange()) {
            foreach ($this->EntryShares as $EntryShare) {
                /* @var $EntryShare \Dashbird\Model\Entities\EntryShare */
                $EntrySharesUserIds[] = $EntryShare->UserId;
            }
        //}

        $Comments = array();
        foreach ($this->Comments as $Comment) {
            /* @var $Comment Comment */
            $Comments[] = $Comment->ToArray();
        }


        return array(
            'entryId' => $this->EntryId,
            'datetime' => $this->DateTime,
            'text' => $this->Text,
            'tags' => $TagTitle,
            'user' => array('userId' => $this->UserId, 'name' => $this->User->Name),
            'entryShares' => $EntrySharesUserIds,
            'comments' => $Comments,
            'hash' => $this->GetHash()
        );
    }
    
    public function GetHash(){
        return md5($this->EntryId . $this->Text . count($this->Comments));
        //return md5($this->EntryId . $this->DateTime . $this->Text . count($this->Comments));
    }

    /**
     * Find a search helper part by the keyword
     * @param string $Keyword
     * @return SearchHelperPartModel 
     */
    public function FindSearchHelperPartByKeyWord($Keyword) {
        $SearchHelperParts = $this->SearchHelperParts;
        foreach ($SearchHelperParts as $SearchHelperPart) {
            /* @var $SearchHelperPart SearchHelperPartModel */
            if ($SearchHelperPart->Keyword == $Keyword) {
                return $SearchHelperPart;
            }
        }
        return null;
    }

    public function SetSearchHelperPart($Keyword, $Value) {
        $SearchHelperParts = $this->SearchHelperParts->Sort('StartAt');
        $CurrentSearchHelperPart = $this->FindSearchHelperPartByKeyWord($Keyword);
        if ($CurrentSearchHelperPart !== null) {
            // if this keyword already exists check if we need to change something
            $OldValue = substr($this->SearchHelper, $CurrentSearchHelperPart->StartAt, ($CurrentSearchHelperPart->EndAt - $CurrentSearchHelperPart->StartAt) + 1);
            if ($OldValue == $Value) {
                // don't change anything
                return;
            }
        }


        $AlreadyExists = false;
        $DifferntLength = 0;
        foreach ($SearchHelperParts as $SearchHelperPart) {
            /* @var $SearchHelperPart SearchHelperPartModel */
            if (!$AlreadyExists) {
                if ($SearchHelperPart->Keyword == $Keyword) {
                    $AlreadyExists = true;
                    $NewLength = strlen($Value);
                    $DifferntLength = $NewLength - (($SearchHelperPart->EndAt - $SearchHelperPart->StartAt) + 1);



                    $SearchHelperTextFront = substr($this->SearchHelper, 0, $SearchHelperPart->StartAt);
                    $SearchHelperTextBack = '';


                    if (strlen($this->SearchHelper) != $SearchHelperPart->EndAt + 1) {
                        // not last item
                        $SearchHelperTextBack = substr($this->SearchHelper, $SearchHelperPart->EndAt + 1);
                    }

                    $this->SearchHelper = $SearchHelperTextFront . $Value . $SearchHelperTextBack;


                    $SearchHelperPart->EndAt = $SearchHelperPart->StartAt + $NewLength - 1;
                    $SearchHelperPart->Update();
                }
            } else {
                $SearchHelperPart->StartAt = $SearchHelperPart->StartAt + $DifferntLength;
                $SearchHelperPart->EndAt = $SearchHelperPart->EndAt + $DifferntLength;
                $SearchHelperPart->Update();
            }
        }

        if (!$AlreadyExists) {
            // we need to create a new search helper part at the end
            $SearchHelperPart = new \Dashbird\Model\Entities\SearchHelperPart();
            $SearchHelperPart->EntryId = $this->EntryId;
            $SearchHelperPart->Keyword = $Keyword;
            $SearchHelperPart->StartAt = strlen($this->SearchHelper);
            $SearchHelperPart->EndAt = $SearchHelperPart->StartAt + (strlen($Value) - 1);

            $this->SearchHelper = $this->SearchHelper . $Value;
            $SearchHelperPart->Insert();
        }
    }

    public function Delete() {
        // delete references first
        foreach ($this->SearchHelperParts as $SearchHelperPart) {
            /* @var $SearchHelperPart SearchHelperPart */
            $SearchHelperPart->Delete();
        }

        foreach ($this->Comments as $Comment) {
            /* @var $Comment Comment */
            $Comment->Delete();
        }

        foreach ($this->EntryShares as $EntryShare) {
            /* @var $EntryShare EntryShare */
            $EntryShare->Delete();
        }

        foreach ($this->EntriesTags as $EntriesTag) {
            /* @var $DashboardEntriesTag DashboardEntriesTags */
            $EntriesTag->Delete();
        }

        parent::Delete();
    }

    public function SetTags(array $TagTitles) {

        $TagTitles = $this->ValidateTagTitles($TagTitles);
        // delete previous tags refering on dashboardentry

        \Pvik\Database\SQL\Manager::GetInstance()->DeleteWithParameters('DELETE FROM EntriesTags WHERE EntriesTags.EntryId = %s', array($this->EntryId));

        // CAUTION: DashboardEntrieTags and Tags in the cache maybe still have a reference to this object
//                // manually delete cache entries
        $CacheEntriesTags = \Pvik\Database\Generic\ModelTable::Get('EntriesTags')->GetCache()->GetAllCacheInstances();
        foreach ($CacheEntriesTags as $CacheEntryTag) {
            /* @var $CacheEntryTag EntriesTags */
            if ($CacheEntryTag->EntryId == $this->EntryId) {
                \Pvik\Database\Generic\ModelTable::Get('EntriesTags')->GetCache()->Delete($CacheEntryTag);
            }
        }

        // clear all reference keys 
        $this->SetFieldData('EntriesTags', null);





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

        // insert relations tags - dashboardentry
        foreach ($TagModels as $TagModel) {
            $EntriesTags = new \Dashbird\Model\Entities\EntriesTags();
            $EntriesTags->EntryId = $this->EntryId;
            $EntriesTags->TagId = $TagModel->TagId;
            $EntriesTags->Insert();
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

            $EntriesTags = new \Dashbird\Model\Entities\EntriesTags();
            $EntriesTags->EntryId = $this->EntryId;
            $EntriesTags->TagId = $Tag->TagId;
            $EntriesTags->Insert();
        } else {
            $AlreadyExists = false;
            foreach ($this->EntriesTags as $EntriesTags) {
                if ($EntriesTags->TagId == $Tag->TagId) {
                    $AlreadyExists = true;
                    break;
                }
            }
            if (!$AlreadyExists) {
                $EntriesTags = new \Dashbird\Model\Entities\EntriesTags();
                $EntriesTags->EntryId = $this->EntryId;
                $EntriesTags->TagId = $Tag->TagId;
                $EntriesTags->Insert();
            }
        }
    }

    public function DeleteTag($TagTitle) {
        $Query = new \Pvik\Database\Generic\Query('Tags');
        $Query->SetConditions('WHERE Tags.Title = "%s"');
        $Query->AddParameter($TagTitle);

        $Tag = $Query->SelectSingle();
        if ($Tag != null) {
            foreach ($this->EntriesTags as $EntriesTags) {
                if ($EntriesTags->TagId == $Tag->TagId) {
                    $EntriesTags->Delete();
                    break;
                }
            }
        }
    }

    public function Update() {
        $TagTitles = '';
        // for perfomance, we don't need lazy loading in this loop
        $this->EntriesTags->LoadList('Tag');
        foreach ($this->EntriesTags as $EntriesTags) {
            $TagTitles .= $EntriesTags->Tag->Title;
        }
        $this->SetSearchHelperPart('tags', $TagTitles);
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

    public function SetEntryShares($UserIds) {
        \Pvik\Database\SQL\Manager::GetInstance()->DeleteWithParameters('DELETE FROM EntryShares WHERE EntryShares.EntryId = %s', array($this->EntryId));

        // CAUTION: EntryShares in the cache maybe still have a reference to this object
        // manually delete cache entries
        $CacheEntryShares = \Pvik\Database\Generic\ModelTable::Get('EntryShares')->GetCache()->GetAllCacheInstances();
        foreach ($CacheEntryShares as $CacheEntryShare) {
            /* @var $CacheEntryShare EntryShare */
            if ($CacheEntryShare->EntryId == $this->EntryId) {
                \Pvik\Database\Generic\ModelTable::Get('EntryShares')->GetCache()->Delete($CacheEntryShare);
            }
        }

        // clear all reference keys 
        $this->SetFieldData('EntryShares', null);


        // validate userIds
        $UserShares = \Dashbird\Library\Services\UserService::Instance()->GetUser()->UserShares;
        $FilteredUserIds = array();
        foreach ($UserIds as $UserId) {
            if ($UserShares->HasValue('ConnectedUserId', $UserId)) {
                $FilteredUserIds[] = $UserId;
            }
        }
        $UserIds = $FilteredUserIds;
        foreach ($UserIds as $UserId) {
            $EntryShare = new \Dashbird\Model\Entities\EntryShare();
            $EntryShare->EntryId = $this->EntryId;
            $EntryShare->UserId = $UserId;
            $EntryShare->Insert();
        }
    }

}
