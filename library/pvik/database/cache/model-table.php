<?php

namespace Pvik\Database\Cache;

/**
 * Manages the cache for a model table
 */
class ModelTable {

    /**
     * Contains the already loaded entities.
     * @var array 
     */
    protected $Cache;

    /**
     * Contains a value that indicates if all Entities are loaded.
     * @var bool 
     */
    protected $IsLoadedAll;

    /**
     * Contains the EntityArray of all Entities if all are loaded.
     * @var \Pvik\Database\Generic\EntityArray
     */
    protected $EntityArrayAll;

    /**
     * Contains the ModelTable for this Cache
     * @var \Pvik\Database\Generic\ModelTable 
     */
    protected $ModelTable;

    /**
     * 
     * @param \Pvik\Database\Generic\ModelTable $ModelTable
     */
    public function __construct(\Pvik\Database\Generic\ModelTable $ModelTable) {
        $this->ModelTable = $ModelTable;
        $this->Cache = array();
        $this->IsLoadedAll = false;
        $this->EntityArrayAll = null;
    }

    /**
     * Returns all instances that are in the cache
     * @return \Pvik\Database\Generic\EntityArray
     */
    public function GetAllCacheInstances() {
        $Instances = new \Pvik\Database\Generic\EntityArray();
        $Instances->SetModelTable($this->ModelTable);
        foreach ($this->Cache as $Value) {
            $Instances->append($Value['Instance']);
        }
        return $Instances;
    }

    /**
     * Stores a instance of model into the cache.
     * @param \Pvik\Database\Generic\Entity $Instance 
     */
    public function Store(\Pvik\Database\Generic\Entity $Instance) {
        if ($Instance != null) {
            $PrimaryKey = $Instance->GetPrimaryKey();
            if ($PrimaryKey != '') {
                // store in cache
                $this->Cache[$PrimaryKey] = array(
                    'Instance' => $Instance,
                    'Timestamp' => microtime()
                );
            }
        }
    }

    /**
     * Loads a Model from cache by its primary key.
     * @param string $PrimaryKey
     * @return \Pvik\Database\Generic\Entity 
     */
    public function LoadByPrimaryKey($PrimaryKey) {
        if (!is_string($PrimaryKey) && !is_int($PrimaryKey)) {
            throw new \Exception('primary key must be a string or int');
        }
        if (isset($this->Cache[$PrimaryKey])) {
            return $this->Cache[$PrimaryKey]['Instance'];
        }
        return null;
    }

    /**
     * Indicates if all entities are loaded in cache from the ModelTable
     * @return bool
     */
    public function IsLoadedAll() {
        return $this->IsLoadedAll;
    }

    /**
     * Returns the list all entities if all are loaded into cache
     * @return \Pvik\Database\Generic\EntityArray
     */
    public function GetEntityArrayAll() {
        return $this->EntityArrayAll;
    }

    /**
     * Set the list of all entities that are loaded into the cache
     * @param \Pvik\Database\Generic\EntityArray $EntityArray
     */
    public function SetEntityArrayAll(\Pvik\Database\Generic\EntityArray $EntityArray) {
        $this->IsLoadedAll = true;
        $this->EntityArrayAll = $EntityArray;
    }

    /**
     * Updates the cache.
     * @param \Pvik\Database\Generic\Entity $Object
     */
    public function Insert(\Pvik\Database\Generic\Entity $Object) {
        // update Foreign Objects that are in cache
        $Helper = $this->ModelTable->GetFieldDefinitionHelper();
        foreach ($Helper->GetFieldList() as $FieldName) {
            if ($Helper->IsTypeForeignKey($FieldName)) {
                $this->InsertForeignKeyReference($Object, $FieldName);
            }
        }
        $this->Store($Object);
    }

    /**
     * Updates the reference from entities to the object by the current foreign key for a field name
     * @param \Pvik\Database\Generic\Entity $Object
     * @param string $FieldName
     */
    public function InsertForeignKeyReference(\Pvik\Database\Generic\Entity $Object, $FieldName) {
        //  get the key that refers to the foreign object (AuthorID  from a book)
        $Helper = $this->ModelTable->GetFieldDefinitionHelper();
        $ForeignKey = $Object->GetFieldData($FieldName);
        if (isset($ForeignKey) && $ForeignKey != 0) {
            $ForeignModelTable = $Helper->GetModelTable($FieldName);
            $ForeignObject = $ForeignModelTable->GetCache()->LoadByPrimaryKey($ForeignKey);  // look if object is in cache
            if ($ForeignObject != null) {
                $ForeignHelper = $ForeignModelTable->GetFieldDefinitionHelper();
                // find data field from type ManyForeignObjects that have a reference to this model table 
                foreach ($ForeignHelper->GetManyForeignObjectsFieldList() as $ForeignFieldName) {
                    if ($ForeignHelper->GetModelTableName($ForeignFieldName) == $this->ModelTable->GetModelTableName() // Author.Books is refering to BooksModelTable
                            && $ForeignHelper->GetForeignKeyFieldName($ForeignFieldName) == $FieldName) {  // Author.Books.ForeignKey is AuthorID
                        // add primary key from inserted object to list
                        $Old = $ForeignObject->GetFieldData($ForeignFieldName);
                        $New = $Old . ',' . $Object->GetPrimaryKey();
                        $ForeignObject->SetFieldData($ForeignFieldName, $New);

                        break;
                    }
                }
            }
        }
    }

    /**
     * Updates the cache.
     * @param \Pvik\Database\Generic\Entity $Object 
     */
    public function Delete(\Pvik\Database\Generic\Entity $Object) {
        // update foreign objects
        $Helper = $this->ModelTable->GetFieldDefinitionHelper();

        foreach ($Helper->GetFieldList() as $FieldName) {
            if ($Helper->IsTypeForeignKey($FieldName)) {
                $this->DeleteForeignKeyReference($Object, $FieldName);
            }
        }
    }

    /**
     * Deletes the reference from entities to the object by the current foreign key for a field name
     * @param \Pvik\Database\Generic\Entity $Object
     * @param string $FieldName
     */
    public function DeleteForeignKeyReference(\Pvik\Database\Generic\Entity $Object, $FieldName) {
        $Helper = $this->ModelTable->GetFieldDefinitionHelper();
        $ForeignModelTable = $Helper->GetModelTable($FieldName);
        //  get the key that refers to the foreign object (AuthorID  from a book)
        $ForeignKey = $Object->GetFieldData($FieldName);
        $ForeignObject = $ForeignModelTable->GetCache()->LoadByPrimaryKey($ForeignKey);
        // if object exist in cache and needs to be updated
        if ($ForeignObject != null) {
            // look through foreign model
            $ForeignHelper = $ForeignModelTable->GetFieldDefinitionHelper();
            foreach ($ForeignHelper->GetManyForeignObjectsFieldList() as $ForeignModelTableFieldName) {
                // searching for a ManyForeignObjects field with ForeignKey reference to this field
                if ($ForeignHelper->GetModelTableName($ForeignModelTableFieldName) == $this->ModelTable->GetModelTableName()  // Author.Books is refering to BooksModelTable
                        && $ForeignHelper->GetForeignKeyFieldName($ForeignModelTableFieldName) == $FieldName) {  // Author.Books.ForeignKey is AuthorID
                    $OldKeys = $ForeignObject->GetFieldData($ForeignModelTableFieldName);
                    // delete from old keys
                    $ForeignObject->SetFieldData($ForeignModelTableFieldName, str_replace($Object->GetPrimaryKey(), '', $OldKeys));
                    break;
                }
            }
        }
    }

}