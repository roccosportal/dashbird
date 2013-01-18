<?php
namespace PvikAdminTools\Controllers;
use \Pvik\Utils\ValidationState;
/**
 * Contains the logic for uploading a file.
 */
class Files extends Base{
    /**
     * Logic for uploading a file.
     */
    public function UploadFileAction(){
        if($this->CheckPermission()){
            $ValidationState = new ValidationState();
            $Uploaded = false;
            // post data send
            if($this->Request->IsPOST('submit')){
                $Folders = \Pvik\Core\Config::$Config['PvikAdminTools']['FileFolders'];
                $SelectedFolder =$this->Request->GetPOST('folder');
                $FolderValid = false;
                foreach($Folders as $Folder){
                    if($SelectedFolder==$Folder){
                        $FolderValid = true;
                        break;
                    }
                }
                if ($FolderValid&&isset($_FILES['file']) && $_FILES['file']['error'] == 0){
                    $FileName =  $_FILES['file']['name'];
                    if($this->Request->IsPOST('name')&&$this->Request->GetPOST('name')!=''){
                        $FileName = $this->Request->GetPOST('name');
                    }
                    $DiretoryName = dirname(\Pvik\Core\Path::RealPath($SelectedFolder . $FileName));
                    if(!is_dir($DiretoryName)){
                        if (!mkdir($DiretoryName, 0777, true)){
                            $ValidationState->SetError('File', 'error creating folder');
                        }
                    }
                    if($ValidationState->IsValid()){
                        move_uploaded_file($_FILES['file']['tmp_name'],\Pvik\Core\Path::RealPath($SelectedFolder . $FileName));
                        $Uploaded = true;
                    }
                    
                }
                else {
                    $ValidationState->SetError('File', 'error uploading');
                }
            }
            $this->ViewData->Set('ValidationState', $ValidationState);
            $this->ViewData->Set('Uploaded', $Uploaded);
            $this->ExecuteView();
        }
    }
}
