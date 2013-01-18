<?php

namespace Pvik\Database\Generic;

/**
 * Represents a row in a table as an object
 */
class Entity {

    /**
     * Contains the name of the model table
     * @var string 
     */
    protected $ModelTableName;

    /**
     * Contains the data of the fields
     * @var array 
     */
    protected $FieldData = array();

    /**
     * Fills object with the given data array.
     * And stores the object to the cache
     * @param type $Data
     * @return \Pvik\Database\Generic\Entity
     */
    public function Fill($Data = array()) {
        // fill this class with the data
        foreach ($Data as $FieldName => $Value) {
            $this->SetFieldData($FieldName, $Value);
        }
        $ModelTable = $this->GetModelTable();
        $ModelTable->GetCache()->Store($this);
        return $this;
    }

    /**
     * Returns the name of the model table.
     * @return string
     */
    public function GetModelTableName() {
        return $this->ModelTableName;
    }

    /**
     * Returns the model table for this entity
     * @return ModelTable
     */
    public function GetModelTable() {
        return ModelTable::Get($this->ModelTableName);
    }

    /**
     * Magic method that allows us to use the field data as properties.
     * Converts foreign keys to objects.
     * @param string $FieldName
     * @return mixed
     * @throws \Exception
     */
    public function __get($FieldName) {
        $ModelTable = $this->GetModelTable();
        $Helper = $ModelTable->GetFieldDefinitionHelper();


        if ($Helper->FieldExists($FieldName)) {
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

                    if ($ForeignKey == null) {
                        return null;
                    }
                    $ForeignModelTable = $Helper->GetModelTable($ForeignKeyFieldName);
                    return $ForeignModelTable->LoadByPrimaryKey($ForeignKey);
                    break;
                case 'ManyForeignObjects':
                    $ForeignKeys = $this->GetFieldData($FieldName);

                    $ModelTable = $Helper->GetModelTable($FieldName);
                    if ($ForeignKeys == null) {
                        $EntityArray = new EntityArray();
                        $EntityArray->SetModelTable($ModelTable);
                        return $EntityArray;
                    }
                    if (isset($ForeignKeys)) {
                        return $ModelTable->LoadByPrimaryKeys(explode(',', $ForeignKeys));
                    } else {
                        throw new \Exception('Foreign keys for ' . $FieldName . ' not found.');
                    }
                    break;
            }
        } else {
            throw new \Exception('Value ' . $FieldName . ' not found.');
        }
    }

    /**
     * Magic method that allows us to set the field data as properties.
     * @param string $FieldName
     * @param mixed $Value
     * @throws \Exception
     */
    public function __set($FieldName, $Value) {


        $ModelTable = $this->GetModelTable();
        //$DataDefinition = $ModelTable->GetDataDefinition();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        if ($Helper->FieldExists($FieldName)) {
            switch ($Helper->GetFieldType($FieldName)) {
                case 'PrimaryKey':
                    throw new \Exception('The primary key is only readable: ' . $FieldName);
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
                    throw new \Exception('The object is only readable: ' . $FieldName);
                    break;
                case 'ManyForeignObjects':
                    throw new \Exception('The list is only readable: ' . $FieldName);
                    break;
            }
        } else {
            throw new \Exception('Value ' . $FieldName . ' not found.');
        }
    }

    /**
     * Returns the primary key
     * @return mixed
     * @throws \Exception
     */
    public function GetPrimaryKey() {
        $ModelTable = $this->GetModelTable();
        $PrimaryKeyName = $ModelTable->GetPrimaryKeyName();
        if ($PrimaryKeyName != null) {
            if ($this->FieldDataExists($PrimaryKeyName)) {
                return $this->GetFieldData($PrimaryKeyName);
            } else {
                return null;
            }
        } else {
            throw new \Exception('The model ' . get_class($this) . ' has no primary key.');
        }
    }

    /**
     * Inserts an entity to the database.
     * @return string primary key
     */
    public function Insert() {
        return $this->GetModelTable()->Insert($this);
    }

    /**
     * Updates an entity on the database
     * @return mixed
     */
    public function Update() {
        return $this->GetModelTable()->Update($this);
    }

    /**
     * Deletes an entity on the database
     * @return mixed
     */
    public function Delete() {
        return $this->GetModelTable()->Delete($this);
    }

    /**
     * Set the a field data value without checking if the value is correct.
     * @param string $FieldName
     * @param mixed $Value
     */
    public function SetFieldData($FieldName, $Value) {
        $this->FieldData[$FieldName] = $Value;
    }

    /**
     * Returns the field data value without converting them as in __get()
     * @param string $FieldName
     * @return mixed
     */
    public function GetFieldData($FieldName) {
        if (array_key_exists($FieldName, $this->FieldData)) {
            return $this->FieldData[$FieldName];
        } else {
            return null;
        }
    }

    /**
     * Checks if field data value exists
     * @param string $FieldName
     * @return bool
     */
    public function FieldDataExists($FieldName) {
        return (isset($this->FieldData[$FieldName]));
    }

    /**
     * Return an array of the keys instead of the objects for a ManyForeignObjects field
     * @param string $FieldName
     * @return array 
     */
    public function GetKeys($FieldName) {
        $ModelTable = $this->GetModelTable();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        if (!$Helper->IsTypeManyForeignObjects($FieldName)) {
            throw new \Exception('The field must have the type ManyForeignObjects.');
        }
        $KeysString = $this->GetFieldData($FieldName);
        if ($KeysString != null) {
            return explode(',', $KeysString);
        } else {
            return array();
        }
    }

}
