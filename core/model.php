<?php
class Model {
    /**
     * Filled in a child class
     * @var type 
     */
    protected $ModelTableName;
    protected $FieldData = array();

    public function Fill($Data = array()){
        // fill this class with the data
        foreach($Data as $FieldName => $Value){
            $this->SetFieldData($FieldName, $Value);
        }
        $ModelTable = $this->GetModelTable();
        $ModelTable->GetCache()->Store($this);
        return $this;
    }
    
    public function GetModelTableName(){
        return $this->ModelTableName;
    }

    public function GetModelTable(){
        return ModelTable::Get($this->ModelTableName);
    }

    public function  __get($FieldName) {
        $ModelTable  = $this->GetModelTable();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        
       
        if($Helper->FieldExists($FieldName)){
             switch ($Helper->GetFieldType($FieldName)) {
                case 'PrimaryKey':
                    return $this->GetFieldData($FieldName);
                    break;
                case 'Normal':
                    return $this->GetFieldData($FieldName);
                    break;
                case 'ForeignKey':
                    return $this->GetFieldData($FieldName);
                    break;
                case 'ForeignObject':
                    // search for the foreign key reference
                    $ForeignKeyFieldName = $Helper->GetForeignKeyFieldName($FieldName);
                    $ForeignKey = $this->GetFieldData($ForeignKeyFieldName);
                    
                    if($ForeignKey==null){
                        return null;
                    }
                    $ForeignModelTable = $Helper->GetModelTable($ForeignKeyFieldName);
                    return $ForeignModelTable->LoadByPrimaryKey($ForeignKey);  
                    break;
                case 'ManyForeignObjects':
                    $ForeignKeys = $this->GetFieldData($FieldName);

                    $ModelTable = $Helper->GetModelTable($FieldName);
                    if($ForeignKeys==null){
                        $ModelArray = new ModelArray();
                        $ModelArray->SetModelTable($ModelTable);
                        return $ModelArray;
                    }
                    if(isset($ForeignKeys)){
                         return $ModelTable->LoadByPrimaryKeys(explode(',', $ForeignKeys));
                    }
                    else {
                        throw new Exception('Foreign keys for '. $FieldName . ' not found.');
                    }
                    break;
            }
        }
        else {
            throw new Exception('Value ' . $FieldName . ' not found.');
        }
    }

    public function __set($FieldName, $Value) {
        

        $ModelTable  = $this->GetModelTable();
        //$DataDefinition = $ModelTable->GetDataDefinition();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        if($Helper->FieldExists($FieldName)){
             switch ($Helper->GetFieldType($FieldName)) {
                case 'PrimaryKey':
                     throw new Exception('The primary key is only readable: '. $Key);
                     break;
                case 'Normal':
                     //$this->Data[$Key] = $Value;
                     $this->SetFieldData($FieldName, $Value);
                     break;
                case 'ForeignKey':
                        
                        $PrimaryKey = $this->GetPrimaryKey();
                        if ($PrimaryKey != null) {
                                $this->GetModelTable()->GetCache()->DeleteForeignKeyReference($this, $FieldName);
                        }
                        $this->SetFieldData($FieldName, $Value);
                          if ($PrimaryKey != null) {
                                $this->GetModelTable()->GetCache()->InsertForeignKeyReference($this, $FieldName);
                        }
                    
                    break;
                case 'ForeignObject':
                    throw new Exception('The object is only readable: '. $Key);
                    break;
                case 'ManyForeignObjects':
                    throw new Exception('The list is only readable: '. $Key);
                    break;
            }
        }
        else {
            throw new Exception('Value ' . $Key . ' not found.');
        }
    }

    public function GetPrimaryKey(){
        $ModelTable  = $this->GetModelTable();
        $PrimaryKeyName = $ModelTable->GetPrimaryKeyName();
        if($PrimaryKeyName!=null){
            if($this->FieldDataExists($PrimaryKeyName)){
                return $this->GetFieldData($PrimaryKeyName);
            }
            else {
                return null;
            }
        }
         else {
            throw new Exception('The model '. get_class($this)  . ' has no primary key.');
        }
    }

    public function Insert(){
        return $this->GetModelTable()->Insert($this);
    }
    
    public function Update(){
        return $this->GetModelTable()->Update($this);
    }
    
    public function Delete(){
        return $this->GetModelTable()->Delete($this);
    }

    public function SetFieldData($FieldName, $Value){
        $this->FieldData[$FieldName] = $Value;
    }

    public function GetFieldData($FieldName){
        if(array_key_exists($FieldName,$this->FieldData)){
            return $this->FieldData[$FieldName];
        }
        else{
            return null;
        }
    }
    
    /**
     *
     * @param string $FieldName
     * @return bool
     */
    public function FieldDataExists($FieldName){
        if(isset($this->FieldData[$FieldName])){
             return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Return an array of the keys instead of the objects for a ManyForeignObjects field
     * @param string $FieldName
     * @return array 
     */
    public function GetKeys($FieldName){
        $ModelTable  = $this->GetModelTable();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        if(!$Helper->IsTypeManyForeignObjects($FieldName)){
            throw new Exception('The field must have the type ManyForeignObjects.');
        }
        $KeysString = $this->GetFieldData($FieldName);
        if($KeysString!=null){
            return explode(',', $KeysString);
        }else {
            return array();
        }
    }
}
