<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $UserId
 * @property string $Name
 * @property string $Password
 * @porperty ModelArray $Links
 * @porperty ModelArray $Notes
 */
class User extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'Users';
    }
}
?>