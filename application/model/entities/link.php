<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $LinkId
 * @property string $Link
 * @property boolean $IsImage
 * @property int $UserId
 * @porperty UserModel $User
 */
class Link extends Module {
    public function __construct(){
        parent::__construct('Link');
        $this->ModelTableName = 'Links';
    }

    public function ToArray(){
        return array (
            "linkId" => $this->LinkId,
            "link" => $this->Link,
            "isImage" => $this->IsImage ? '1' : '0');
    }
    

}
?>