<?php
class DashboardEntriesTagsModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'DashboardEntriesTags';
       
        $this->ModelName = 'DashboardEntriesTags';
       
        $this->PrimaryKeyName = 'DashboardEntriesTagsId';
       
        $this->FieldDefinition['DashboardEntriesTagsId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['DashboardEntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'DashboardEntries');
       
        $this->FieldDefinition['DashboardEntry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'DashboardEntryId');
        
        $this->FieldDefinition['TagId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Tags');
       
        $this->FieldDefinition['Tag'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'TagId');
        

    }
}

/**
 * @property int $DashboardEntriesTagsId
 * @property int $DashboardEntryId
 * @property DashboardEntryModel $DashboardEntry
 * @property int $TagId
 * @property TagModel $Tag
 */
class DashboardEntriesTagsModel extends Model {
    public function __construct(){
        $this->ModelTableName = 'DashboardEntriesTags';
    }

}
?>