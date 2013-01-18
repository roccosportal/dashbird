<?php

namespace Pvik\Database\SQL;

use Pvik\Database\Generic\ModelTable;

/**
 * Builds sql statements.
 */
abstract class Builder {

    /**
     * Contains the current instance of a builder according to the selected database type.
     * @var Builder 
     */
    protected static $Instance = null;

    /**
     * Get the current instance of the sql builder.
     * @return Builder 
     */
    public static function GetInstance() {
        if (self::$Instance == null) {
            if (isset(\Pvik\Core\Config::$Config['MySQL'])) {
                self::$Instance = new \Pvik\Database\MYSQL\Builder();
            } elseif (isset(\Pvik\Core\Config::$Config['MSSQL'])) {
                self::$Instance = new \Pvik\Database\MSSQL\Builder();
            } elseif (isset(\Pvik\Core\Config::$Config['SQLSRV'])) {
                self::$Instance = new \Pvik\Database\SQLSRV\Builder();
            }
        }
        return self::$Instance;
    }

    protected $QuoteSign = '"';

    /**
     * Creates a where statement by a primary key.
     * Example:
     * WHERE Author.AuthorID = "%s"
     * @param ModelTable $ModelTable
     * @return string 
     */
    public function CreateWhereStatementByPrimaryKey(ModelTable $ModelTable) {
        return 'WHERE ' . $ModelTable->GetTableName() . '.' . $ModelTable->GetPrimaryKeyName() . ' = ' . $this->QuoteSign . '%s' . $this->QuoteSign;
    }

    /**
     * Creates a where statement according to primary keys.
     * 
     * @param ModelTable $ModelTable
     * @param array $Keys
     * @return array $Result['SQL'], $Result['Parameters'] 
     */
    public function CreateWhereStatementByPrimaryKeys(ModelTable $ModelTable, array $Keys) {
        $Result = $this->CreateInStatementForKeys($ModelTable, $ModelTable->GetPrimaryKeyName(), $Keys);
        $Result['SQL'] = "WHERE " . $Result['SQL'];
        return $Result;
    }

    /**
     * Create a in statement for keys
     * @param ModelTable $ModelTable
     * @param string $Field
     * @param array $Keys
     * @return array $Result['SQL'], $Result['Parameters']
     */
    public function CreateInStatementForKeys(ModelTable $ModelTable, $FieldName, array $Keys) {
        $SQL = $ModelTable->GetTableName() . '.' . $FieldName . ' IN ( ';
        $Count = 0;
        $Parameters = array();
        foreach ($Keys as $Key) {
            if ($Count != 0) {
                $SQL .= ',';
            }
            $SQL .= $this->QuoteSign . '%s' . $this->QuoteSign;
            array_push($Parameters, $Key);
            $Count++;
        }
        $SQL .= ')';
        return array('SQL' => $SQL, 'Parameters' => $Parameters);
    }

    /**
     * Creates a select statement.
     * @param ModelTable $ModelTable
     * @param string $Conditions
     * @param string $OrderBy
     * @return string 
     */
    abstract public function CreateSelectStatement(ModelTable $ModelTable, $Conditions, $OrderBy);

