<?php
namespace Pvik\Web;
/**
 * Class for a master page view.
 */
class MasterPage {
    /**
     * Contains the current view.
     * @var View 
     */
    protected $View = null;
    /**
     * Contains the path to the master page view.
     * @var string 
     */
    protected $MasterPagePath;
    /**
     * Contains the view data from the view.
     * @var \Pvik\Utils\KeyValueArray 
     */
    protected $ViewData = null;
    
    /**
     * Contains the Html Helper.
     * @var ViewHelpers\HtmlHelper
     */
    protected $Helper = null;

    /**
     *
     * @param string $MasterPagePath
     * @param View $View 
     */
    public function __construct($MasterPagePath,View $View){
        $this->MasterPagePath = $MasterPagePath;

        // set the view that uses the masterpage
        $this->View = $View;
        $this->ViewData = $View->GetViewData();
        $this->Helper = new \Pvik\Web\ViewHelpers\HtmlHelper();

        $this->ExecutePartialCode();
    }

    /**
     * Execute the partial master page view. Should be ran after the normal views ran.
     */
    protected function ExecutePartialCode(){
        // delete old content and ignore it
        ob_get_clean();
        // start output buffering
        // the core will output the html
        ob_start();
         // include partial code
        require($this->MasterPagePath);
    }
    
    /**
     * Get the content from a normal view that was executed before.
     * @param string $ContentId 
     */
    public function UseContent($ContentId){
        $Content = '';
        if($this->View!=null){
            $Contents = $this->View->GetContents();
            $Content = $Contents->Get($ContentId);
            if($Content==null){
                $Content = '';
            }
        }
        echo $Content;
    }
}
