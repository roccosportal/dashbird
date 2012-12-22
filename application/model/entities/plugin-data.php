<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $PluginDataId
 * @property string $Name
 * @property string $Data
 * @property int $UserId
 * @property User $User
 */
class PluginData extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'PluginDatas';
    }
    
    public function ToArray(){
        return array(
            'pluginDataId' => $this->PluginDataId,
            'data' => json_decode($this->Data),
            'name' => $this->Name
        );
    }


}
