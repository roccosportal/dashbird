<?php

namespace Pvik\Database\Generic;

/**
 * A object for saving sql query options.
 */
class Query {

    /**
     * Contains the ModelTable on which the query will run.
     * @var ModelTable 
     */
    protected $ModelTable;

    /**
     *
     * Contains the WHERE conditions.
     * @var string 
     */
    protected $Conditions;

    /**
     * Contains the ORDER BY.
     * @var string 
     */
    protected $OrderBy;

    /**
     * Contains the parameters.
     * @var array 
     */
    protected $Parameters;

    /**
     *
     * @param string $ModelTableName ModelTable name on which the query will run.
     */
    public function __construct($ModelTableName) {
        if (!is_string($ModelTableName)) {
            throw new \Exception('ModelTableName must be a string.');
        }
        $this->ModelTable = ModelTable::Get($ModelTableName);
        $this->Conditions = '';
        $this->OrderBy = '';
        $this->Parameters = array();
    }

    /**
     * Sets the WHERE conditions.
     * @example SetConditions('WHERE Field = "%s"');
     * @param string $Conditions
     * @return Query 
     */
    public function SetConditions($Conditions) {
        $this->Conditions = $Conditions;
        return $this;
    }

    /**
     * Returns the WHERE conditions.
     * @return string 
     */
    public function GetConditions() {
        return $this->Conditions;
    }

    /**
     * Sets the ORDER BY.
     * @example SetOrderBy('ORDER BY Date');
     * @param string $OrderBy
     * @return Query 
     */
    public function SetOrderBy($OrderBy) {
        $this->OrderBy = $OrderBy;
        return $this;
    }

    /**
     * Returns the ORDER BY.
     * @return string 
     */
    public function GetOrderBy() {
        return $this->OrderBy;
    }

    /**
     * Add a parameter to list.
     * @param string $Parameter
     * @return Query 
     */
    public function AddParameter($Parameter) {
        array_push($this->Parameters, $Parameter);
        return $this;
    }

    /**
     * Get all parameters.
     * @return array 
     */
    public function GetParameters() {
        return $this->Parameters;
    }

    /**
     * Runs a select statement.
     * @return ModelArray
     */
    public function Select() {
        return $this->ModelTable->Select($this);
    }

    /**
     * Runs a select single statement. 
     * @return Model
     */
    public function SelectSingle() {
        return $this->ModelTable->SelectSingle($this);
    }

}