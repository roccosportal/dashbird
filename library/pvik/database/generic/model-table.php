<?php

namespace Pvik\Database\Generic;

use Pvik\Core\Config;
use Pvik\Database\Generic\Query;
use Pvik\Database\SQL\Builder;
use Pvik\Database\SQL\Manager;

/**
 * Represents a database table
 */
class ModelTable {

    // -- static --
    /**
     * Contains the instances of loaded ModelTables
     * @var array 
     */
    protected static $ModelTables = array();

    /**
     * Gets a ModelTable instance or creates a new one if a instance doesn't exsists yet.
     * @return ModelTable
     */
    public static function Get($ModelTableName) {
        if (!is_string($ModelTableName)) {
            throw new \Exception('ModelTableName must be a string.');
        }
        // instance already stored
        if (isset(self::$ModelTables[$ModelTableName])) {
            return self::$ModelTables[$ModelTableName];
        } else {
            // create new instance

            $ClassName = $ModelTableName;
            if ($ClassName[0] !== '\\') {
                $ClassName = Config::$Config['DefaultNamespace'] . Config::$Config['DefaultNamespaceModelTable'] . '\\' . $ClassName;
            }

            $Instance = new $ClassName();
            self::$ModelTables[$ModelTableName] = $Instance;
            return $Instance;
        }
    }

    // -- INSTANCE --
    /**
     * Contains the field definition array.
     * Filled in a child class.
     * @var array 
     */
    protected $FieldDefinition;

    /**
     * Contains the real table name.
     * Filled in a child class.
     * @var string 
     */
    protected $TableName;

    /**
     * Contains the name of the primary key.
     * Whether filled in a chilled class or filled after running the method GetPrimaryKeyName().
     * @var string 
     */
    protected $PrimaryKeyName;

    /**
     *  Contains the cache class
     * @var CacheModelTable 
     */
    protected $Cache;

    /**
     * Contains the name of the ModelTable.
     * @var type 
     */
    protected $ModelTableName;

    /**
     * A instance of the field definition helper.
     * @var FieldDefinitionHelper 
     */
    protected $FieldDefinitionHelper;

    /**
     * Contains the name of the Model that belongs to this ModelTable.
     * Filled in a child class.
     * @var string 
     */
    protected $EntityName;

    /**
     *
     * @return Pvik\Database\Cache\ModelTable
     */
    public function GetCache() {
        if ($this->Cache === null) {
            $this->Cache = new \Pvik\Database\Cache\ModelTable($this);
        }
        return $this->Cache;
    }

    /**
     * Returns the PrimaryKey name.
     * @return string 
     */
    public function GetPrimaryKeyName() {
        if ($this->PrimaryKeyName != null) {
            return $this->PrimaryKeyName;
        } else {
            $Helper = $this->GetFieldDefinitionHelper();
            // try to find primary key
            foreach ($Helper->GetFieldList() as $FieldName) {
                if ($Helper->IsTypePrimaryKey($FieldName)) {
                    // primary key found
                    $this->PrimaryKeyName = $Key;
                    return $this->PrimaryKeyName;
                }
            }
        }
        throw new \Exception('No primary key found.');
    }

    /**
     * Returns the real table name.
     * @return string 
     */
    public function GetTableName() {
        return $this->TableName;
    }

    /**
     * Returns the name of the Model that belongs to this ModelTable.
     * @return string 
     */
    public function GetEntityName() {
        if (empty($this->EntityName)) {
            throw new \Exception('EntityName not set up for ' . get_class($this));
        }
        return $this->EntityName;
    }

    /**
     * Returns the name of the Model class that belongs to this ModelTable.
     * @return string 
     */
    public function GetEntityClassName() {
        $EntityClassName = $this->GetEntityName();
        if ($EntityClassName[0] !== '\\') {
            $EntityClassName = Config::$Config['DefaultNamespace'] . Config::$Config['DefaultNamespaceEntity'] . '\\' . $EntityClassName;
        }
        return $EntityClassName;
    }