    /**
     * Creates a insert statement.
     * @param ModelTable $ModelTable
     * @param Model $Object
     * @return array $Result['SQL'], $Result['Parameters']
     */
    public function CreateInsertStatement(ModelTable $ModelTable, \Pvik\Database\Generic\Entity $Object) {
        $SQL = 'INSERT INTO ' . $ModelTable->GetTableName();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        // create column list
        $SQL .= ' (';
        $Count = 1;
        foreach ($Helper->GetFieldList() as $FieldName) {
            switch ($Helper->GetFieldType($FieldName)) {
                case 'Normal':
                case 'ForeignKey':
                    if ($Count > 1) {
                        // add , at the end
                        $SQL .= ', ';
                    }
                    $SQL .= $FieldName;
                    $Count++;
                    break;
                case 'PrimaryKey':
                    // only insert a value if it is a guid otherwise ignore
                    // the primarykey will be set on the database
                    if ($Helper->IsGuid($FieldName)) {
                        if ($Count > 1) {
                            // add , at the end
                            $SQL .= ', ';
                        }
                        $SQL .= $FieldName;
                        $Count++;
                    }
                    break;
            }
        }
        $SQL .= ')';
        // create column values
        $SQL .= ' VALUES (';
        $Count = 1;
        $Parameters = array();
        foreach ($Helper->GetFieldList() as $FieldName) {
            switch ($Helper->GetFieldType($FieldName)) {
                case 'Normal':
                case 'ForeignKey':
                    if ($Count > 1) {
                        // add , at the end
                        $SQL .= ', ';
                    }
                    $Data = $Object->GetFieldData($FieldName);
                    $Result = $this->ConvertValue($Data, $Parameters);
                    $Parameters = $Result['Parameters'];
                    $SQL .= $Result['Value'];

                    $Count++;
                    break;
                case 'PrimaryKey':
                    // only insert a value if it is a guid otherwise ignore
                    // the primarykey will be set on the database
                    if ($Helper->IsGuid($FieldName)) {
                        if ($Count > 1) {
                            // add , at the end
                            $SQL .= ', ';
                        }
                        $SQL .= $this->QuoteSign . '%s' . $this->QuoteSign;
                        array_push($Parameters, Core::CreateGuid());
                        $Count++;
                    }
                    break;
            }
        }
        $SQL .= ')';
        return array('SQL' => $SQL, 'Parameters' => $Parameters);
    }

    /**
     * Creates an update statement.
     * @param ModelTable $ModelTable
     * @param Model $Object
     * @return array $Result['SQL'], $Result['Parameters']
     */
    public function CreateUpdateStatement(ModelTable $ModelTable, \Pvik\Database\Generic\Entity $Object) {
        $SQL = 'UPDATE ' . $ModelTable->GetTableName() . ' SET ';
        $Count = 1;
        $Parameters = array();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        foreach ($Helper->GetFieldList() as $FieldName) {
            switch ($Helper->GetFieldType($FieldName)) {
                case 'Normal':
                case 'ForeignKey':
                    if ($Count > 1) {
                        // add , at the end
                        $SQL .= ', ';
                    }
                    $Data = $Object->GetFieldData($FieldName);
                    $Result = $this->ConvertValue($Data, $Parameters);
                    $Parameters = $Result['Parameters'];
                    $SQL .= $FieldName . '=' . $Result['Value'];
                    $Count++;
                    break;
            }
        }
        $SQL .= ' ' . $this->CreateWhereStatementByPrimaryKey($ModelTable);
        array_push($Parameters, $Object->GetFieldData($ModelTable->GetPrimaryKeyName()));
        return array('SQL' => $SQL, 'Parameters' => $Parameters);
    }

    /**
     * Converts a value to a sql string.
     * @param mixed $Value
     * @param array $Parameters
     * @return array $Result['Value'], $Result['Parameters'] 
     */
    protected function ConvertValue($Value, array $Parameters) {
        if (is_bool($Value)) {
            if ($Value == true)
                $ConvertedValue = 'TRUE';
            else
                $ConvertedValue = 'FALSE';
        }
        elseif ($Value !== null) {
            $ConvertedValue = $this->QuoteSign . '%s' . $this->QuoteSign;
            array_push($Parameters, $Value);
        } else {
            $ConvertedValue = 'NULL';
        }
        return array('Value' => $ConvertedValue, 'Parameters' => $Parameters);
    }

    /**
     * Creates a delete statement.
     * @param ModelTable $ModelTable
     * @return string 
     */
    public function CreateDeleteStatement(ModelTable $ModelTable) {
        return 'DELETE FROM ' . $ModelTable->GetTableName() . ' WHERE ' . $ModelTable->GetPrimaryKeyName() . ' = ' . $this->QuoteSign . '%s' . $this->QuoteSign;
    }

}