<?php

class Foto extends Flex {

    protected $tableName = 'fotos';
    protected $mapper = array(
        'id' => 'int',
        'id_tipo' => 'int',
        'tipo' => 'string',
        'img' => 'string',
        'descricao' => 'string',
        'ordem' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');
            
    public static $configGG = array(
        'nome' => 'Fotos',
        'class' => __CLASS__,
        'ordenacao' => 'ordem',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `fotos`;
        CREATE TABLE `fotos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_tipo` INT NOT NULL,
            `tipo` VARCHAR(255) NOT NULL,
            `img` VARCHAR(255) NULL,
            `descricao` TEXT NULL,
            `ordem` INT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static $tamImg = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
    );
    
    public static function changeTempImage($idAnt, $newId, $tipo) {
        try {
            $conn = new Connection();
            $statement = $conn->prepareStatement("UPDATE fotos SET id_tipo = :nid: WHERE id_tipo = :id: AND tipo = :tipo:");
            $statement->setInt("id", $idAnt);
            $statement->setInt("nid", $newId);
            $statement->setString("tipo", $tipo);
            $statement->executeQuery();
        } catch (SQLException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public static function deleteImage($id, $nmImage='img'){
        $obj = self::load($id);
        $tam = 'tam'.ucfirst($nmImage);
        Image::deleteImage(self::$$tam, $id, $obj->get('tipo').'/'. $obj->get('id_tipo').'/', $obj->get($nmImage));
        unset($obj);
    }
    
    public static function deleteByTipo($id, $tipo) {
        try {
            $conn = new Connection();
            $statement = $conn->prepareStatement("DELETE FROM fotos WHERE id_tipo = :id: AND tipo = :tipo:");
            $statement->setInt("id", $id);
            $statement->setString("tipo", $tipo);
            $statement->executeQuery();
        } catch (SQLException $e) {
            echo $e->getMessage();
            exit;
        }
    }
    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
        if($error==''){
            return true;
        }else{
            echo '<ul>'.$error.'</ul>';
            return false;
        }
    }

    public static function saveForm() {
        global $request;
        $classe = __CLASS__;
        $ret = array('success'=>false, 'obj'=> null);

        if(self::validate()){
            $id = $request->getInt('id');
            $obj = new $classe(array($id));

            if ($id == 0) {
                $obj->set('ativo', 1);
            } else {
                $obj = self::load($id);
            }
            
            $obj->set('descricao', $_POST['descricao']);
            
            $obj->save();

            
            echo 'Registro salvo com sucesso!';

            $ret['success'] = true;
            $ret['obj'] = $obj;   
        }

        return $ret;
    }

    public static function delete($ids) {
        global $defaultPath;
        $classe = __CLASS__;
        $obj = new $classe();

        $arrIds = (substr_count($ids, ',') > 0 ? explode(',', $ids) : array($ids));

        foreach ($arrIds as $id) { 
            self::deleteImage($id, 'img');
        } 

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {

        $string = "";
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);
               
        if ($codigo + 0 > 0) {
            $obj = self::load($codigo);
        }

        $img = $obj->getImage('r',0,$obj->get('tipo').'/'.$obj->get('id_tipo').'/');

        $string .= '
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">Imagem</div>
                <div class="card-body d-flex justify-content-center">
                    <img class="img-fluid" src="'.($img!=''?$img:__BASEPATH__.'uploads/img/no-pic.png').'" />
                </div>
            </div>
        </div>
        ';
        $string .= '</div>';
        $string .= '</div>';
        
        $string .= '
        <div class="col-sm-12">
            <div class="form-floating">
                <input type="text" class="form-control" name="descricao" value="'.$obj->get('descricao').'" placeholder="seu dado aqui" required>
                <label for="">Legenda*</label>
            </div>
        </div>
        ';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '<div class="dragable overflow-hidden position-relative row row-cols-1 row-cols-lg-2 row-cols-xl-3 g-3" data-table="fotos">';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<article class="col" id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</article>';
        }
       
        $string .= '</div>';
        
        return $string;
    }

    public static function getLine($obj){
        $img = $obj->getImage('t',0,$obj->get('tipo').'/'.$obj->get('id_tipo').'/', 'img');
        $module = $obj->getTableName();
        $id = $obj->get('id');

        return '
        <div class="card">
            <div class="ratio ratio-21x9">
                <img class="img-fluid object-fit-contain" src="'.($img!=''?$img:__BASEPATH__.'uploads/img/no-pic.png').'" />
            </div>
            <div class="d-flex p-2 align-items-center justify-content-between gap-1 flex-wrap bg-dark text-white">
                <button type="button" class="rounded-pill btn btn-dark text-white btn-sm" onclick="modalForm(`'. $module . '`, '.$id.', ``, function(){ getLine(`'.$module.'`, '.$id.');});">
                    <i class="small ti ti-pencil"></i>
                    <span class="d-none d-sm-inline-block">Editar</span>
                </button>
                <button type="button" class="rounded-pill btn btn-dark text-white btn-sm" onclick="delFormAction(`'.$module.'`, '.$id.', function(){ $(`#tr-'.$module.$id.'`).fadeOut(`slow`, function(){ $(this).remove(); }); });">
                    <i class="small ti ti-trash"></i>
                    <span class="d-none d-sm-inline-block">Excluir</span>
                </button>
                <button type="button" class="rounded-pill btn btn-dark text-white btn-sm" onclick="galleryRotateImage('.$id.');">
                    <i class="small ti ti-rotate"></i>
                    <span class="d-none d-sm-inline-block">Girar</span>
                </button>
                '.($obj->get('descricao') != '' ? '<span class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.$obj->get('descricao').'"></span>':'').'
                <span class="btn btn-dark rounded-pill drag-handler" data-id="'.$id.'"><span class="ti ti-arrows-move"></span></span>
            </div>
        </div>';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['descricao'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if($request->query('id_tipo') != ''){
            $filter = (int) $request->query('id_tipo');
            $paramAdd .= " AND id_tipo = {$filter } ";
        }
        
        if($request->query('tipo') != ''){
            $filter = $request->query('tipo');
            $paramAdd .= " AND tipo = '{$filter }' ";
        }

        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`dt_cad`) <= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }
        
        return $paramAdd;
    }

}

