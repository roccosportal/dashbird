<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $DashboardEntryId
 * @property string $Module
 * @property int $ReferenceId
 * @property string $Date
 * @property string $SearchHelper
 * @property int $UserId
 * @porperty UserModel $User
 * @property ModelArray $DashboardEntriesTags
 * @property ModelArray $SearchHelperParts
 */
class DashboardEntry extends \Pvik\Database\Generic\Entity {


        public function __construct() {
                $this->ModelTableName = 'DashboardEntries';
        }

        public function Insert() {
                if ($this->Date == null) {
                        $this->Date = date('Y.m.d  H:i:s');
                }

                parent::Insert();
        }

        public function GetModelTableForModule() {
                switch ($this->Module) {
                        case 'Link':
                                return \Pvik\Database\Generic\ModelTable::Get('Links');
                                break;
                        case 'Note':
                                return \Pvik\Database\Generic\ModelTable::Get('Notes');
                                break;
                        case 'Todo':
                                return \Pvik\Database\Generic\ModelTable::Get('Todos');
                                break;
                        default:
                                return null;
                                break;
                }
        }

        public function GetReferenceObject() {
                $ModelTable = $this->GetModelTableForModule();
                if ($ModelTable) {
                        return $ModelTable->LoadByPrimaryKey($this->ReferenceId);
                }
        }

        public function GetReferenceArray() {
                $Object = $this->GetReferenceObject();
                $Array = array();
                if ($Object != null) {
                        $Array = $Object->ToArray();
                }
                return $Array;
        }

        public function ToArray() {
                $TagTitle = array();
                foreach ($this->DashboardEntriesTags as $DashboardEntriesTags) {
                        $TagTitle[] = $DashboardEntriesTags->Tag->Title;
                }
                return array(
                    'dashboardEntryId' => $this->DashboardEntryId,
                    'date' => $this->Date,
                    'module' => $this->Module,
                    'reference' => $this->GetReferenceArray(),
                    'tags' => $TagTitle
                );
        }
        
        /**
         * Find a search helper part by the keyword
         * @param string $Keyword
         * @return SearchHelperPartModel 
         */
        public function FindSearchHelperPartByKeyWord($Keyword){
                 $SearchHelperParts = $this->SearchHelperParts;
                 foreach ($SearchHelperParts as $SearchHelperPart) {
                          /* @var $SearchHelperPart SearchHelperPartModel */
                         if ($SearchHelperPart->Keyword == $Keyword) {
                                 return $SearchHelperPart;
                         }
                 }
                 return null;
        }


        public function SetSearchHelpePart($Keyword, $Value) {
                $SearchHelperParts = $this->SearchHelperParts->Sort('StartAt');
                $CurrentSearchHelperPart = $this->FindSearchHelperPartByKeyWord($Keyword);
                if($CurrentSearchHelperPart !== null){
                        // if this keyword already exists check if we need to change something
                        $OldValue = substr($this->SearchHelper, $CurrentSearchHelperPart->StartAt, ($CurrentSearchHelperPart->EndAt -  $CurrentSearchHelperPart->StartAt) + 1);
                        if($OldValue == $Value){
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
                        $SearchHelperPart->DashboardEntryId = $this->DashboardEntryId;
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
                        /* @var $SearchHelperPart SearchHelperPartModel */
                        $SearchHelperPart->Delete();
                }

                parent::Delete();
        }

        public function SetTags(array $TagTitles) {
               
                $TagTitles = $this->ValidateTagTitles($TagTitles);
                // delete previous tags refering on dashboardentry
                
                \Pvik\Database\SQL\Manager::GetInstance()->DeleteWithParameters('DELETE FROM DashboardEntriesTags WHERE DashboardEntriesTags.DashboardEntryId = %s', array($this->DashboardEntryId));
                
                // CAUTION: DashboardEntrieTags and Tags in the cache maybe still have a reference to this object
//                // manually delete cache entries
                $CacheDashboardEntriesTags = \Pvik\Database\Generic\ModelTable::Get('DashboardEntriesTags')->GetCache()->GetAllCacheInstances();
                foreach($CacheDashboardEntriesTags as $CacheDashboardEntryTag){
                        /* @var $CacheDashboardEntryTag DashboardEntriesTagsModel */
                        if($CacheDashboardEntryTag->DashboardEntryId == $this->DashboardEntryId){
                            \Pvik\Database\Generic\ModelTable::Get('DashboardEntriesTags')->GetCache()->Delete($CacheDashboardEntryTag);
                        }
                }
                
                // clear all reference keys 
                $this->SetFieldData('DashboardEntriesTags', null);
                
                
                
                
                
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
                        $DashboadEntriesTags = new \Dashbird\Model\Entities\DashboardEntriesTags();
                        $DashboadEntriesTags->DashboardEntryId = $this->DashboardEntryId;
                        $DashboadEntriesTags->TagId = $TagModel->TagId;
                        $DashboadEntriesTags->Insert();
                        
                }
               
        }

        public function AddTag($TagTitle) {
                if(!$this->ValidateTagTitle($TagTitle)){
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

                        $DashboadEntriesTags = new \Dashbird\Model\Entities\DashboardEntriesTags();
                        $DashboadEntriesTags->DashboardEntryId = $this->DashboardEntryId;
                        $DashboadEntriesTags->TagId = $Tag->TagId;
                        $DashboadEntriesTags->Insert();
                } else {
                        $AlreadyExists = false;
                        foreach ($this->DashboardEntriesTags as $DashboadEntriesTags) {
                                if ($DashboadEntriesTags->TagId == $Tag->TagId) {
                                        $AlreadyExists = true;
                                        break;
                                }
                        }
                        if (!$AlreadyExists) {
                                $DashboadEntriesTags = new \Dashbird\Model\Entities\DashboardEntriesTags();
                                $DashboadEntriesTags->DashboardEntryId = $this->DashboardEntryId;
                                $DashboadEntriesTags->TagId = $Tag->TagId;
                                $DashboadEntriesTags->Insert();
                        }
                }
        }

        public function DeleteTag($TagTitle) {
                $Query = new \Pvik\Database\Generic\Query('Tags');
                $Query->SetConditions('WHERE Tags.Title = "%s"');
                $Query->AddParameter($TagTitle);

                $Tag = $Query->SelectSingle();
                if ($Tag != null) {
                        foreach ($this->DashboardEntriesTags as $DashboadEntriesTags) {
                                if ($DashboadEntriesTags->TagId == $Tag->TagId) {
                                        $DashboadEntriesTags->Delete();
                                        break;
                                }
                        }
                }
        }

        public function Update() {
                $TagTitles = '';
                // for perfomance, we don't need lazy loading in this loop
                $this->DashboardEntriesTags->LoadList('Tag');
                foreach ($this->DashboardEntriesTags as $DashboadEntriesTags) {
                        $TagTitles .= $DashboadEntriesTags->Tag->Title;
                }
                $this->SetSearchHelpePart('tags', $TagTitles);
                parent::Update();
        }
        
        protected function ValidateTagTitle($TagTitle){
            return (strpos($TagTitle, '<') === false) && (strpos($TagTitle, '>') === false);
        }
        
        protected function ValidateTagTitles(array $TagTitles){
            $TagTitlesTemp = array();
            foreach($TagTitles as $TagTitle){
                if($this->ValidateTagTitle($TagTitle)){
                    $TagTitlesTemp[] = $TagTitle;
                }
            }
            return $TagTitlesTemp;
        }

}

?>