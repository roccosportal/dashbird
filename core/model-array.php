<?php
/**
 * A class that contains a list of Model.
 */
class ModelArray extends ArrayObject
{
    /**
     * Contains the list of fields that get sorted when running a sort function.
     * @var array 
     */
    protected $SortFieldLists = null;
    /**
     * Contains the ModelTable of the models.
     * @var ModelTable 
     */
    protected $ModelTable;

    /**
     * Sets the ModelTable of the models
     * @param ModelTable $ModelTable 
     */
    public function SetModelTable(ModelTable $ModelTable = null)
    {
        $this->ModelTable = $ModelTable;
    }
    /**
     * Get the ModelTable of the models
     * @return ModelTable 
     */
    public function GetModelTable()
    {
        return $this->ModelTable;
    }

    /**
     * Run a distinct on a field
     * @param string $FieldName
     * @return ModelArray 
     */
    public function Distinct($FieldName)
    {
        $KeyList = array();
        $List = new ModelArray();
        foreach ($this as $Object)
        {
            if (!in_array($Object->$FieldName, $KeyList))
            {
                array_push($KeyList, $Object->$FieldName);
                $List->append($Object);
            }
        }
        return $List;
    }

    /**
     * Returns a list of models where the field is equals one of the given values.
     * @param string $FieldName
     * @param IteratorAggregate $Values
     * @return ModelArray 
     */
    public function FilterIn($FieldName,IteratorAggregate $Values)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        if ($Values === null)
            return $List;


        if (!is_array($Values) && !($Values instanceof IteratorAggregate))
            throw new Exception('The parameters keys must be an array.');


