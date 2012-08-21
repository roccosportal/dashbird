<?php

class ModuleModel extends Model{
    protected $ModuleName; 
    
    public function __construct($ModuleName) {
        $this->ModuleName = $ModuleName;
    }
    
    /**
     *
     * @return DashboardEntryModel 
     */
    public function GetDashboardEntry(){
        $Query = new Query('DashboardEntries');
        $Query->SetConditions('WHERE DashboardEntries.Module = "%s" AND DashboardEntries.ReferenceId = "%s"');
        $Query->AddParameter($this->ModuleName);
        $Query->AddParameter($this->GetPrimaryKey());
        return $Query->SelectSingle();
    }
}
?>
