<?php

namespace Dashbird\Controllers;


class PluginData extends Base {

    public function IndexAction() {
        // output of the basic html
        $this->ExecuteView();
    }

    public function ApiPluginDataGetAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
         $Name = $this->Request->GetGET('name');
         if($Name==null){
             return $this->ResponseWrongData();
         }
        
        $PluginDatasModelTable = \Pvik\Database\Generic\ModelTable::Get('PluginDatas');
        /* @var $PluginDatasModelTable \Dashbird\Model\ModelTables\PluginDatas */
        $PluginData = $PluginDatasModelTable->FindByNameAndUserId($Name, $this->GetUserId());
        
        if($PluginData==null){
            $PluginData = new \Dashbird\Model\Entities\PluginData();
            $PluginData->Name = $Name;
            $PluginData->Data = '{}';
            $PluginData->UserId = $this->GetUserId();
            $PluginData->Insert();
        }
        
        $this->ResponseSuccess($PluginData->ToArray());
        
        
    }
    
    
    public function ApiPluginDataSaveAction() {
        if (!$this->IsLoggedIn()) {
            return $this->ResponseNotLoggedIn();
        }
        
         $Name = $this->Request->GetPOST('name');
         $Data = $this->Request->GetPOST('data');
         if($Name==null||$Data==null){
             return $this->ResponseWrongData();
         }
         
      
        
        $PluginDatasModelTable = \Pvik\Database\Generic\ModelTable::Get('PluginDatas');
        /* @var $PluginDatasModelTable \Dashbird\Model\ModelTables\PluginDatas */
        $PluginData = $PluginDatasModelTable->FindByNameAndUserId($Name, $this->GetUserId());
        
        if($PluginData==null){
            $PluginData = new \Dashbird\Model\Entities\PluginData();
            $PluginData->Name = $Name;
            $PluginData->Data = '{}';
            $PluginData->UserId = $this->GetUserId();
            $PluginData->Insert();
        }
        
        $PluginData->Data = $Data;
        $PluginData->Update();
        
        $this->ResponseSuccess($PluginData->ToArray());
        
        
    }
    
    


}

?>