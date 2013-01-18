<?php
namespace PvikAdminTools\Controllers;
use \Pvik\Database\Generic\ModelTable;
use \Pvik\Utils\ValidationState;
/**
 * Logic for a table list or single entry.
 */
class Tables extends Base {

    /**
     * Contains a helper class for the PvikAdminTools configuration.
     * @var \PvikAdminTools\Library\ConfigurationHelper 
     */
    protected $ConfigurationHelper;

    /**
     * Returns the name of a model table.
     * @param string $ParameterTableName lower cased
     * @return string 
     */
    protected function GetModelTableName($ParameterTableName) {
        foreach (\Pvik\Core\Config::$Config['PvikAdminTools']['Tables'] as $TableName => $TableConfiguration) {
            if (strtolower($ParameterTableName) == strtolower($TableName)) {
                return $TableName;
            }
        }
        return null;
    }

    /**
     * Redirects to the root tables page.
     */
    protected function RedirectToTables() {
        $Url = '~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/';
        $this->RedirectToPath($Url);
    }

    /**
     * Displays a list of all tables.
     */
    public function IndexAction() {
        if ($this->CheckPermission()) {
            $this->ExecuteView();
        }
    }

    /**
     * Redirects to the right action depending on the parameters.
     */
    public function IndexWithParametersAction() {
        $this->ConfigurationHelper = new \PvikAdminTools\Library\ConfigurationHelper();
        if ($this->CheckPermission()) {
            $Parameters = $this->GetParameters('parameters');
            if (count($Parameters) >= 2) {
                $ParameterTableName = $Parameters[0];
                $ModelTableName = $this->GetModelTableName($ParameterTableName);
                $this->ConfigurationHelper->SetCurrentTable($ModelTableName);
                if ($ModelTableName != null) {
                    $Action = $Parameters[1];
                    switch ($Action) {
                        case 'list':
                            $this->ListTable($ModelTableName);
                            break;
                        case 'new':
                            $this->NewEntry($ModelTableName);
                            break;
                        case 'edit':
                            if (count($Parameters) == 3) {
                                $this->EditEntry($ModelTableName, $Parameters[2]);
                            } else {
                                $this->RedirectToTables();
                            }
                            break;
                        case 'delete':
                            if (count($Parameters) == 3) {
                                $this->DeleteEntry($ModelTableName, $Parameters[2]);
                            } else {
                                $this->RedirectToTables();
                            }
                            break;
                        default:
                            $this->RedirectToTables();
                            break;
                    }
                } else {
                    $this->RedirectToTables();
                }
            } else {
                $this->RedirectToTables();
            }
        }
    }

    /**
     * Lists table entries.
     * @param string $ModelTableName 
     */
    protected function ListTable($ModelTableName) {
        $EntityArray = ModelTable::Get($ModelTableName)->LoadAll();

        $TableHtml = new \PvikAdminTools\Library\TableHtml($EntityArray, $this->Request);
        $this->ViewData->Set('TableHtml', $TableHtml);
        $this->ExecuteViewByAction('ListTable');
    }

    /**
     * Logic for a new entry. 
     * @param string $ModelTableName 
     */
    protected function NewEntry($ModelTableName) {
        $this->ViewData->Set('ModelTableName', $ModelTableName);
        $ParameterPresetValues = $this->GetParameters('preset-values');

        $PresetValues = array();
        if ($ParameterPresetValues != null) {
            // preset values must be a even number
            if(count($ParameterPresetValues) % 2 == 0){
                // convert to associative array
                for ($Index = 0; $Index < count($ParameterPresetValues); $Index++) {
                    $Key = $ParameterPresetValues[$Index];
                    $Index++;
                    $Value = $ParameterPresetValues[$Index];
                    $PresetValues[$Key] = $Value;
                }
                
            }
            else {
                $this->RedirectToTables();
                exit();
            }
            
        } 
        $this->ViewData->Set('PresetValues',$PresetValues);
        

        $ModelTable = ModelTable::Get($ModelTableName);

        $EntityClassName = $ModelTable->GetEntityClassName();
        $Entity = new $EntityClassName();

        
        // data send
        if ($this->Request->IsPOST('submit')) {
           

            $ValidationState = new ValidationState();
            $Fields = array();
            foreach ($this->ConfigurationHelper->GetFieldList() as $FieldName) {
                $Type = $this->ConfigurationHelper->GetFieldType($FieldName);
                $FieldClassName = '\\PvikAdminTools\\Library\\Fields\\' . $Type;
                if (!class_exists($FieldClassName)) {
                    throw new \Exception('PvikAdminTools: The type ' . $Type . ' does not exists. Used for the field ' . $FieldName);
                }
                $Field = new $FieldClassName($FieldName, $Entity, $this->Request, $ValidationState);
                /* @var $Field PvikAdminToolsBaseField */
                array_push($Fields, $Field);
                $ValidationState = $Field->Validation();
            }
            if ($ValidationState->IsValid()) {
                // update all fields
                foreach ($Fields as $Field) {
                    /* @var $Field PvikAdminToolsBaseField */
                    $Field->Update();
                }
                $Entity->Insert();
                
                $RedirectBackUrl = $this->Request->GetGET('redirect-back-url');
                if($RedirectBackUrl!=null){
                    // the user was was in edit mode of an table entry
                    // clicked on new in a foreign id and and created/updated/deleted a entry
                    // now we redirect back to the edit mode
                     $this->RedirectToPath(urldecode($RedirectBackUrl));
                }
                else {
                    // redirect to inserted entry
                    $this->RedirectToPath('~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' . strtolower($ModelTableName) . ':edit:' . $Entity->GetPrimaryKey() . '/');
                }
                
            } else {
               

                $SingleHtml = new \PvikAdminTools\Library\SingleHtml($Entity, $ValidationState, $this->Request);
                $SingleHtml->SetPresetValues($PresetValues);
                $this->ViewData->Set('SingleHtml', $SingleHtml);


                $this->ExecuteViewByAction('NewEntry');
            }
        } else {
            $ValidationState = new ValidationState();
          
            $SingleHtml = new \PvikAdminTools\Library\SingleHtml($Entity, $ValidationState, $this->Request);
            $SingleHtml->SetPresetValues($PresetValues);

            $this->ViewData->Set('SingleHtml', $SingleHtml);
            $this->ExecuteViewByAction('NewEntry');
        }
    }

