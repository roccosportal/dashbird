<?php
namespace Dashbird\Model\ModelTables;
class PostShares extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        $this->TableName = 'PostShares';
       
        $this->EntityName = 'PostShare';
       
        $this->PrimaryKeyName = 'PostShareId';
       
        $this->FieldDefinition['PostShareId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['PostId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Posts');
       
        $this->FieldDefinition['Post'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'PostId');
       
        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
        
    }
}