        foreach ($this as $Object)
        {
            foreach ($Values as $Value)
            {
                if ($Object->$FieldName === $Value)
                {
                    $List->append($Object);
                    break;
                }
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field is not equals one of the given values.
     * @param string $FieldName
     * @param IteratorAggregate $Values
     * @return ModelArray 
     */
    public function FilterNotIn($FieldName, $Values)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        if ($Keys === null)
            return $List;


        if (!is_array($Values) && !($Values instanceof IteratorAggregate))
            throw new Exception('The parameters keys must be an array.');


        foreach ($this as $Object)
        {
            $HasValue = false;
            foreach ($Values as $Value)
            {
                if ($Object->$FieldName == $Value)
                {
                    $HasValue = true;
                    break;
                }
            }
            if ($HasValue == false)
            {
                $List->append($Object);
            }
        }
        return $List;
    }

    /**
     * Returns a list of models where a field value is equals to the value.
     * @param string $FieldName
     * @param mixed $Value
     * @return ModelArray 
     */
    public function FilterEquals($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName === $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where a field value is higher than the value.
     * @param string $FieldName
     * @param mixed $Value
     * @return ModelArray 
     */
    public function FilterHeigher($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName > $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value is higher or equals to the value.
     * @param string $FieldName
     * @param type $Value
     * @return ModelArray 
     */
    public function FilterHeigherEquals($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName >= $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field is lower than the value
     * @param string $FieldName
     * @param mixed $Value
     * @return ModelArray 
     */
    public function FilterLower($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName < $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value is lower or equals to the value.
     * @param string $FieldName
     * @param mixed $Value
     * @return ModelArray 
     */
    public function FilterLowerEquals($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName <= $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value is not equals the value.
     * @param string $FieldName
     * @param mixed $Value
     * @return ModelArray 
     */
    public function FilterNotEquals($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName !== $Value)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value starts with the value.
     * @param string $FieldName
     * @param string $Value
     * @return ModelArray 
     */
    public function FilterStartsWith($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName!=null&&strpos($Object->$FieldName, $Value)===0)
            {
                $List->append($Object);
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value ends with the value.
     * @param string $FieldName
     * @param string $Value
     * @return ModelArray 
     */
    public function FilterEndsWith($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if ($Object->$FieldName!=null){
                $LengthField = strlen($Object->$FieldName);
                $LengthValue = strlen($Value);
                if($LengthField>=$LengthValue){
                    if(strpos($Object->$FieldName, $Value)===($LengthField - $LengthValue)){
                        $List->append($Object);
                    }
                }
            }
        }
        return $List;
    }
    
    /**
     * Returns a list of models where the field value contains the value.
     * @param string $FieldName
     * @param string $Value
     * @return ModelArray 
     */
    public function FilterContains($FieldName, $Value)
    {
        $List = new ModelArray();
        $List->SetModelTable($this->GetModelTable());
        foreach ($this as $Object)
        {
            if (strpos($Object->$FieldName, $Value)!==false)
            {
                $List->append($Object);
            }
        }
        return $List;
    }

    /**
     * Returns a list of an field.
     * @param type $FieldName
     * @return ModelArray 
     */
    public function GetList($FieldName)
    {
        $List = new ModelArray();
        if ($this->ModelTable != null)
        {
            $Helper = $this->ModelTable->GetFieldDefinitionHelper();
            if($Helper->IsTypeForeignObject($FieldName)||$Helper->IsTypeManyForeignObjects($FieldName)){
                $List->SetModelTable($Helper->GetModelTable($FieldName));
            }
        }
        foreach ($this as $Object)
        {
            $List->append($Object->$FieldName);
        }
        return $List;
    }
    /**
     * Returns the first object in the list.
     * @return Model; 
     */
    public function GetFirst(){
        foreach($this as $Object){
            return $Object;
        }
        return null;
    }
    /**
     * Returns the first objects in the list
     * @param int $MaxCount
     * @return ModelArray 
     */
    public function GetFirsts($MaxCount){
        $List = new ModelArray();
        if ($this->ModelTable != null)
        {
            $List->SetModelTable($this->ModelTable);
        }
        $Count = 1;
        foreach($this as $Object){
            $List->append($Object);
            if($Count==$MaxCount){
                break;
            }
            else {
                $Count++;
            }
        }
        return $List;
    }
    
    /**
     * Returns the objects between start and end number.
     * @param int $Start
     * @param int $Length
     * @return ModelArray 
     */
    public function GetBetween($Start, $Length){
        $List = new ModelArray();
        if ($this->ModelTable != null)
        {
            $List->SetModelTable($this->ModelTable);
        }
        $Count = 1;
        foreach($this as $Object){
            if($Count>=$Start){
                if($Start+$Length!=$Count){
                    $List->append($Object);
                }
                else {
                    break;
                }
            }
            $Count++;
        }
        return $List;
    }
    
    
    /**
     * Sorts the list to specified arguments.
     * Allows more then one argument.
     * @example Sort('Date'); // sorts the list asscending to field date
     * @example Sort('-Date'); // sorts the list descending to field date
     * @example Sort('+Date'); // equals to Sort('Date')
     * @example Sort('Author->Name'); // author is a ForeignObject and the list gets sorted assencding to the name
     * @example Sort('Date', '-Author->Name'); // sorts first to field date and than to sub field of author
     * @example Sort('Date', 'Author->Country->Name', 'Author->Name');
     * @param type $SortArgument
     * @return ModelArray 
     */
    public function Sort($SortArgument)
    {
        $Arguments = func_get_args();
        $FieldLists = array();
        foreach ($Arguments as $Argument)
        {
            array_push($FieldLists, $this->ConvertStringToFieldList($Argument, true));
        }
        $this->SortFieldLists = $FieldLists;
        $this->uasort(array($this, 'Compare'));
        return $this;
    }

    /**
     * Converts a field string from 'Author->Country->Name' to an array
     * @param string $String
     * @param bool $IsSortable
     * @return array 
     */
    protected function ConvertStringToFieldList($String, $IsSortable = false)
    {
        if ($IsSortable)
        {
            $Sort = 'ASC';
            if ($String[0] == '+')
            {
                $String = substr($String, 1);
            }
            elseif ($String[0] == '-')
            {
                $String = substr($String, 1);
                $Sort = 'DESC';
            }
        }

        $Fields = explode('->', $String);

        $FieldList = array();
        if ($IsSortable)
            $FieldList['Sort'] = $Sort;
        $FieldList['Fields'] = $Fields;
        return $FieldList;
    }

    /**
     * Compares two obects.
     * @param mixed $Object1
     * @param mixed $Object2
     * @return int 
     */
    public function Compare($Object1, $Object2)
    {
        $FieldLists = $this->SortFieldLists;
        foreach ($FieldLists as $FieldList)
        {
            $Result = $this->CompareFieldList($Object1, $Object2, $FieldList);
            if ($Result != 0)
            {
                return $Result;
            }
        }
        return 0;
    }

    /**
     * Compares a two objects to a field list.
     * @param mixed $Object1
     * @param mixed $Object2
     * @param array $FieldList
     * @return int 
     */
    protected function CompareFieldList($Object1, $Object2, $FieldList)
    {
        $Sort = $FieldList['Sort'];
        $Fields = $FieldList['Fields'];
        $CountFields = count($Fields);
        for ($Index = 0; $Index < $CountFields; $Index++)
        {
            $Field = $Fields[$Index];
            if ($Object1->$Field == null && $Object2->$Field == null)
            {
                return 0;
            }
            elseif ($Object1->$Field == null)
            {
                if ($Sort == 'ASC')
                {
                    return -1;
                }
                return 1;
            }
            elseif ($Object2->$Field == null)
            {
                if ($Sort == 'ASC')
                {
                    return 1;
                }
                return -1;
            }
            // if it is the last item in the field list
            elseif ($Index == ($CountFields - 1))
            {
                if ($Object1->$Field == $Object2->$Field)
                {
                    return 0;
                }
                elseif ($Object1->$Field < $Object2->$Field)
                {
                    if ($Sort == 'ASC')
                    {
                        return -1;
                    }
                    return 1;
                }
                elseif ($Sort == 'ASC')
                {
                    return 1;
                }
                return -1;
            }
            else
            {
                $Object1 = $Object1->$Field;
                $Object2 = $Object2->$Field;
            }
        }
    }

    /**
     * Merge a ModelArray to current ModelArray
     * @param ModelArray $ModelArray
     * @return ModelArray 
     */
    public function Merge(ModelArray $ModelArray)
    {
        $List = new ModelArray();
        // set model table if both arrays have the same model table
        if ($this->GetModelTable() == $ModelArray->GetModelTable())
        {
            $List->SetModelTable($this->GetModelTable());
        }
        foreach ($this as $Object)
        {

            $List->append($Object);
        }
        foreach ($ModelArray as $Object)
        {

            $List->append($Object);
        }
        return $List;
    }

    /**
     * Checks if one of the models in list have the value.
     * @param string $FieldName
     * @param mixed $Value
     * @return bool 
     */
    public function HasValue($FieldName, $Value)
    {
        foreach ($this as $Object)
        {
            if ($Object->$FieldName === $Value)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if none of the models in list have the value.
     * @param type $FieldName
     * @param type $Value
     * @return type 
     */
    public function HasNotValue($FieldName, $Value)
    {
        return !$this->HasValue($FieldName, $Value);
    }

    /**
     * Loads a list of fields in the model.
     * @example LoadList('Author->Books'); // returns a list of books
     * @param string $Fields
     * @return ModelArray 
     */
    public function LoadList($Fields)
    {
        if ($this->ModelTable == null)
        {
            throw new Exception('ModelTable must be set to use this function.');
        }
        $FieldList = $this->ConvertStringToFieldList($Fields);
        $ModelTable = $this->ModelTable;
        $List = $this;
        foreach ($FieldList['Fields'] as $FieldName)
        {
            // load definition for current field
            $Helper = $ModelTable->GetFieldDefinitionHelper();

            if (!$Helper->FieldExists($FieldName))
            {
                throw new Exception('Field ' . $FieldName . ' must be in field definition');
            }
            switch ($Helper->GetFieldType($FieldName))
            {
                case 'ForeignObject':
                    $ForeignKeyFieldName = $Helper->GetForeignKeyFieldName($FieldName);
                    $ForeignModelTable = $Helper->GetModelTable($ForeignKeyFieldName);
                    $Keys = array();
                    // get all keys
                    foreach ($List as $Object)
                    {
                        if ($Object != null && $Object->$ForeignKeyFieldName !== null)
                        {
                            array_push($Keys, $Object->$ForeignKeyFieldName);
                        }
                    }
                    // load all foreign objects
                    $List = $ForeignModelTable->LoadByPrimaryKeys($Keys);
                    // set for the next field in the loop
                    $ModelTable = $ForeignModelTable;
                    break;
                case 'ManyForeignObjects':
                    $ForeignModelTable = $Helper->GetModelTable($FieldName);
                    $Keys = array();
                    foreach ($List as $Object)
                    {
                        if ($Object != null)
                        {
                            $Keys = array_merge($Keys, $Object->GetKeys($FieldName));
                        }
                    }
                    $List = $ForeignModelTable->LoadByPrimaryKeys($Keys);
                    // set for the next field in the loop
                    $ModelTable = $ForeignModelTable;
                    break;
                default:
                    return null;
                    break;
            }
        }
        return $List;
    }
    /**
     * Checks if the list is empty.
     * @return bool 
     */
    public function IsEmpty(){
        if($this->count()==0){
            return true;
        }
        else {
            return false;
        }
    }

}
