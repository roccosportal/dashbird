<?php
Core::Depends('~/core/sql-builder.php');
/**
 * Builds sql statements according to mysql sql.
 */
class MySQLBuilder  extends SQLBuilder{


     public function __construct(){
                $this->QuoteSign = '"';
     }

    /**
     * Creates a select statement.
     * @param ModelTable $ModelTable
     * @param string $Conditions
     * @param string $OrderBy
     * @return string 
     */
    public function CreateSelectStatement(ModelTable $ModelTable, $Conditions, $OrderBy) {
        $SQL = '';
        $SQL .= $this->CreateSelectPart($ModelTable);
        $SQL .= ' ';
        $SQL .= $Conditions;
        $SQL .= ' ';
        $SQL .= $this->CreateGroupByStatement($ModelTable);
        $SQL .= ' ';
        $SQL .= $OrderBy;
        return $SQL;
    }

    /**
     * Creates the select header.
     * @param ModelTable $ModelTable
     * @return string 
     */
    protected function CreateSelectPart(ModelTable $ModelTable) {
        $SQL = 'SELECT ';
        $Count = 1;
        $Join = false;
        $AliasArray = array();
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        foreach ($Helper->GetFieldList() as $FieldName) {
            switch ($Helper->GetFieldType($FieldName)) {
                case 'Normal':
                case 'PrimaryKey':
                case 'ForeignKey':
                    if ($Count > 1) {
                        // add , at the end
                        $SQL .= ', ';
                    }
                    $SQL .= $this->SQLAttribute($ModelTable, $FieldName);
                    $Count++;
                    break;
                case 'ManyForeignObjects':
                    if ($Count > 1) {
                        // add , at the end
                        $SQL .= ', ';
                    }
                    // simeple creation for a unique alias
                    $Alias = '';
                    for ($i = 0; $i < count($AliasArray) + 1; $i++) {
                        $Alias .= 't';
                    }
                    $AliasArray[$FieldName] = $Alias;
                    // get definition for the foreign table

                    $ForeignModelTable = $Helper->GetModelTable($FieldName);
                    // generate group_conact
                    $SQL.= 'GROUP_CONCAT(DISTINCT ' . $this->SQLAttribute($ModelTable, $ForeignModelTable->GetPrimaryKeyName(), '', $Alias) . ') as ' . $FieldName;
                    $Join = true;
                    $Count++;
                    break;
            }
        }
        $SQL .= ' FROM ' . $ModelTable->GetTableName();


        if ($Join) {
            // add joins
            foreach ($Helper->GetManyForeignObjectsFieldList() as $FieldName) {
                $ForeignModelTable = $Helper->GetModelTable($FieldName);
                $SQL .= ' LEFT JOIN ' . $ForeignModelTable->GetTableName() . ' as ' . $AliasArray[$FieldName]
                        . ' ON ' . $this->SQLAttribute($ModelTable, $Helper->GetForeignKeyFieldName($FieldName), '', $AliasArray[$FieldName])
                        . ' = ' . $this->SQLAttribute($ModelTable, $ModelTable->GetPrimaryKeyName(), '', $ModelTable->GetTableName());
            }
        }
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

    /**
     * Creates group statement.
     * @param ModelTable $ModelTable
     * @return string 
     */
    protected function CreateGroupByStatement(ModelTable $ModelTable) {
        $Count = 1;
        $SQL = '';
        $Helper = $ModelTable->GetFieldDefinitionHelper();
        foreach ($Helper->GetManyForeignObjectsFieldList() as $FieldName) {
            if ($Count > 1) {
                // add , at the end
                $SQL .= ', ';
            }
            $SQL .= $this->SQLAttribute($ModelTable, $ModelTable->GetPrimaryKeyName());
            $Count++;
        }
        // if we have a group by part
        if ($Count > 1) {
            $SQL = 'GROUP BY ' . $SQL;
        }
        return $SQL;
    }

}