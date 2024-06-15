<?php

class Image {
    
    public static $typesAllowed = array('jpg','jpeg','gif','png');

    public static function saveFromUpload($imgBefore, $nmImg, $sizes, $id, $dest){
        global $defaultPath;
        self::deleteImage($sizes, $id, $dest, $imgBefore);
        $dest = $defaultPath."uploads/".$dest.($dest[(strlen($dest)-1)] == '/'?'':'/');
        $nameImg = $dest.str_pad($id+0, 7, '0',STR_PAD_LEFT).'_';
        $extesion = Utils::getFileExtension($_FILES[$nmImg]["name"]);
        $newName = self::configureName($_FILES[$nmImg]["name"], $nmImg);
        $newName = self::configureName($_FILES[$nmImg]["name"], $nmImg);
        $original = \WideImage\WideImage::load($nmImg);
        if(!is_dir($dest)){
            mkdir($dest,0777,true);
        }
        chmod($dest, 0777);
        
        foreach ($sizes as $nm => $tam){

            if($original->getHeight() > $original->getWidth()){
                $img = ($tam['h'] == 0 ? $original->resize($tam['w'],null) : $original->resize(null,$tam['h']));
            }else{
                $img = ($tam['w'] == 0 ? $original : $original->resize($tam['w'],null));
            }
            
            if($nm == 'crop'){
                if($original->getHeight() > $original->getWidth()){
                    $img = $original->resize($tam['w'],null);
                }else{
                    $img = $original->resize(null,$tam['h']);
                }
                $img = $img->crop('center','center',$tam['w'],$tam['h']);

            }

            if($extesion =='png'){
                $img = $img->asTrueColor();
            }

            $img->saveToFile($nameImg.$nm.'_'.$newName);
            chmod($nameImg.$nm.'_'.$newName, 0777);
            $img->destroy();
        }
    }
    
    public static function deleteImage($sizes, $id, $dest, $image){
        global $defaultPath;
        $dest = $defaultPath."uploads/".$dest.($dest[(strlen($dest)-1)] == '/'?'':'/');
        $nameImg = $dest.str_pad($id+0, 7, '0',STR_PAD_LEFT).'_';
        
        foreach ($sizes as $nm => $tam){
            $name = $nameImg.$nm.'_'.$image;
            if(file_exists($name)){
                unlink($name);
            }
                
        }
    }    
    
    public static function configureName($name, $nmField = 'img', $toWebp=true){

        if(strpos($name, $nmField.'_') === false) $name = $nmField.'_'.$name;

        $pts = explode('.', $name);
        $extesion = end($pts);
        $newName = str_replace(' ','-',Utils::removeDiatrics(strtolower($name)));
        if($toWebp){
            return str_replace('.'.$extesion,'.webp',$newName);
        }
        return $newName;
    }
    
    
}
