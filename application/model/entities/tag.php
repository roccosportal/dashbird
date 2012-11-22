<?php

namespace Dashbird\Model\Entities;

/**
 * @property int $TagId
 * @property string $Title
 */
class Tag extends \Pvik\Database\Generic\Entity {

    public function __construct() {
        $this->ModelTableName = 'Tags';
    }

    public function ToArray() {
        return array(
            'title' => $this->title
        );
    }

}
