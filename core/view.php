<?php
/**
 * Class that class a partial view code.
 */
class View {
    /**
     * Contains the path to a master page if set up.
     * @var string 
     */
    protected $MasterPagePath = null;
    /**
     * Contains the differnt areas of content if the view uses a master page.
     * @var KeyValueArray 
     */
    protected $Contents;
    /**
     * Contains the id of the current content area.
     * @var string 
     */
    protected $CurrentContentId = null;
    /**
     * Contains the controller that executed this view.
     * @var Controller 
     */
    protected $Controller = null;
    /**
     * Contains the view data from the controller
     * @var type 
     */
    protected $ViewData = null;
    /**
     * Contains the path to the partial view
     * @var type 
     */
    protected $ViewPath = null;

    /**
     *
     * @param string $ViewPath
     * @param Controller $Controller 
     */
    public function __construct($ViewPath,Controller $Controller){
        $this->Contents = new KeyValueArray();
        $this->ViewPath = $ViewPath;
        $this->Controller = $Controller;

        $this->ViewData = $this->Controller->GetViewData();

        $this->ExecutePartialCode();
        
        If($this->MasterPagePath != null){
            ViewManager::ExecuteMasterPage(Core::RealPath($this->MasterPagePath), $this);
        }
    }

    /**
     * Executes the partial view.
     */
    protected function ExecutePartialCode(){
        if(!file_exists($this->ViewPath)){
            throw new Exception('View file doesn\'t exist: '. $this->ViewPath);
        }
        // include partial code
        require($this->ViewPath);
    }

    /**
     * Defines the master page.
     * @param type $MasterPagePath 
     */
    protected function UseMasterPage($MasterPagePath){
        $this->MasterPagePath = $MasterPagePath;
    }

    /**
     * Starts to fetch a content area.
     * @param type $ContentId 
     */
    protected function StartContent($ContentId) {
        if($this->MasterPagePath!=null){
            // delete old content that is outside of the content tags
            ob_get_clean();
            // set the content id and start getting all output content
            $this->CurrentContentId = $ContentId;
            ob_start();
        }
    }

    /**
     * Ends to fetch a content area and safes it into the Contents array.
     */
    protected function EndContent(){
        if($this->MasterPagePath!=null && $this->CurrentContentId!=null){
            // save the output content in a array to pass it to the masterpage
            $this->Contents->Set($this->CurrentContentId, ob_get_clean());
            $this->CurrentContentId = null;
            // start output buffering
            // if we use a masterpage every content have to be between content tags
            // we buffer contents outside of tags to ignore it
            ob_start();
        }
    }

    /**
     * Returns the content areas if the view used a master page.
     * @return KeyValueArray 
     */
    public function GetContents(){
        return $this->Contents;
    }

    /**
     * Returns the view data.
     * @return KeyValueArray 
     */
    public function GetViewData(){
        return $this->ViewData;
    }

}