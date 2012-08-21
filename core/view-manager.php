<?php
/**
 * Manages the views
 */
class ViewManager {
    /**
     * Contains the path of the current view.
     * @var string 
     */
    protected static $ViewPath = null;
    /**
     * Contains the path of the current master page view.
     * @var type 
     */
    protected static $MasterPagePath = null;

    /**
     * Executes a view.
     * @param string $ViewPath
     * @param Controller $Controller 
     */
    public static function ExecuteView($ViewPath,Controller $Controller){
        Log::WriteLine('Executing view: '. $ViewPath);
        self::$ViewPath = $ViewPath;
        $BaseView = new View($ViewPath,$Controller);
    }

    /**
     * Executes a master page view.
     * @param string $MasterPagePath
     * @param View $View 
     */
    public static function ExecuteMasterPage($MasterPagePath,View $View){
        Log::WriteLine('Executing masterpage: '. $MasterPagePath);
        self::$MasterPagePath = $MasterPagePath;
        $BaseMasterPage = new MasterPage($MasterPagePath, $View);
    }
}
