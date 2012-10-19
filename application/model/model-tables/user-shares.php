<?php
namespace Dashbird\Model\ModelTables;
class UserShares extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        $this->TableName = 'UserShares';
       
        $this->EntityName = 'UserShare';
       
        $this->PrimaryKeyName = 'UserShareId';
       
        $this->FieldDefinition['UserShareId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        $this->FieldDefinition['ConnectedUserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['ConnectedUser'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'ConnectedUserId');
        
        
        
        
    }
}