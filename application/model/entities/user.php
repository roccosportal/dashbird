<?php

namespace Dashbird\Model\Entities;
/**
 * @property int $UserId
 * @property string $Name
 * @property string $Password
 * @property \Pvik\Database\Generic\EntityArray $Links
 * @property \Pvik\Database\Generic\EntityArray $Notes
 * @property \Pvik\Database\Generic\EntityArray $UserShares
 */
class User extends \Pvik\Database\Generic\Entity {
    public function __construct(){
        $this->ModelTableName = 'Users';
    }
    
    /**
     * Returns an array of user ids
     * @return array
     */
    public function UserSharesToArray() {
        $Array = array();
        foreach ($this->UserShares as $UserShare) {
            /* @var $UserShare \Dashbird\Model\Entities\UserShare */
            $Array[] = array ('userId' => $UserShare->ConnectedUserId, 'name' => $UserShare->ConnectedUser->Name);
        }
        return $Array;
    }
}
?>