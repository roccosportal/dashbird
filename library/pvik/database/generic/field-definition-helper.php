<?php

namespace Pvik\Database\Generic;

/**
 * A helper class for the field definition of model tables
 */
class FieldDefinitionHelper {

    /**
     * Field definition of an model table.
     * @var array 
     */
    protected $FieldDefinition;

    /**
     * A list of fields in the current field definition.
     * @link GetFieldList()
     * @var \ArrayObject 
     */
    protected $FieldList;

    /**
     * A list of fields in the current field definition of type ManyForeignObjects.
     * @link GetManyForeignObjectsFieldList()
     * @var \ArrayObject 
     */
    protected $ManyForeignObjectsFieldList;

    /**
     * A list of fields in the current field definition of type ForeignKey.
     * @link GetForeignKeysFieldList()
     * @var \ArrayObject 
     */
    protected $ForeignKeysFieldList;

    /**
     * A list of fields in the current field definition of type ForeignObject.
     * @link GetForeignObjectsFieldList()
     * @var \ArrayObject 
     */
    protected $ForeignObjectsFieldList;

    /**
     * Contains the origin ModelTable of the field definition.
     * @var ModelTable 
     */
    protected $OriginModelTable;

    /**
     *
     * @param array $FieldDefinition
     * @param ModelTable $OriginModelTable 
     */
    public function __construct($FieldDefinition, ModelTable $OriginModelTable) {
        $this->FieldDefinition = $FieldDefinition;
        $this->OriginModelTable = $OriginModelTable;
    }

    /**
     * Returns the origin ModelTable of the field definition
     * @return ModelTable 
     */
    public function GetOriginModelTable() {
        return $this->OriginModelTable;
    }

    /**
     * Returns the field definition of the ModelTable.
     * @return type 
     */
    public function GetFieldDefinition() {
        return $this->FieldDefinition;
    }

    /**
     * Returns a list of fields in the current field definition.
     * Saves the result and just loop once trough the field definition to get all fields.
     * @return ArrayObject 
     */
    public function GetFieldList() {
        // just run it once and save results
        if ($this->FieldList == null) {
            $this->FieldList = new \ArrayObject();
            foreach ($this->GetFieldDefinition() as $FieldName => $Definition) {
                $this->FieldList->append($FieldName);
            }
        }
        return $this->FieldList;
    }

    /**
     * Returns a list of fields in the current field definition of type ManyForeignObjects.
     * Saves the result and just loop once trough the field definition to get all fields.
     * @return \ArrayObject
     */
    public function GetManyForeignObjectsFieldList() {
        // just run it once and save results
        if ($this->ManyForeignObjectsFieldList == null) {
            $this->ManyForeignObjectsFieldList = new \ArrayObject();
            foreach ($this->GetFieldList() as $FieldName) {
                if ($this->IsTypeManyForeignObjects($FieldName)) {
                    $this->ManyForeignObjectsFieldList->append($FieldName);
                }
            }
        }
        return $this->ManyForeignObjectsFieldList;
    }

    /**
     * Returns a list of fields in the current field definition of type ForeignKey.
     * Saves the result and just loop once trough the field definition to get all fields.
     * @return \ArrayObject 
     */
    public function GetForeignKeysFieldList() {
        // just run it once and save results
        if ($this->ForeignKeysFieldList == null) {
            $this->ForeignKeysFieldList = new \ArrayObject();
            foreach ($this->GetFieldList() as $FieldName) {
                if ($this->IsTypeForeignKey($FieldName)) {
                    $this->ForeignKeysFieldList->append($FieldName);
                }
            }
        }
        return $this->ForeignKeysFieldList;
    }

    /**
     * Returns a list of fields in the current field definition of type ForeignObject.
     * Saves the result and just loop once trough the field definition to get all fields.
     * @return \ArrayObject 
     */
    public function GetForeignObjectsFieldList() {
        // just run it once and save results
        if ($this->ForeignObjectsFieldList == null) {
            $this->ForeignObjectsFieldList = new \ArrayObject();
            foreach ($this->GetFieldList() as $FieldName) {
                if ($this->IsTypeForeignObject($FieldName)) {
                    $this->ForeignObjectsFieldList->append($FieldName);
                }
            }
        }
        return $this->ForeignObjectsFieldList;
    }

    /**
     * Checks if a field exists in the field definition.
     * @param string $FieldName
     * @return bool 
     */
    public function FieldExists($FieldName) {
        if (isset($this->FieldDefinition[$FieldName])) {
            return true;
        }
        return false;
    }

    /**
     * Returns the definition array of an field or null if the field doesn't exists.
     * @param string $FieldName
     * @return array 
     */
    public function GetField($FieldName) {
        if ($this->FieldExists($FieldName)) {
            return $this->FieldDefinition[$FieldName];
        }
        return null;
    }

