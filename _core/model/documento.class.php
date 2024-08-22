
<?php

class Documento extends Flex {

    protected $tableName = 'documentos';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'descricao' => 'string',
        'pdf'=> 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Documentos',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `documentos`;
        CREATE TABLE `documentos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(100) DEFAULT NULL,
            `descricao` text DEFAULT NULL,
            `pdf` varchar(100) DEFAULT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
       
        if($_POST['nome'] == ''){
            $error .= '<li>O campo "Nome" n&atilde;o foi informado</li>';
        }
        if($_POST['descricao'] == ''){
            $error .= '<li>O campo "Descri&ccedil;&atilde;o" n&atilde;o foi informado</li>';
        }

        if (isset($_FILES['pdf']) && $_FILES['pdf']['name'] != '') {
            if($_FILES['pdf']['error'] != 0){
                $error .= '<li>PDF inv&aacute;lido;</li>';
            }
        }
        
        if($error==''){
            return true;
        }else{
            echo '<ul>'.$error.'</ul>';
            return false;
        }
    }

    public static function saveForm() {
        global $request, $defaultPath;
        $classe = __CLASS__;
        $ret = array('success'=>false, 'obj'=> null);

        if(self::validate()){
            $id = $request->getInt('id');
            $obj = new $classe(array($id));

            if ($id > 0) {
                $obj = self::load($id);
            }
            
            $obj->set('nome', $_POST['nome']);
            $id = $obj->get('id');
            $fileBefore = '';
            if (isset($_FILES['pdf']) && $_FILES['pdf']['name'] != '') {
                $fileBefore = $obj->get('pdf');
                $obj->set('pdf', File::configureName($_FILES['pdf']['name']));
            }
            
            $obj->save();
            
            if (isset($_FILES['pdf']) && $_FILES['pdf']['name'] != '') {
                File::saveFromUpload($fileBefore, 'pdf', $id, $obj->getTableName());
            }
            
            echo 'Registro salvo com sucesso!';

            Utils::generateSitemap();

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

        
        $ret = $obj->dbDelete($obj, 'id IN('.$ids.')');
        Utils::generateSitemap();
        return $ret;
        }

    public static function deleteFile($id, $dest='pdf'){
        $obj = self::load($id);
        // print_r($obj);
        // exit;
        File::deleteFile($id, $obj->getTableName(), $obj->get('pdf'));
        unset($obj);
    }

    public static function form($codigo = 0) {
        global $request;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);
        
        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
            $codigo = time();
            $string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
        
        $string .= '
            <div class="col-sm-12 mb-3 required">
                <div class="form-floating">
                <input name="nome" id="nome" maxlength="255" type="text" class="form-control" placeholder="Doc" value="'.$obj->get('nome').'" required/>
                <label for="">Nome</label>
                </div>
            </div>';
        
            $string .= '
            <div class="col-sm-12 mb-3">
                <div class="form-floating">
                    <div class="input-group">
                        <span class="input-group-addon" placeholder="Doc"><span class="glyphicon glyphicon-file"></span></span>
                        <input name="pdf" type="file" class="form-control" value=""/>
                    </div>
                </div>
            </div>';

            if ($obj->get('pdf') != '') {
                $string .= GG::showFile($obj, 'Arquivo atual', 'pdf');
            }

           

            $string .= '
            <div class="form-group col-sm-12">
                <label for="">Descri&ccedil;&atilde;o</label>
                <textarea class="form-control ckeditor" name="descricao" id="descricao">'.$obj->get('descricao').'</textarea>
            </div>';
        
         
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table table-responsive table-striped">
            <thead>
              <tr>
                <th width="10" class="p-3">'.GG::getCheckboxHead().'</th>
                <th class="col-sm-12">Nome</th>
                <th width="10"></th>
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
        return '
        <td class="p-3">'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        $string = ''; 
        $string .= '
        <div class="form-group col-sm-4">
            <label for="">Nome</label>
            <input name="nome" type="text" class="form-control" value="'.$request->query('nome').'"/>
        </div>';

        $string .= '
            <div class="form-group col-sm-3">
                <label for="">Ordem</label>
                <select class="form-control" name="order">';
                foreach([
                    'nome' => 'A-Z',
                    'nome desc' => 'Z-A',
                    'id' => 'Mais antigo primeiro',
                    'id desc' => 'Mais recente primeiro',
                ] as $key => $value){
                    $string .= '<option value="'.$key.'"'.($request->query('order') == $key ? ' selected':'').'>'.$value.'</option>';
                }
        $string .= '
                </select>
            </div>';
        
        $string .= '
            <div class="form-group col-sm-3">
                <label for="">Registros</label>
                <select class="form-control" name="offset">';
                foreach($GLOBALS['QtdRegistros'] as $key){
                    $string .= '<option value="'.$key.'"'.($request->query('offset') == $key ? ' selected':'').'>'.$key.' registros</option>';
                }
        $string .= '
                </select>
            </div>';

        return $string;
    }
}