    /**
     * Returns the name of the ModelTable withouth the suffix ModelTable.
     * @return string 
     */
    public function GetModelTableName() {
        if ($this->ModelTableName == null) {
            $Class = explode('\\', get_class($this));
            $this->ModelTableName = end($Class);
        }
        return $this->ModelTableName;
    }

    /**
     * Returns a instance of a field definition helper or creates a new one.
     * @return FieldDefinitionHelper 
     */
    public function GetFieldDefinitionHelper() {
        // just use one instance
        if ($this->FieldDefinitionHelper == null) {
            $this->FieldDefinitionHelper = new FieldDefinitionHelper($this->FieldDefinition, $this);
        }
        return $this->FieldDefinitionHelper;
    }

    /**
     * Select a ModelArray from the database.
     * @param Query $Query
     * @return EntityArray 
     */
    public function Select(Query $Query) {
        $QueryString = Builder::GetInstance()->CreateSelectStatement($this, $Query->GetConditions(), $Query->GetOrderBy());
        $ModelArray = Manager::GetInstance()->FillList($this, $QueryString, $Query->GetParameters());
        // if the query don't have any conditions we have loaded all objects and can save the complete list 
        // in chache
        if ($Query->GetConditions() == '') {
            $this->Cache->SetEntityArrayAll($ModelArray);
        }
        return $ModelArray;
    }

    /**
     * Select a single Model from the database.
     * @param Query $Query
     * @return Entity 
     */
    public function SelectSingle(Query $Query) {
        $List = $this->Select($Query);
        if ($List->count() > 0) {
            // return first element
            return $List[0];
        } else {
            return null;
        }
    }

    /**
     * Select all Models from the database.
     * 
     * @return EntityArray 
     */
    public function SelectAll() {
        // creating a new query without any conditions
        $Query = new Query($this->GetModelTableName());
        return $this->Select($Query);
    }

    /**
     * Insert a Entity to the database and updates the cache.
     * Returns the primary key.
     * @param Entity $Object
     * @return string 
     */
    public function Insert(Entity $Object) {
        $PrimaryKey = $Object->GetPrimaryKey();
        If (empty($PrimaryKey)) {
            // insert into database
            $Insert = Builder::GetInstance()->CreateInsertStatement($this, $Object);
            $PrimaryKey = Manager::GetInstance()->InsertWithParameters($Insert['SQL'], $Insert['Parameters']);
            // update primarykey in object
            $Object->SetFieldData($this->GetPrimaryKeyName(), $PrimaryKey);
            $this->GetCache()->Insert($Object);
            return $PrimaryKey;
        } else {
            throw new \Exception('The primarykey of this object is already set and the object can\'t be inserted.');
        }
    }

    /**
     * Updates a Model on the database.
     * @param Entity $Object
     * @return mixed 
     */
    public function Update(Entity $Object) {
        $PrimaryKey = $Object->GetPrimaryKey();
        if (!empty($PrimaryKey)) {
            // create update statement
            $Update = Builder::GetInstance()->CreateUpdateStatement($this, $Object);
            return Manager::GetInstance()->UpdateWithParameters($Update['SQL'], $Update['Parameters']);
        } else {
            throw new \Exception('Primary key isn\'t set, can\'t update');
        }
    }

    /**
     * Deletes a Entity from the database and updates the cache.
     * @param Entity $Object 
     */
    public function Delete(Entity $Object) {
        $PrimaryKey = $Object->GetPrimaryKey();
        if ($PrimaryKey != null) {
            $QueryString = Builder::GetInstance()->CreateDeleteStatement($this);
            $Parameters = array();
            array_Push($Parameters, $PrimaryKey);
            Manager::GetInstance()->DeleteWithParameters($QueryString, $Parameters);
            $this->GetCache()->Delete($Object);
        } else {
            throw new \Exception('Can\'t delete model cause it has no primary key.');
        }
    }

