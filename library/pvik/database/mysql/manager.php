<?php

namespace Pvik\Database\MYSQL;

use Pvik\Core\Log;

/**
 * Uses the  mysql_ functions for the database.
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
        $MySQL = \Pvik\Core\Config::$Config['MySQL'];
        $this->Connection = mysql_connect($MySQL['Server'], $MySQL['Username'], $MySQL['Password']);
        if (!$this->Connection) {
            throw new \Exception('Database error: ' . mysql_error());
        } else {
            mysql_set_charset('utf8', $this->Connection);
            if (!mysql_select_db($MySQL['Database'])) {
                throw new \Exception('Database error: ' . mysql_error());
            }
        }
    }

    /**
     * Executes a statement.
     * @param string $QueryString
     * @return mixed 
     */
    public function ExecuteQueryString($QueryString) {
        Log::WriteLine('Executing querystring: ' . $QueryString);
        $Result = mysql_query($QueryString);
        if (!$Result) {
            throw new \Exception('Could not execute following statement: ' . $QueryString . "\n" . 'MySQLError: ' . mysql_error());
        }
        return $Result;
    }

    /**
     *  Returns the last inserted id
     * @return mixed 
     */
    public function GetLastInsertedId() {
        return mysql_insert_id();
    }

    /**
     *  Escapes a string.
     * @param string $String
     * @return string
     */
    public function EscapeString($String) {
        return mysql_escape_string($String);
    }

    /**
     *  Fetches an associative array from a database result
     * @param mixed $Result
     * @return array
     */
    public function FetchAssoc($Result) {
        return mysql_fetch_assoc($Result);
    }

}