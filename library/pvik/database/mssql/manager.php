<?php

namespace Pvik\Database\MSSQL;

use Pvik\Core\Log;

/**
 * Uses the function mssql_ for the database.
 * Runs sql statements.
 */
class Manager extends \Pvik\Database\SQL\Manager {

    /**
     * Contains the connection to the database.
     * @var mixed 
     */
    protected $Connection;

    /**
     * Connect to the database
     */
    protected function __construct() {
        $MSSQL = \Pvik\Core\Config::$Config['MSSQL'];
        ini_set('mssql.charset', 'UTF-8');
        $this->Connection = mssql_connect($MSSQL['Server'], $MSSQL['Username'], $MSSQL['Password']);
        if (!$this->Connection) {
            throw new \Exception('Database error: ' . mssql_get_last_message());
        } else {
            if (!mssql_select_db($MSSQL['Database'])) {
                throw new \Exception('Database error: ' . mssql_get_last_message());
            }
        }
    }

    /**
     * Executes a statement.
     * @param string $QueryString
     * @return mixed 
     */
    public function ExecuteQueryString($QueryString) {
        self::Connect();
        Log::WriteLine('Executing querystring: ' . $QueryString);
        $Result = mssql_query($QueryString);
        if (!$Result) {
            throw new \Exception('Could not execute following statement: ' . $QueryString . "\n" . 'MSSQLError: ' . mssql_get_last_message());
        }
        return $Result;
    }

    /**
     *  Returns the last inserted id
     * @return mixed 
     */
    public function GetLastInsertedId() {
        $ID = 0;
        $Result = mssql_query("SELECT @@identity AS id");
        if ($Row = mssql_fetch_array($Result, MSSQL_ASSOC)) {
            $ID = $Row["id"];
        }
        return $ID;
    }

    /**
     *  Escapes a string.
     * @param string $String
     * @return string
     */
    public function EscapeString($String) {
        return str_replace("'", "''", $String);
    }

    /**
     *  Fetches an associative array from a database result
     * @param mixed $Result
     * @return array
     */
    public function FetchAssoc($Result) {
        return mssql_fetch_assoc($Result);
    }

}