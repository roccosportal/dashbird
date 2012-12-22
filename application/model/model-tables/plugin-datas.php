<?php

namespace Dashbird\Model\ModelTables;

class PluginDatas extends \Pvik\Database\Generic\ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'PluginDatas';
       
        $this->EntityName = 'PluginData';
       
        $this->PrimaryKeyName = 'PluginDataId';
       
        $this->FieldDefinition['PluginDataId'] = array ('Type' => 'PrimaryKey');
        
        $this->FieldDefinition['Name'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['Data'] =  array ('Type' => 'Normal');
        
        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');

        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
         
    }
    
    /**
     * 
     * @param string $Name
     * @return \Dashbird\Model\Entities\Plugin
     */
    public function FindByNameAndUserId($Name, $UserId){
         $Query = new \Pvik\Database\Generic\Query('PluginDatas');
         $Query->SetConditions('WHERE PluginDatas.Name = "%s" AND PluginDatas.UserId = "%s"');
         $Query->AddParameter($Name);
         $Query->AddParameter($UserId);
         return $Query->SelectSingle();
    }
}