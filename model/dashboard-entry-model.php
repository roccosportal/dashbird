<?php

class DashboardEntriesModelTable extends ModelTable {

        public function __construct() {
                // define the table name
                $this->TableName = 'DashboardEntries';

                $this->ModelName = 'DashboardEntry';

                $this->PrimaryKeyName = 'DashboardEntryId';

                $this->FieldDefinition['DashboardEntryId'] = array('Type' => 'PrimaryKey');

                $this->FieldDefinition['Module'] = array('Type' => 'Normal');

                $this->FieldDefinition['ReferenceId'] = array('Type' => 'Normal');

                $this->FieldDefinition['Date'] = array('Type' => 'Normal');

                $this->FieldDefinition['SearchHelper'] = array('Type' => 'Normal');

                $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');

                $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');

                $this->FieldDefinition['DashboardEntriesTags'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'DashboardEntriesTags', 'ForeignKey' => 'DashboardEntryId');
                
                $this->FieldDefinition['SearchHelperParts'] = array('Type' => 'ManyForeignObjects', 'ModelTable' => 'SearchHelperParts', 'ForeignKey' => 'DashboardEntryId');
                
                
        }

}

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
class DashboardEntryModel extends Model {


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
                                return ModelTable::Get('Links');
                                break;
                        case 'Note':
                                return ModelTable::Get('Notes');
                                break;
                        case 'Todo':
                                return ModelTable::Get('Todos');
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



        public function SetSearchHelpePart($Keyword, $Value) {
                $SearchHelperParts = $this->SearchHelperParts->Sort('StartAt');
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
                        $SearchHelperPart = new SearchHelperPartModel();
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
                // delete previous tags refering on dashboardentry
                
                SQLManager::GetInstance()->DeleteWithParameters('DELETE FROM DashboardEntriesTags WHERE DashboardEntriesTags.DashboardEntryId = %s', array($this->DashboardEntryId));
                
                // CAUTION: DashboardEntrieTags and Tags in the cache maybe still have a reference to this object
//                // manually delete cache entries
                $CacheDashboardEntriesTags = ModelTable::Get('DashboardEntriesTags')->GetCache()->GetAllCacheInstances();
                foreach($CacheDashboardEntriesTags as $CacheDashboardEntryTag){
                        /* @var $CacheDashboardEntryTag DashboardEntriesTagsModel */
                        if($CacheDashboardEntryTag->DashboardEntryId == $this->DashboardEntryId){
                                ModelTable::Get('DashboardEntriesTags')->GetCache()->Delete($CacheDashboardEntryTag);
                        }
                }
                
                
                
                
                
                $TagModels = array();
                if (count($TagTitles) > 0) {
                        // search for existing tags
                        $TitleInStateMent = '';
                        $First = true;
                        $Query = new Query('Tags');
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
                                $TagModel = new TagModel();
                                $TagModel->Title = $TagTitle;
                                $TagModel->Insert();
                                // append to list
                                $TagModels->append($TagModel);
                        }
                }

                // insert relations tags - dashboardentry
                foreach ($TagModels as $TagModel) {
                        $DashboadEntriesTags = new DashboardEntriesTagsModel();
                        $DashboadEntriesTags->DashboardEntryId = $this->DashboardEntryId;
                        $DashboadEntriesTags->TagId = $TagModel->TagId;
                        $DashboadEntriesTags->Insert();
                        
                }
               
        }

        public function AddTag($TagTitle) {
                $Query = new Query('Tags');
                $Query->SetConditions('WHERE Tags.Title = "%s"');
                $Query->AddParameter($TagTitle);

                $Tag = $Query->SelectSingle();


                if ($Tag == null) {

                        $Tag = new TagModel();
                        $Tag->Title = $TagTitle;
                        $Tag->Insert();

                        $DashboadEntriesTags = new DashboardEntriesTagsModel();
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
                                $DashboadEntriesTags = new DashboardEntriesTagsModel();
                                $DashboadEntriesTags->DashboardEntryId = $this->DashboardEntryId;
                                $DashboadEntriesTags->TagId = $Tag->TagId;
                                $DashboadEntriesTags->Insert();
                        }
                }
        }

        public function DeleteTag($TagTitle) {
                $Query = new Query('Tags');
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

}

?>