<?php

class Permissao extends Flex {

    protected $tableName = 'permissoes';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'modulo' => 'string',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');
    

    public static $configGG = array(
        'nome' => 'Permiss&otilde;es',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `permissoes`;
        CREATE TABLE `permissoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `modulo` VARCHAR(20) NOT NULL,
            `ativo` int(1) DEFAULT 1,
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
        
        if($_POST['modulo'] == ''){
            $error .= '<li>O campo "M&oacute;dulo" n&atilde;o foi informado</li>';
        }
        $_POST["modulo"] = trim($_POST["modulo"]);
        if (self::exists("modulo='{$_POST['modulo']}' ".$paramAdd)) {
            $error .= '<li>M&oacute;dulo j&aacute; cadastrado;</li>';
        }

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
            
            $obj->set('nome', $_POST['nome']);
            $obj->set('modulo', $_POST['modulo']);
            $obj->save();

            $id = $obj->get('id');
            
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

        //$arrIds = (substr_count($ids, ',') > 0 ? explode(',', $ids) : array($ids));
        //foreach ($arrIds as $id) { } 

        Flex::dbDelete(new PermissaoUsuario(), "id_permissao IN({$ids})");
        return Flex::dbDelete($obj, "id IN({$ids})");
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
        <div class="col-sm-6 mb-3 mb-sm-0">
            <div class="form-floating">
                <input name="nome" id="nome" onblur="if($(`#modulo`).val() == ``){ configuraUrl(this, `modulo`); }" maxlength="100" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('nome').'" required/>
                <label for="">Nome</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="form-floating">
                <input name="modulo" id="modulo" maxlength="20" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('modulo').'" required/>
                <label for="">M&oacute;dulo</label>
            </div>
        </div>';
        

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table atelie-table table-striped">
            <thead>
              <tr>
                <th width="10">'.GG::getCheckboxHead().'</th>
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
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>'.GG::getResponsiveList(['Nome' => $obj->nome], $obj).'
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome','modulo'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        $string = ''; 
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="nome" type="text" placeholder="seu dado aqui" class="form-control" value="'.$request->query('nome').'"/>
                <label for="">Nome</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6  mb-3">
            <div class="form-floating">
                <input name="modulo" type="text" placeholder="seu dado aqui" class="form-control" value="'.$request->query('modulo').'"/>
                <label for="">M&oacute;dulo</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="inicio" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('inicio').'"/>
                <label for="">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="fim" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('fim').'"/>
                <label for="">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-3">    
            <div class="form-floating">
                <select class="form-select" name="order">';
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
                <label for="">Ordem</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3">    
            <div class="form-floating">
                <select class="form-select" name="offset">';
                foreach($GLOBALS['QtdRegistros'] as $key){
                    $string .= '<option value="'.$key.'"'.($request->query('offset') == $key ? ' selected':'').'>'.$key.' registros</option>';
                }
        $string .= '
                </select>
                <label for="">Registros</label>
            </div>
        </div>';

        return $string;
    }

}