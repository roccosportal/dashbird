<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $PostShareId
 * @property int $UserId
 * @property User $User
 * @property int $PostId
 * @property Post $Post
 */
class PostShare extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'PostShares';
    }
}
?>