    /**
     * Logic for editing an entry.
     * @param string $ModelTableName
     * @param string $EntityPrimaryKey 
     */
    protected function EditEntry($ModelTableName, $EntityPrimaryKey) {
        
        $this->ViewData->Set('ModelTableName', $ModelTableName);
        $ModelTable = ModelTable::Get($ModelTableName);
        $Entity = $ModelTable->LoadByPrimaryKey($EntityPrimaryKey);
        if ($Entity != null) {
            // sets the redirect back url 
            // if somebody clicks on new in a foreign table and submit the form he gets redirect back to this entry
            $RedirectBackUrl =  '~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' . strtolower($ModelTableName) . ':edit:' . $Entity->GetPrimaryKey() . '/';
            $Fields = array();

            // data send
            if ($this->Request->IsPOST('submit')) {
                $ValidationState = new ValidationState();
                $Fields = array();
                foreach ($this->ConfigurationHelper->GetFieldList() as $FieldName) {
                    $Type = $this->ConfigurationHelper->GetFieldType($FieldName);
                    $FieldClassName = '\\PvikAdminTools\\Library\\Fields\\' . $Type;
                    if (!class_exists($FieldClassName)) {
                        throw new \Exception('PvikAdminTools: The type ' . $Type . ' does not exists. Used for the field ' . $FieldName);
                    }
                    $Field = new $FieldClassName($FieldName, $Entity, $this->Request, $ValidationState);
                    /* @var $Field PvikAdminToolsBaseField */
                    array_push($Fields, $Field);
                    $ValidationState = $Field->Validation();
                }

                $this->ViewData->Set('ValidationState', $ValidationState);

                if ($ValidationState->IsValid()) {
                    // update all fields
                    foreach ($Fields as $Field) {
                        /* @var $Field PvikAdminToolsBaseField */
                        $Field->Update();
                    }
                    $Entity->Update();
                }
                
                $RedirectBackUrlAsParameter = $this->Request->GetGET('redirect-back-url');
                if($RedirectBackUrlAsParameter!=null){
                    // the user was was in edit mode of an table entry
                    // clicked on new in a foreign id and and created/updated/deleted a entry
                    // now we redirect back to the edit mode
                     $this->RedirectToPath(urldecode($RedirectBackUrlAsParameter));
                }
                else {

                    $SingleHtml = new \PvikAdminTools\Library\SingleHtml($Entity, $ValidationState, $this->Request);
                    $SingleHtml->SetForeignTableButtonRedirectBackUrl($RedirectBackUrl);
                    $this->ViewData->Set('SingleHtml', $SingleHtml);

                    $this->ExecuteViewByAction('EditEntry');
                }
            } else {
                $ValidationState = new ValidationState();

                $SingleHtml = new \PvikAdminTools\Library\SingleHtml($Entity, $ValidationState, $this->Request);
                $SingleHtml->SetForeignTableButtonRedirectBackUrl($RedirectBackUrl);
                $this->ViewData->Set('SingleHtml', $SingleHtml);

                $this->ExecuteViewByAction('EditEntry');
            }
        } else {
            $this->RedirectToTables();
        }
    }

    /**
     * Logic for deleting an entry.
     * @param type $ModelTableName
     * @param type $EntityPrimaryKey 
     */
    protected function DeleteEntry($ModelTableName, $EntityPrimaryKey) {
        $Entity = ModelTable::Get($ModelTableName)->LoadByPrimaryKey($EntityPrimaryKey);
        if ($Entity != null) {
            $Entity->Delete();
        }
        
        $RedirectBackUrl = $this->Request->GetGET('redirect-back-url');
        if($RedirectBackUrl!=null){
            // the user was was in edit mode of an table entry
            // clicked on new in a foreign id and and created/updated/deleted a entry
            // now we redirect back to the edit mode
             $this->RedirectToPath(urldecode($RedirectBackUrl));
        }
        else {
            $Url = '~' . \Pvik\Core\Config::$Config['PvikAdminTools']['Url'] . 'tables/' . strtolower($ModelTableName) . ':list/';
            $this->RedirectToPath($Url);
        }
    }

}
