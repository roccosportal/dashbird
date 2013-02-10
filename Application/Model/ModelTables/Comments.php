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
        
        $this->FieldDefinition['PostId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Posts');
       
        $this->FieldDefinition['Post'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'PostId');
        
        
        
        
        
    }
}