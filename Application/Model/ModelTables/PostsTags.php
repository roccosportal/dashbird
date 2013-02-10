<?php

namespace Dashbird\Model\ModelTables;
class PostsTags extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'PostsTags';
       
        $this->EntityName = 'PostsTags';
       
        $this->PrimaryKeyName = 'PostsTagsId';
       
        $this->FieldDefinition['PostsTagsId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['PostId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Posts');
       
        $this->FieldDefinition['Post'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'PostId');
        
        $this->FieldDefinition['TagId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Tags');
       
        $this->FieldDefinition['Tag'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'TagId');
        

    }
}