    /**
     * Checks if a value field for a definition field is set up.
     * @param string $FieldName
     * @param string $ValueName
     * @return bool 
     */
    public function HasFieldValue($FieldName, $ValueName) {
        $Field = $this->GetField($FieldName);
        if ($Field != null && isset($Field[$ValueName])) {
            return true;
        }
        return false;
    }

    /**
     * Returns the value of a value field in a field definition or null.
     * @param string $FieldName
     * @param string $ValueName
     * @return string 
     */
    public function GetFieldValue($FieldName, $ValueName) {
        if ($this->HasFieldValue($FieldName, $ValueName)) {
            $Field = $this->GetField($FieldName);
            return $Field[$ValueName];
        }
        return null;
    }

    /**
     * Returns the type of a field or null.
     * @param string $FieldName
     * @return string 
     */
    public function GetFieldType($FieldName) {
        return $this->GetFieldValue($FieldName, 'Type');
    }

    /**
     * Checks if the type is ForeignKey of a field.
     * @param string $FieldName
     * @return bool 
     */
    public function IsTypeForeignKey($FieldName) {
        $FieldType = $this->GetFieldType($FieldName);
        if ($FieldType == 'ForeignKey') {
            return true;
        }
        return false;
    }

    /**
     * Checks if the type is PrimaryKey of a field.
     * @param string $FieldName
     * @return bool 
     */
    public function IsTypePrimaryKey($FieldName) {
        $FieldType = $this->GetFieldType($FieldName);
        if ($FieldType == 'PrimaryKey') {
            return true;
        }
        return false;
    }

    /**
     * Checks if the type is ForeignObject of a field.
     * @param string $FieldName
     * @return bool 
     */
    public function IsTypeForeignObject($FieldName) {
        $FieldType = $this->GetFieldType($FieldName);
        if ($FieldType == 'ForeignObject') {
            return true;
        }
        return false;
    }

    /**
     * Checks if the type is ManyForeignObjects of a field.
     * @param string $FieldName
     * @return bool 
     */
    public function IsTypeManyForeignObjects($FieldName) {
        $FieldType = $this->GetFieldType($FieldName);
        if ($FieldType == 'ManyForeignObjects') {
            return true;
        }
        return false;
    }

    /**
     * Returns the ForeignKey field name for an field of type ForeignObject or ManyForeignObjects.
     * @param string $FieldName
     * @return string 
     */
    public function GetForeignKeyFieldName($FieldName) {
        if ($this->IsTypeForeignObject($FieldName) || $this->IsTypeManyForeignObjects($FieldName)) {
            return $this->GetFieldValue($FieldName, 'ForeignKey');
        } else {
            throw new \Exception('Only the types ForeignObject and ManyForeignObjects have a ModelTable.');
        }
    }

    /**
     * Returns the name of the ModelTable value field for the definition field or null.
     * @param string $FieldName
     * @return string 
     */
    public function GetModelTableName($FieldName) {
        $Field = $this->GetField($FieldName);
        if ($Field != null) {
            return $this->GetFieldValue($FieldName, 'ModelTable');
        }
        return null;
    }

    /**
     * Returns the ModelTable for a ForeignKey, ManyForeignObjects or ForeignObject field.
     * @param string $FieldName
     * @return ModelTable 
     */
    public function GetModelTable($FieldName) {
        if ($this->IsTypeForeignKey($FieldName) || $this->IsTypeManyForeignObjects($FieldName)) {
            $ModelTableName = $this->GetModelTableName($FieldName);

            return ModelTable::Get($ModelTableName);
        } elseif ($this->IsTypeForeignObject($FieldName)) {
            $ForeignKeyFieldName = $this->GetForeignKeyFieldName($FieldName);
            $ModelTableName = $this->GetModelTableName($ForeignKeyFieldName);


            return ModelTable::Get($ModelTableName);
        } else {
            throw new \Exception('Only the types ForeignKey, ManyForeignObjects, ForeignObject have a ModelTable.');
        }
    }

    /**
     * Returns the ModelTable name of a field of type ForeignObject or null.
     * @param string $FieldName
     * @return string 
     */
    public function GetModelTableNameForForeignObject($FieldName) {
        if ($this->IsTypeForeignObject($FieldName)) {
            $ForeignKeyFieldName = $this->GetFieldValue($FieldName, 'ForeignKey');
            if ($ForeignKeyFieldName != null && $ForeignKeyFieldName != '') {
                return $this->GetModelTableName($ForeignKeyFieldName);
            }
        } else {
            throw new \Exception($FieldName . ' is not an ForeignObject.');
        }
        return null;
    }

    /**
     * Checks is a PrimaryKey is set up as Guid.
     * @param string $FieldName
     * @return bool 
     */
    public function IsGuid($FieldName) {
        if ($this->IsTypePrimaryKey($FieldName)) {
            $Value = $this->GetFieldValue($FieldName, 'IsGuid');
            if ($Value === true) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception('Only a primary key can have the field IsGUID.');
        }
    }

}