<?php

namespace Dashbird\Model\ModelTables;

class Users extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Users';
       
        $this->EntityName = 'User';
       
        $this->PrimaryKeyName = 'UserId';
       
        $this->FieldDefinition['UserId'] = array ('Type' => 'PrimaryKey');
        
        $this->FieldDefinition['Name'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['Password'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['Links'] = array ('Type' => 'ManyForeignObjects', 'ModelTable' => 'Links','ForeignKey' => 'UserId');

        $this->FieldDefinition['Notes'] = array ('Type' => 'ManyForeignObjects', 'ModelTable' => 'Notes','ForeignKey' => 'UserId');
    }
}