<?php

namespace Dashbird\Model\ModelTables;

class Links extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Links';
       
        $this->EntityName = 'Link';
       
        $this->PrimaryKeyName = 'LinkId';
       
        $this->FieldDefinition['LinkId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Link'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['IsImage'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
    }
}