    /**
     * Select a EntityArray from database by primary keys.
     * @param array $PrimaryKeys
     * @return EntityArray 
     */
    public function SelectByPrimaryKeys(array $PrimaryKeys) {
        $Values = Builder::GetInstance()->CreateWhereStatementByPrimaryKeys($this, $PrimaryKeys);
        $Query = new Query($this->GetModelTableName());
        $Query->SetConditions($Values['SQL']);
        foreach ($Values['Parameters'] as $Parameter) {
            $Query->AddParameter($Parameter);
        }
        return $this->Select($Query);
    }

    /**
     * Select a EntityArray from database by foreign keys through a sql IN statement.
     * Example: SELECT * FROM Books WHERE Books.AuthorID IN ('1', '2', '3')
     * @param string $FieldName
     * @param array $Keys
     * @return EntityArray 
     */
    public function SelectByForeignKeys($FieldName, array $Keys) {
        $InStatementResult = Builder::GetInstance()->CreateInStatementForKeys($this, $FieldName, $Keys);
        $Query = new Query($this->GetModelTableName());
        $Query->SetConditions("WHERE " . $InStatementResult['SQL'] . " ");
        foreach ($InStatementResult['Parameters'] as $Parameter) {
            $Query->AddParameter($Parameter);
        }
        return $this->Select($Query);
    }

    /**
     * Select a Entity from database by its primary key.
     * @param string $PrimaryKey
     * @return Entity 
     */
    public function SelectByPrimaryKey($PrimaryKey) {
        $Conditions = Builder::GetInstance()->CreateWhereStatementByPrimaryKey($this);
        $Query = new Query($this->GetModelTableName());
        $Query->SetConditions($Conditions);
        $Query->AddParameter($PrimaryKey);
        return $this->SelectSingle($Query);
    }

    /**
     * Returns a Entity from cache or from database by its primary key.
     * @param string $PrimaryKey
     * @return Entity 
     */
    public function LoadByPrimaryKey($PrimaryKey) {
        if (!is_string($PrimaryKey) && !is_int($PrimaryKey)) {
            throw new \Exception('primary key must be a string or int');
        }
        if ($PrimaryKey === 0 || $PrimaryKey === null || $PrimaryKey === '') {
            return null;
        } else {
            // search in cache
            $CacheInstance = $this->GetCache()->LoadByPrimaryKey($PrimaryKey);
            if ($CacheInstance != null) {
                return $CacheInstance;
            } else {
                // get via select
                return $this->SelectByPrimaryKey($PrimaryKey);
            }
        }
    }

    /**
     * Returns a EntityArray from cache or database by the primary keys.
     * @param array $Keys
     * @return EntityArray 
     */
    public function LoadByPrimaryKeys(array $Keys) {
        // convert to array
        $List = new EntityArray();
        $List->SetModelTable($this);
        $LoadKeys = array();
        foreach ($Keys as $Key) {
            if (!empty($Key)) {
                // search in cache
                $Item = $this->GetCache()->LoadByPrimaryKey($Key);
                // mark for loading later
                if ($Item == null) {
                    array_push($LoadKeys, $Key);
                } else {
                    $List->append($Item);
                }
            }
        }
        if (!empty($LoadKeys)) {
            // now load every data we didn't find in the cache
            $LoadedItems = $this->SelectByPrimaryKeys($LoadKeys);
            foreach ($LoadedItems as $LoadedItem) {
                $List->append($LoadedItem);
            }
        }
        return $List;
    }

    /**
     *  Returns all Entities from cache or from database.
     *  @return EntityArray
     */
    public function LoadAll() {
        if (!$this->GetCache()->IsLoadedAll()) {
            return $this->SelectAll();
        }
        return $this->GetCache()->GetEntityArrayAll();
    }

}