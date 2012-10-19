<?php
namespace Dashbird\Model\Entities;
class Module extends \Pvik\Database\Generic\Entity{
    protected $ModuleName; 
    
    public function __construct($ModuleName) {
        $this->ModuleName = $ModuleName;
    }
    
    /**
     *
     * @return DashboardEntry 
     */
    public function GetDashboardEntry(){
        $Query = new \Pvik\Database\Generic\Query('DashboardEntries');
        $Query->SetConditions('WHERE DashboardEntries.Module = "%s" AND DashboardEntries.ReferenceId = "%s"');
        $Query->AddParameter($this->ModuleName);
        $Query->AddParameter($this->GetPrimaryKey());
        return $Query->SelectSingle();
    }
}
?>
