<?php
class LinksModelTable extends ModelTable {
    public function __construct(){
        // define the table name
        $this->TableName = 'Links';
       
        $this->ModelName = 'Link';
       
        $this->PrimaryKeyName = 'LinkId';
       
        $this->FieldDefinition['LinkId'] = array ('Type' => 'PrimaryKey');

        $this->FieldDefinition['Link'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['IsImage'] =  array ('Type' => 'Normal');

        $this->FieldDefinition['UserId'] = array('Type' => 'ForeignKey', 'ModelTable' => 'Users');
       
        $this->FieldDefinition['User'] = array('Type' => 'ForeignObject', 'ForeignKey' => 'UserId');
        
        
        
        
    }
}

Core::Depends('~/model/module-model.php');
/**
 * @property int $LinkId
 * @property string $Link
 * @property boolean $IsImage
 * @property int $UserId
 * @porperty UserModel $User
 */
class LinkModel extends ModuleModel {
    public function __construct(){
        parent::__construct('Link');
        $this->ModelTableName = 'Links';
    }

    public function ToArray(){
        return array (
            "linkId" => $this->LinkId,
            "link" => $this->Link,
            "isImage" => $this->IsImage);
    }
    

}
?>