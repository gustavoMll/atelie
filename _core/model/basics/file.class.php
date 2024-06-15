<?php

class File {
    
    public static function saveFromUpload($fileBefore, $nmFile, $id, $dest){
        global $defaultPath;
        
        if(is_array($nmFile)){
            $upFile = $nmFile;
        }else{
            $upFile = $_FILES[$nmFile];
        }
        
        self::deleteFile($id, $dest, $fileBefore);
        $dest = $defaultPath."uploads/".$dest.($dest[(strlen($dest)-1)] == '/'?'':'/');
        $nameFile = $dest.str_pad($id+0, 7, '0',STR_PAD_LEFT).'_';
        $extesion = Utils::getFileExtension($upFile["name"]);
        $newName = self::configureName($upFile["name"]);
        if(!is_dir($dest)){
            mkdir($dest,0777,true);
        }
        chmod($dest, 0777);

        $file = $nameFile.$newName;
        $ok = move_uploaded_file($upFile["tmp_name"],$file);
        chmod($file, 0777);
        return $ok;
    }
    
    public static function deleteFile($id, $dest, $file){
        global $defaultPath;
        $dest = $defaultPath."uploads/".$dest.($dest[(strlen($dest)-1)] == '/'?'':'/');
        $nameFile = $dest.str_pad($id+0, 7, '0',STR_PAD_LEFT).'_'.$file;
        if(file_exists($nameFile)){
            unlink($nameFile);
        }
           
    }
    
    public static function configureName($name){
        return Utils::replace('/[^0-9a-zA-Z_\-\.]/i','',Utils::removeDiatrics(strtolower($name)));
    }
    
    
}
