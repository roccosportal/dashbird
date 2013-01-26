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
        
        $this->FieldDefinition['UserShares'] = array ('Type' => 'ManyForeignObjects', 'ModelTable' => 'UserShares','ForeignKey' => 'UserId');
         
    }
    
    /**
     * 
     * @param string $Name
     * @return \Dashbird\Model\Entities\User
     */
    public function FindByName($Name){
         $Query = new \Pvik\Database\Generic\Query('Users');
         $Query->SetConditions('WHERE Users.Name = "%s"');
         $Query->AddParameter($Name);
         return $Query->SelectSingle();
    }
}