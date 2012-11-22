<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $EntryShareId
 * @property int $UserId
 * @property User $User
 * @property int $EntryId
 * @property Entry $Entry
 */
class EntryShare extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'EntryShares';
    }
}
?>