<?php


Core::Depends('~/core/sql-builder.php');
/**
 * Builds sql statements according to mssql sql.
 */
class MSSQLBuilder extends SQLBuilder {

        public function __construct() {
                $this->QuoteSign = "'";
        }

        /**
         * Creates a select statement.
         * @param ModelTable $ModelTable
         * @param string $Conditions
         * @param string $OrderBy
         * @return string 
         */
        public function CreateSelectStatement(ModelTable $ModelTable, $Conditions, $OrderBy) {
                $SQL = "";
                $SQL .= $this->CreateSelectPart($ModelTable);
                $SQL .= " ";
                $SQL .= $Conditions;
                $SQL .= " ";
                $SQL .= " ";
                $SQL .= $OrderBy;
                return $SQL;
        }

        /**
         * Creates the select header.
         * @param ModelTable $ModelTable
         * @return string 
         */
        protected function CreateSelectPart(ModelTable $ModelTable) {
                $SQL = "SELECT ";
                $Count = 1;
                $Helper = $ModelTable->GetFieldDefinitionHelper();
                foreach ($Helper->GetFieldList() as $FieldName) {
                        switch ($Helper->GetFieldType($FieldName)) {
                                case "Normal":
                                case "PrimaryKey":
                                case "ForeignKey":
                                        if ($Count > 1) {
                                                // add , at the end
                                                $SQL .= ", ";
                                        }
                                        $SQL .= $this->SQLAttribute($ModelTable, $FieldName);
                                        $Count++;
                                        break;
                                case "ManyForeignObjects":
                                        if ($Count > 1) {
                                                // add , at the end
                                                $SQL .= ", ";
                                        }
                                        $ForeignModelTable = $Helper->GetModelTable($FieldName);
                                        // generate group_conact
                                        $SQL .= " " . $FieldName . " = replace ((SELECT " . $this->SQLAttribute($ForeignModelTable, $ForeignModelTable->GetPrimaryKeyName(), "[data()]") .
                                                " FROM " . $ForeignModelTable->GetTableName() .
                                                " WHERE  " . $ForeignModelTable->GetTableName() . "." . $Helper->GetForeignKeyFieldName($FieldName) . " = " . $ModelTable->GetTableName() . "." . $ModelTable->GetPrimaryKeyName() .
                                                " FOR xml path('')), ' ', ',') ";

                                        $Count++;
                                        break;
                        }
                }
                $SQL .= " FROM " . $ModelTable->GetTableName();

                return $SQL;
        }

    /**
     * Creates a sql attribute part.
     * Example:
     * Authors.AuthorID
     * or
     * Authors.AuthorID as ID
     * @param ModelTable $ModelTable
     * @param string $Attribute
     * @param string $Alias
     * @param string $Table
     * @return string 
     */
    protected function SQLAttribute(ModelTable $ModelTable, $Attribute, $Alias = '', $Table = '') {
        if ($Table == '') {
            $Table = $ModelTable->GetTableName();
        }
        $SQL = $Table . '.' . $Attribute;
        if ($Alias != '') {
            $SQL .= ' as ' . $Alias;
        }
        return $SQL;
    }

}