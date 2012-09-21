<?php

namespace Dashbird\Model\ModelTables;

class Notes extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Notes';
       
        $this->EntityName = 'Note';
       
        $this->PrimaryKeyName = 'NoteId';
       
        $this->FieldDefinition['NoteId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Text'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
    }
}