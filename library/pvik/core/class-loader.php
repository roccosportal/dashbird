<?php

namespace Pvik\Core;

use Pvik\Core\Path;

/**
 * This class is the last instance that tries to load a class if it wasn't found
 */
class ClassLoader {

    /**
     * Instance of the current class loader
     * @var ClassLoader 
     */
    protected static $Instance;

    /**
     * List of namespace associations to paths
     * @var array 
     */
    protected $NamespaceAssociationList;

    /**
     * 
     */
    protected function __construct() {
        $this->NamespaceAssociationList = array();
    }

    /**
     * Initialize this class loader
     */
    public function Init() {
        $this->Autoload();
    }

    /**
     * Returns the current instance
     * @return \Pvik\Core\ClassLoader
     */
    public static function GetInstance() {
        if (self::$Instance === null) {
            self::$Instance = new ClassLoader();
        }
        return self::$Instance;
    }

    /**
     * Registers an anoymous function for spl_autoload_register.
     * This function tries to load the class via the namespace 
     */
    protected function Autoload() {
        spl_autoload_register(function ($Class) {
                    if ($Class[0] !== '\\') {
                        $Class = '\\' . $Class;
                    }

                    $Instance = ClassLoader::GetInstance();

                    $Name = $Class;
                    foreach ($Instance->GetNamespaceAssociationList() as $Namespace => $Path) {
                        if (strpos($Name, $Namespace .'\\') === 0) { // starts with
                            $Name = str_replace($Namespace, $Path, $Name);
                            break;
                        }
                    }

                    $Path = Path::ConvertNameToPath($Name);
                    $Path = str_replace('//', '/', $Path);
                    $Path = Path::RealPath($Path . '.php');
                    //echo $Path;
                    if (file_exists($Path)) {

                        require $Path;
                        if (class_exists('\\Pvik\\Core\\Log')) {
                            Log::WriteLine('[Include] ' . $Path);
                        }
                        return true;
                    } else {
                        throw new \Pvik\Core\ClassNotFoundException($Class, $Path);
                    }
                });
    }

    /**
     * Returns the current list of namespace associations to paths
     * @return array
     */
    public function GetNamespaceAssociationList() {
        return $this->NamespaceAssociationList;
    }

    /**
     * Set a path association for a namespace
     * @param String $Namespace
     * @param String $Path
     * @return \Pvik\Core\ClassLoader
     */
    public function SetNamespaceAssociation($Namespace, $Path) {
        $this->NamespaceAssociationList[$Namespace] = $Path;
        return $this;
    }

}
