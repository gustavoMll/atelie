<?php

class PermissaoUsuario extends Flex {

    protected $tableName = 'permissoesusuario';
    protected $mapper = array(
        'id' => 'int',
        'id_permissao' => 'int',
        'id_usuario' => 'int',
        'sel' => 'int',
        'ins' => 'int',
        'upd' => 'int',
        'del' => 'int',
        'usr_cad' => 'string', 
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Permiss&otilde;es do Usu&aacute;rio',
        'class' => __CLASS__,
        'ordenacao' => 'permissao ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `permissoesusuario`;
        CREATE TABLE `permissoesusuario` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_permissao` int(11) NOT NULL,
            `id_usuario` int(11) NOT NULL,
            `sel` int(1) DEFAULT 1,
            `ins` int(1) DEFAULT 1,
            `upd` int(1) DEFAULT 1,
            `del` int(1) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    protected $permissao = null;
    public function getPermissao() {
        if (!$this->permissao || $this->permissao->get('id') != $this->get('id_permissao')) {
            $this->permissao = Permissao::load($this->get('id_permissao'));
        }
        return $this->permissao;
    }

    protected $usuario = null;
    public function getUsuario() {
        if (!$this->usuario || $this->usuario->get('id') != $this->get('id_usuario')) {
            $this->usuario = Usuario::load($this->get('id_usuario'));
        }
        return $this->usuario;
    }

    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
        if($_POST['id_usuario'] == ''){
            $error .= '<li>O Usu&aacute;rio n&atilde;o foi informado</li>';
        }
        
        if($_POST['id_permissao'] == ''){
            $error .= '<li>A Permiss&atilde;o n&atilde;o foi informada</li>';
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

            if ($id > 0) {
                $obj = self::load($id);
            }
            $obj->set('id_permissao', $_POST["id_permissao"]);
            $obj->set('id_usuario', $_POST["id_usuario"]);
            $obj->set('sel', (isset($_POST["sel"]) ? 1 : 0));
            $obj->set('ins', (isset($_POST["ins"]) ? 1 : 0));
            $obj->set('upd', (isset($_POST["upd"]) ? 1 : 0));
            $obj->set('del', (isset($_POST["del"]) ? 1 : 0));

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

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function search($params = ['fields' => 'id']) {
        $classe = __CLASS__;
        $obj = new $classe();
        $objP = new Permissao();

        $params['j'] = " INNER JOIN (
            SELECT 
                id as permissao_id, 
                nome permissao,
                modulo 
            FROM 
                {$GLOBALS['DBPREFIX']}{$objP->getTableName()}
        ) b ON {$obj->getTableName()}.id_permissao = b.permissao_id
        ";
        return parent::search($params);
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
        
        if($obj->get('id_usuario')==''){
            $obj->set('id_usuario', $request->getInt('id_usuario'));
        }
        $string .= '<input name="id_usuario" type="hidden" class="form-control" value="' . $obj->get('id_usuario') . '"/>';
        
        $string .= '
        <div class="col-lg-6 mb-3">
            <label for="pu_id_permissao" class="form-label">M&oacute;dulo</label>
            <div class="input-group">
                <select class="form-select" name="id_permissao" id="pu_id_permissao">
                    <option value="" selected disabled>Selecione</option>';
                    $rs = Permissao::search([
                        'fields' => 'id,nome',
                        'order' => 'nome ASC'
                    ]);
                    while($rs->next()){
                        $string .= '<option value="'.$rs->getInt('id').'" '.($obj->get('id_permissao') == $rs->getInt('id')? " selected " : "").'>'.$rs->getString('nome').'</option>';
                    } 
            $string .= '
                </select>
                <button type="button" class="btn btn-secondary btn-sm addcat px-3" onclick="javascript:modalForm(`permissoes`,0,``, () => updateOptionField(`permissoes`, ``, `#pu_id_permissao`, `nome`));">
                    <i class="ti ti-plus"></i>
                </button>
            </div>
        </div>';
        
        $string .= '
        <div class="col-lg-6 mb-3">

            <p class="mb-2">Permiss&atilde;o</p>

            <div class="d-flex align-items-center gap-1">

                <div>
                    <input type="checkbox" class="btn-check" id="sel" name="sel" '.($obj->get('sel') == 1 ? " checked" : "").' value="1">
                    <label class="btn btn-outline-secondary '.($obj->get('sel') == 1 ? " active" : "").'" for="sel">Ver</label>
                </div>
                
                <div>
                    <input type="checkbox" class="btn-check" id="ins" name="ins" '.($obj->get('ins') == 1 ? " checked" : "").' value="1">
                    <label for="ins" class="btn btn-outline-secondary '.($obj->get('ins') == 1 ? " active" : "").'">Inserir</label>
                </div>
                
                <div>
                    <input type="checkbox" class="btn-check" id="upd" name="upd" '.($obj->get('upd') == 1 ? " checked" : "").' value="1">
                    <label for="upd" class="btn btn-outline-secondary '.($obj->get('upd') == 1 ? " active" : "").'">Editar</label>
                </div>
                
                <div>
                    <input type="checkbox" class="btn-check" id="del" name="del" '.($obj->get('del') == 1 ? " checked" : "").' value="1">
                    <label for="del" class="btn btn-outline-secondary '.($obj->get('del') == 1 ? " active" : "").'">Excluir</label>
                </div>

            </div>

        </div>';

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table table-responsive table-striped">
            <thead>
              <tr>
                <th class="col-sm-8 p-3">Permiss&atilde;o</th>
                <th class="col-sm-1 text-center">Ver</th>
                <th class="col-sm-1 text-center">Inserir</th>
                <th class="col-sm-1 text-center">Editar</th>
                <th class="col-sm-1 text-center">Apagar</th>
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
        <td class="link-edit p-3">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getPermissao()->get('nome'), false).'</td>
        <td class="small text-center align-middle"><strong><span class="ti ti-'.($obj->get('sel')==1?'check text-success':'x text-danger').'"></span></td>
        <td class="small text-center align-middle"><strong><span class="ti ti-'.($obj->get('ins')==1?'check text-success':'x text-danger').'"></span></td>
        <td class="small text-center align-middle"><strong><span class="ti ti-'.($obj->get('upd')==1?'check text-success':'x text-danger').'"></span></td>
        <td class="small text-center align-middle"><strong><span class="ti ti-'.($obj->get('del')==1?'check text-success':'x text-danger').'"></span></td>        
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        
        if( (int) $request->query('id_usuario') > 0){
            $paramAdd .= " AND id_usuario IN(".((int) $request->query('id_usuario')).")";
        }
        
        if( (int) $request->query('id_permissao') > 0){
            $paramAdd .= " AND id_permissao IN(".((int) $request->query('id_permissao')).")";
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
        return '';
    }

}