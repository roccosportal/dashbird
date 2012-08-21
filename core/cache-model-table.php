<?php

class CacheModelTable {

        /**
         * Contains the already loaded models.
         * @var array 
         */
        protected $Cache;

        /**
         * Contains a value that indicates if all Models are loaded.
         * @var bool 
         */
        protected $IsLoadedAll;

        /**
         * Contains the ModelArray of all Models if all are loaded.
         * @var ModelArray
         */
        protected $ModelArrayAll;

        /**
         * Contains the ModelTable for this Cache
         * @var ModelTable 
         */
        protected $ModelTable;

        public function __construct(ModelTable $ModelTable) {
                $this->ModelTable = $ModelTable;
                $this->Cache = array();
                $this->IsLoadedAll = false;
                $this->ModelArrayAll = null;
        }

        /**
         * Stores a instance of model into the cache.
         * @param Model $Instance 
         */
        public function Store(Model $Instance) {
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
         * @return Model 
         */
        public function LoadByPrimaryKey($PrimaryKey) {
                if (!is_string($PrimaryKey) && !is_int($PrimaryKey)) {
                        throw new Exception('primary key must be a string or int');
                }
                if (isset($this->Cache[$PrimaryKey])) {
                        return $this->Cache[$PrimaryKey]['Instance'];
                }
                return null;
        }

        public function IsLoadedAll() {
                return $this->IsLoadedAll;
        }

        public function GetModelArrayAll() {
                return $this->ModelArrayAll;
        }

        public function SetModelArrayAll(ModelArray $ModelArray) {
                $this->IsLoadedAll = true;
                $this->ModelArrayAll = $ModelArray;
        }

        /**
         * Updates the cache.
         * @param Model $Object
         */
        public function Insert(Model $Object) {
                // update Foreign Objects that are in cache
                $Helper = $this->ModelTable->GetFieldDefinitionHelper();
                foreach ($Helper->GetFieldList() as $FieldName) {
                        if ($Helper->IsTypeForeignKey($FieldName)) {
                                $this->InsertForeignKeyReference($Object, $FieldName);
                        }
                }
                $this->Store($Object);
        }

        public function InsertForeignKeyReference(Model $Object, $FieldName) {
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
         * @param Model $Object 
         */
        public function Delete(Model $Object) {
                // update foreign objects
                $Helper = $this->ModelTable->GetFieldDefinitionHelper();

                foreach ($Helper->GetFieldList() as $FieldName) {
                        if ($Helper->IsTypeForeignKey($FieldName)) {
                                $this->DeleteForeignKeyReference($Object, $FieldName);
                        }
                }
        }

        public function DeleteForeignKeyReference(Model $Object, $FieldName) {
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

