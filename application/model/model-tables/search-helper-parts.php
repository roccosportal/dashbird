<?php

namespace Dashbird\Model\ModelTables;

class SearchHelperParts extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'SearchhelperParts';
       
        $this->EntityName = 'SearchHelperPart';
       
        $this->PrimaryKeyName = 'SearchHelperPartId';
       
        $this->FieldDefinition['SearchHelperPartId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['EntryId'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['Keyword'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['StartAt'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['EndAt'] =  array ('Type' => 'Normal');

    }
}
