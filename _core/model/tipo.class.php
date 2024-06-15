<?php

class Tipo extends Flex {

    protected $tableName = 'tipos';
    protected $mapper = array(
        'id' => 'int',
		'nome' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Tipos',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
        'show-menu'=> true,
        'icon' => 'ti ti-category'
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `tipos`;
        CREATE TABLE `tipos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }

    public static function getTipos(){
        return Tipo::search([
            's' => 'id, nome',
            'o' => 'nome'
        ]);
    }

    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

    	if(!isset($_POST['nome']) || $_POST['nome'] == ''){
    		$error .= '<li>O campo "Descrição" n&atilde;o foi informado</li>';
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
            
			$obj->set('nome', $_POST['nome']);

            $obj->save();

            $id = $obj->get('id');

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

        $ret = $obj->dbDelete($obj, 'id IN('.$ids.')');

        Utils::generateSitemap();

        return $ret;
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
        <div class="row g-2">
            <div class="col-sm-12 required">
                <div class="form-floating">
                    <input class="form-control" name="nome" placeholder="" value="'.$obj->get('nome').'">
                    <label class="form-label">Nome</label>
                </div>
            </div> 
        </div>';

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <div class="table-responsive"><table class="table lev-table table-hover">
            <thead>
              <tr>
                <th width="10">'.GG::getCheckboxHead().'</th>
                <th class="col-sm-12">Nome</th>
              </tr>
            </thead>
            <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr class="position-relative" id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table></div>';
        
        return $string;
    }

    public static function getLine($obj){
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>'.GG::getResponsiveList(['Nome' => $obj->nome], $obj).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        
        if ($request->query('nome') > 0) {
            $paramAdd .= " AND nome LIKE '%{$request->query('nome')}%'";
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
        <div class="col-sm-12 mb-3">
            <div class="form-floating">
                <input name="nome" id="filterNome" type="text" class="form-control" value="'.$request->query('nome').'" placeholder="seu dado aqui" />
                <label for="filterNome" class="form-label">Nome</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="inicio" id="filterInicio" type="text" class="form-control date" value="'.$request->query('inicio').'" placeholder="seu dado aqui" />
                <label for="filterInicio">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="fim" id="filterFim" type="text" class="form-control date" value="'.$request->query('fim').'" placeholder="seu dado aqui" />
                <label for="filteFim" class="form-label">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <select class="form-select" name="order" id="order">';
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
                <label for="order" class="form-label">Ordem</label>
            </div>
        </div>    
        ';
        
        $string .= '
        <div class="col-sm-6 mb-3">
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
