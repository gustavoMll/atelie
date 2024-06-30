<?php

class Documento extends Flex {

    protected $tableName = 'documentos';
    protected $mapper = array(
        'id' => 'int',
        'descricao' => 'string',
        'obrigatorio' => 'int',
        'arquivo' => 'string',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Documentos',
        'class' => __CLASS__,
        'ordenacao' => 'descricao ASC',
        'envia-arquivo' => true,
        'show-menu' => true,
        'icon' => 'ti ti-file',
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `documentos`;
        CREATE TABLE `documentos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `descricao` VARCHAR(255) NOT NULL,
            `obrigatorio` INT(1) DEFAULT 1,
            `arquivo` VARCHAR(255) NOT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
        ";
    }


    public static function getDocs($params = 'descricao'){
        return self::search([
            's' => 'id,'.$params,
            'o' => 'descricao'
        ]);
    }

    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

        if(!isset($_POST['descricao']) || $_POST['descricao'] == ''){
            $error .= '<li>O campo "Descri&ccedil;&atilde;o" n&atilde;o foi informado</li>';
        }
        
        if(!isset($_POST['obrigatorio']) || $_POST['obrigatorio'] == ''){
            $error .= '<li>O campo "Obrigat&oacute;rio" n&atilde;o foi informado</li>';
        }
        
        if(!isset($_FILES['arquivo']) || $_FILES['arquivo']['name'] == ''){
            $error .= '<li>O campo "Arquivo" n&atilde;o foi informado</li>';
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

            $obj->set('descricao', $_POST['descricao']);
            $obj->set('obrigatorio', $_POST['obrigatorio']);
            $obj->set('arquivo', $_FILES['arquivo']['name']);

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

        Flex::dbDelete(new Pessoa(), "id IN({$ids})");
        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0){
        global $request, $objSession;
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
        <div class="col-sm-8 mb-3 required">
            <div class="form-floating">
                <input name="descricao" id="descricao" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('descricao').'"/>
                <label for="descricao" class="form-label">Descri&ccedil;&atilde;o</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 mb-3 required">
            <div class="form-floating">
                <select class="form-select" name="obrigatorio" required>
                    <option value="1" selected>Sim</option>
                    <option value="0" '.($obj->get('obrigatorio') == 0 ? 'selected' : '').'>N&atilde;o</option>
                </select>
                <label for="obrigatorio" class="form-label">Obriga&oacute;rio?</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-12 mb-3 required">
            <div class="form-floating">
                <input name="arquivo" id="arquivo" maxlength="255" type="file" class="form-control" value="'.$obj->get('arquivo').'"/>
                <label for="arquivo" class="form-label">Arquivo</label>
            </div>
        </div>';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-8">Descri&ccedil;&atilde;o</th>
                    <th class="col-sm-4">Obrigat&oacute;rio?</th>
                </tr>
                </thead>
                <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table>
              ';
        
        return $string;
    }

    public static function getLine($obj){
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('descricao')).'</td>
        '.GG::getResponsiveList([
            'Descri&ccedil;&atilde;o' => $obj->get('descricao'),
            'Obrigat&oacute?' => ($obj->get('obrigatorio') ? 'Sim' : 'N&atildeo')
        ], $obj).'
        <td>'.($obj->get('obrigatorio') ? 'Sim' : 'N&atildeo').'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';

        if($request->query('descricao') != ''){
            $paramAdd .= " AND `descricao` LIKE '%{$request->query('descricao')}%'";
        }
        
        if($request->query('obrigatoriedade') != ''){
            $paramAdd .= " AND `obrigatorio` = {$request->query('obrigatoriedade')}";
        }
        
        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`dt_cad`) <= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        
        $string = '';

        $string .= '
        <div class="col-sm-8 mb-3">
            <div class="form-floating">
                <input name="descricao" id="filterDescricao" type="text" class="form-control" value="'.$request->query('descricao').'" placeholder="seu dado aqui" />
                <label for="filterDescricao" class="form-label">Descri&ccedil;&atilde;o</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <select class="form-select" name="obrigatoriedade">
                    <option value="">Todos</option>
                    <option value="1">Obrigat&oacute;rios</option>
                    <option value="0">N&atilde;o obrigat&oacute;rios</option>
                </select>
                <label for="obrigatoriedade">Obrigatoriedade</label>
            </div>
        </div>
        ';

        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <input name="inicio" id="filterInicio" type="text" class="form-control date" value="'.$request->query('inicio').'" placeholder="seu dado aqui" />
                <label for="filterInicio">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <input name="fim" id="filterFim" type="text" class="form-control date" value="'.$request->query('fim').'" placeholder="seu dado aqui" />
                <label for="filteFim" class="form-label">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <select class="form-select" name="order" id="order">';
                foreach([
                    'descricao' => 'A-Z',
                    'descricao desc' => 'Z-A',
                    'id' => 'Mais antigo primeiro',
                    'id desc' => 'Mais recente primeiro',
                ] as $key => $value){
                    $string .= '<option value="'.$key.'"'.($request->query('order') == $key ? ' selected':'').'>'.$value.'</option>';
                }
        $string .= '
                </select>
                <label for="order" class="form-label">Ordem</label>
            </div>
        </div>    
        ';
        
        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <select class="form-select" name="offset" id="offset">';
                foreach($GLOBALS['QtdRegistros'] as $key){
                    $string .= '<option value="'.$key.'"'.($request->query('offset') == $key ? ' selected':'').'>'.$key.' registros</option>';
                }
        $string .= '
                </select>
                <label for="offset" class="form-label">Registros</label>
            </div>
        </div>';

        
        return $string;
    }
}