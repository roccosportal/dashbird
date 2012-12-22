<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $UserId
 * @property string $Name
 * @property string $Password
 * @property \Pvik\Database\Generic\EntityArray $UserShares
 */
class User extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'Users';
    }

    public function ToArraySimple() {
        return array('userId' => $this->UserId,
            'name' => $this->Name);
    }
    
     public function ToArray() {
        return array('userId' => $this->UserId,
            'name' => $this->Name,
            'userShares' => $this->UserSharesToArray());
    }

    /**
     * Returns an array of user ids
     * @return array
     */
    public function UserSharesToArray() {
        $Array = array();
        foreach ($this->UserShares as $UserShare) {
            /* @var $UserShare \Dashbird\Model\Entities\UserShare */
            $Array[] = array('userId' => $UserShare->ConnectedUserId, 'name' => $UserShare->ConnectedUser->Name);
        }
        return $Array;
    }

}

?>