<?php
class SearchHelperPartsModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'SearchHelperParts';
       
        $this->ModelName = 'SearchHelperPart';
       
        $this->PrimaryKeyName = 'SearchHelperPartId';
       
        $this->FieldDefinition['SearchHelperPartId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['DashboardEntryId'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['Keyword'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['StartAt'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['EndAt'] =  array ('Type' => 'Normal');

    }
}

/**
 * @property int $SearchHelperPartId
 * @property int $DashboardEntryId
 * @property string $Keyword
 * @property int $StartAt
 * @porperty int $EndAt
 */
class SearchHelperPartModel extends Model {
    public function __construct(){
        $this->ModelTableName = 'SearchHelperParts';
    }
}
?>