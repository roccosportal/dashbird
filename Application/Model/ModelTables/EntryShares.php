<?php
namespace Dashbird\Model\ModelTables;
class EntryShares extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        $this->TableName = 'EntryShares';
       
        $this->EntityName = 'EntryShare';
       
        $this->PrimaryKeyName = 'EntryShareId';
       
        $this->FieldDefinition['EntryShareId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['EntryId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Entries');
       
        $this->FieldDefinition['Entry'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'EntryId');
       
        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
        
    }
}