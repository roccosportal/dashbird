<?php

namespace Dashbird\Model\ModelTables;

class Comments extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Comments';
       
        $this->EntityName = 'Comment';
       
        $this->PrimaryKeyName = 'CommentId';
       
        $this->FieldDefinition['CommentId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Text'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['DateTime'] =  array ('Type' => 'Normal');
              
        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        $this->FieldDefinition['EntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Entries');
       
        $this->FieldDefinition['Entry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'EntryId');
        
        
        
        
        
    }
}