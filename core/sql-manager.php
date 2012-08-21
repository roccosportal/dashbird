<?php

/**
 * Class that refers to the right function depending on which database is selected.
 */
abstract class SQLManager {
    protected static $Instance = null;
    
    /**
     * Get the current instance of the sql manager
     * @return SQLManager 
     */
    public static function GetInstance(){
            if(self::$Instance == null){
                         if (isset(Core::$Config['MySQL'])) {
                                 self::$Instance = new MySQLManager();
                                 Log::WriteLine('Use MySQL as database system');
                         } elseif (isset(Core::$Config['MSSQL'])) {
                                 self::$Instance = new MSSQLManager();
                                 Log::WriteLine('Use MSSQL as database system');
                        } elseif (isset(Core::$Config['SQLSRV'])) {
                                 self::$Instance = new SQLSRVManager();
                                Log::WriteLine('Use SQLSRV as database system');
                         }
            }
            return self::$Instance;
    }

     /**
     * Executes a statement.
     * @param string $QueryString
     * @return mixed 
     */
    abstract public function ExecuteQueryString($QueryString);
    
      /**
     *  Returns the last inserted id
     * @return mixed 
     */
    abstract public function GetLastInsertedId();
    
       /**
     *  Escapes a string.
     * @param string $String
     * @return string
     */
    abstract public  function EscapeString($String);
    
      /**
     *  Fetches an associative array from a database result
     * @param mixed $Result
     * @return array
     */
     abstract public  function FetchAssoc($Result);

      /**
     * Executes a select statement.
     * @param type $QueryString
     * @return mixed 
     */
    public function Select($QueryString) {
        return $this->ExecuteQueryString($QueryString);
    }

    /**
     * Executes a select statement with parameters.
     * @param string $QueryString
     * @param array $Parameters
     * @return mixed 
     */
    public function SelectWithParameters($QueryString, array $Parameters) {
        $ConvertedParameters = $this->ConvertParameters($Parameters);
        $QueryString = vsprintf($QueryString, $ConvertedParameters);
        return $this->Select($QueryString);
    }

    /**
     * Executes a insert statement
     * @param string $QueryString
     * @return mixed 
     */
    public function Insert($QueryString) {
        $this->ExecuteQueryString($QueryString);
        return $this->GetLastInsertedId();
    }
    
  

    /**
     * Executes a insert statement with parameters.
     * @param string $QueryString
     * @param array $Parameters
     * @return mixed 
     */
    public function InsertWithParameters($QueryString, array $Parameters) {
        $ConvertedParameters =$this->ConvertParameters($Parameters);
        $QueryString = vsprintf($QueryString, $ConvertedParameters);
        return $this->Insert($QueryString);
    }

    /**
     * Executes a update statement.
     * @param string $QueryString
     * @return mixed 
     */
    public function Update($QueryString) {
        return $this->ExecuteQueryString($QueryString);
    }

    /**
     * Executes a update statement with parameters.
     * @param string $QueryString
     * @param array $Parameters
     * @return type 
     */
    public function UpdateWithParameters($QueryString, array $Parameters) {
        $ConvertedParameters = $this->ConvertParameters($Parameters);
        $QueryString = vsprintf($QueryString, $ConvertedParameters);
        return $this->Update($QueryString);
    }

    /**
     * Executes a delete statement.
     * @param string $QueryString
     * @return mixed 
     */
    public function Delete($QueryString) {
        return $this->ExecuteQueryString($QueryString);
    }

    /**
     * Executes a delete statement with parameters
     * @param type $QueryString
     * @param type $Parameters
     * @return type 
     */
    public function DeleteWithParameters($QueryString, array $Parameters) {
        $ConvertedParameters = $this->ConvertParameters($Parameters);
        $QueryString = vsprintf($QueryString, $ConvertedParameters);
        return $this->Delete($QueryString);
    }

    /**
     * Escape parameters.
     * @param array $Parameters
     * @return array 
     */
    public function ConvertParameters(array $Parameters) {
        $ConvertedParameters = array();
        foreach ($Parameters as $Parameter) {
            array_push($ConvertedParameters, $this->EscapeString($Parameter));
        }
        return $ConvertedParameters;
    }
    
 
    

    /**
     * Creates a ModelArray from a select statemet
     * @param ModelTable $ModelTable
     * @param string $QueryString
     * @param array $Parameters
     * @return ModelArray 
     */
    public function FillList(ModelTable $ModelTable, $QueryString, array $Parameters) {
        $Result = $this->SelectWithParameters($QueryString, $Parameters);
        $List = new ModelArray();
        $List->SetModelTable($ModelTable);
        while ($Data = $this->FetchAssoc($Result)) {
            $Classname = $ModelTable->GetModelClassName();
            $Model = new $Classname();
            $Model->Fill($Data);
            $List->append($Model);
        }
        return $List;
    }
    
  

}