<?php

namespace PvikAdminTools\Library\Fields;

/**
 * Displays a checkbox.
 */
class Password extends Base {

    protected function AddHtmlSingleControl() {
        $Disabled = '';
        if ($this->ConfigurationHelper->FieldExists($this->FieldName)
                && $this->ConfigurationHelper->IsDisabled($this->FieldName)) {
            $Disabled = 'disabled="disabled"';
        }
        $this->Html .= '<input class="span8" name="' . $this->GetLowerFieldName() . '" type="password" ' . $this->GetPresetValue() . ' ' . $Disabled . ' />';
    }

    /**
     * Returns the preset value for the checkbox
     * @return string 
     */
    public function GetPresetValue() {
        if($this->IsNewEntity()){
            return '';
        }
        return 'value="******"';
    }

    /**
     * Validates the checkbox.
     * @return ValidationState 
     */
    public function Validation() {
        // ignore validation
        return $this->ValidationState;
    }

    /**
     * Returns the html for the overview.
     * @return string 
     */
    public function HtmlOverview() {
        return '******';
    }

    /**
     * Updates the entity.
     */
    public function Update() {
        $FieldName = $this->FieldName;
        $Value = $this->GetPost();
        if ($Value != '******') {
            $Random = md5(uniqid(mt_rand(), true));
            $Salt = '$2a$07$' . $Random . '$';
            $this->Entity->$FieldName = crypt($Value, $Salt);
        }
    }

}
