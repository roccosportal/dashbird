<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $UserShareId
 * @property int $UserId
 * @property User $User
 * @property int $ConnectedUserId
 * @property User $ConnectedUser
 */
class UserShare extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'UserShares';
    }

}

