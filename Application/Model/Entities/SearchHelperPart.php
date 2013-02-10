<?php
namespace Dashbird\Model\Entities;
/**
 * @property int $SearchHelperPartId
 * @property int $PostId
 * @property string $Keyword
 * @property int $StartAt
 * @porperty int $EndAt
 */
class SearchHelperPart extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'SearchHelperParts';
    }
}
?>