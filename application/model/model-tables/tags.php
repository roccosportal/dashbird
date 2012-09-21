<?php

namespace Dashbird\Model\ModelTables;

class Tags extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Tags';
       
        $this->EntityName= 'Tag';
       
        $this->PrimaryKeyName = 'TagId';
       
        $this->FieldDefinition['TagId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Title'] =  array ('Type' => 'Normal');

    }
}
