<?php

class Arquivo extends Flex {

    protected $tableName = 'arquivos';
    protected $mapper = array(
        'id' => 'int',
        'id_tipo' => 'int',
        'tipo' => 'string',
        'file' => 'string',
        'descricao' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');
            
    public static $configGG = array(
        'nome' => 'Arquivos',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `arquivos`;
        CREATE TABLE `arquivos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_tipo` INT NOT NULL,
            `tipo` VARCHAR(255) NOT NULL,
            `file` VARCHAR(255) NULL,
            `descricao` TEXT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    protected $usuario = null;
    public function getUsuario() {
        if (!$this->usuario || $this->usuario->get('login') != $this->get('usr_cad')) {
            
            $this->usuario = new Usuario();
            $this->usuario->set('nome','N&atilde;o informado');

            if($this->get('usr_cad') != ''){
                $rs = Usuario::search(['s'=>'id','w'=>"login='{$this->get('usr_cad')}'"]);
                if($rs->next()){
                    $this->usuario = Usuario::load($rs->getInt('id'));
                }   
            }
        }
        return $this->usuario;
    }

    public static function deleteFile($id, $nmFile='file'){
        $obj = self::load($id);
        File::deleteFile($id, $obj->get('tipo').'/'. $obj->get('id_tipo').'/', $obj->get($nmFile));
        unset($obj);
    }

    public static function changeTempImage($idAnt, $newId, $tipo) {
        $classe = __CLASS__;
        $obj = new $classe();
        
        $conn = new Connection();
        $statement = $conn->prepareStatement("UPDATE {$obj->getTableName()} SET id_tipo = :nid: WHERE id_tipo = :id: AND tipo = :tipo:");
        $statement->setInt("id", $idAnt);
        $statement->setInt("nid", $newId);
        $statement->setString("tipo", $tipo);
        $statement->executeQuery();
    }
    
    
    public static function deleteByTipo($id, $tipo) {
        $classe = __CLASS__;
        $obj = new $classe();
        
        $conn = new Connection();
        $statement = $conn->prepareStatement("DELETE FROM {$obj->getTableName()} WHERE id_tipo = :id: AND tipo = :tipo:");
        $statement->setInt("id", $id);
        $statement->setString("tipo", $tipo);
        $statement->executeQuery();
    
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
        $classe = __CLASS__;
        $obj = new $classe();

        $arrIds = (substr_count($ids, ',') > 0 ? explode(',', $ids) : array($ids));

        foreach ($arrIds as $id) { 
            self::deleteFile($id, 'file');
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
        
        $string .= '<div class="form-group col-sm-12 required">';
        $string .= '<label for="">Descri&ccedil;&atilde;o</label>';
        $string .= '<input type="text" class="form-control" name="descricao" value="'.$obj->get('descricao').'">';
        $string .= '</div>';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
        <table class="table table-responsive table-striped table-hover">
        <thead>
            <tr>
            <th class="text-center">Download</th>
            <th class="col-sm-8">Nome</th>
            <th class="text-right">Tamanho</th>
            <th class="text-center">Autor</th>
            </tr>
        </thead>
        <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table>';
        
        return $string;
    }

    public static function getLine($obj){
        $desc = $obj->get('descricao')!=''?$obj->get('descricao'):'Arquivo sem descri&ccedil;&atilde;o';
        $link = $obj->getFile(0, $obj->get('tipo').'/'.$obj->get('id_tipo').'/');
        $size = $obj->getFileSize(0,$obj->get('tipo').'/'.$obj->get('id_tipo').'/');

        return '
        <td data-label="Download"><a href="'.$link.'" target="_blank" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-cloud-download"></span> Download</a></td>
        <td data-label="Nome" class="td-link">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $desc).'</td>
        <td data-label="Tamanho" class="text-right">'.$size.'</td>
        <td data-label="Autor" class="text-center"><span class="glyphicon glyphicon-user" data-toggle="tooltip" data-placement="left" title="'.$obj->getUsuario()->get('nome').' em '.Utils::dateFormat($obj->get('dt_cad'), 'd/m/Y H:i:s') .'"></span> </td>
        ';
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
