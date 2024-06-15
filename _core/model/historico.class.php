<?php

class Historico extends Flex {

    protected $tableName = 'historico';
    protected $mapper = array(
        'id' => 'int',
        'id_projeto' => 'int',
		'descricao' => 'string',
        'data' => 'string',
		'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Histórico',
        'class' => __CLASS__,
        'ordenacao' => 'data DESC',
        'envia-arquivo' => false,
        'show-menu' => false,
        'icon' => "ti ti-heart-handshake"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `historico`;
        CREATE TABLE `historico` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_projeto` int(11) NOT NULL,
            `descricao` TEXT NOT NULL,
            `data` DATETIME NOT NULL,
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
        
    	if($_POST['descricao'] == ''){
    		$error .= '<li>O campo "Descri&ccedil;&atilde;o" n&atilde;o foi informado</li>';
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

			$obj->set('id_projeto', (int) $_POST['id_projeto']);
			$obj->set('descricao', $_POST['descricao']);

            $data = date('Y-m-d H:i');
           
            if(isset($_POST['dia']) && $_POST['dia'] != '' && isset($_POST['hora']) && $_POST['hora'] != ''){
                $data = Utils::dateFormat($_POST['dia'], 'Y-m-d') . ' ' . $_POST['hora'];
            }
			$obj->set('data', $data);
        
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
            $obj->set('id_projeto', $request->getInt('id_projeto'));
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
        

    	$string .= '
        <input name="id_projeto" type="hidden" value="'.$obj->get('id_projeto').'">
        <div class="tab-content p-4"> 
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <div class="form-floating">
                        <input name="dia" id="dia" type="text" placeholder="seu dado aqui" class="form-control date" value="'.Utils::dateFormat($obj->get('data'), 'd/m/Y').'"/>
                        <label for="">Dia</label>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="form-floating">
                        <input name="hora" id="hora" type="time" placeholder="seu dado aqui" class="form-control" value="'.Utils::dateFormat($obj->get('data'), 'H:i:s').'"/>
                        <label for="">Hora</label>
                    </div>
                </div>
                <div class="form-group col-sm-12 mt-3">
                    <label for="descricao'.$obj->getTableName().'">Descri&ccedil;&atilde;o</label>
                    <textarea class="form-control ckeditor" name="descricao" id="descricao'.$obj->getTableName().'">'.$obj->get('descricao').'</textarea>
                </div>
            </div>
        </div>';
		

        return $string;
    }

    public static function getTable($rs) {
        $string = '
        <div class="col-sm-12 px-2">
            <div class="row px-3 g-3">';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<div id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</div>';
        }
       
        $string .= '
            </div>
        </div>';
        
        return $string;
    }

    public static function getLine($obj){

        $rs = Usuario::search([
            's' => 'id',
            'w' => "login='{$obj->get('usr_ualt')}'",
        ]);

        $user = null;
        if($rs->next()){
            $user = Usuario::load($rs->getInt('id'));
        }
        return '
        <div class="col-sm-12 px-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <strong>'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $user->get('nome') . ' em '. Utils::dateFormat($obj->get('data'), 'd/m/Y - H:i'), false, false).'</strong>
                    </div>
                    <div>
                        <a 
                        href="javascript:;" 
                        onclick="delFormAction(`'.$obj->getTableName().'`, '.$obj->get('id').', function(){ $(`#tr-'.$obj->getTableName().$obj->get('id').'`).fadeOut(`slow`, function(){ $(this).remove(); }); });" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="right" 
                        data-bs-title="Excluir"
                        data-bs-trigger="hover"
                        aria-label="Delete record"
                        class="text-danger"
                        ><i class="ti ti-trash text-danger"></i></a> 
                    </div>
                </div>
                <div class="card-body">
                '.$obj->get('descricao').'
                </div>
            </div>
        </div>';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['decricao'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if($request->query('id_projeto') != ''){
            $paramAdd .= " AND id_projeto = {$request->query('id_projeto')} ";
        }
        
        if($request->query('id_pessoa') != ''){
            $paramAdd .= " AND id_projeto IN (SELECT id FROM projetos WHERE id_cliente = {$request->query('id_pessoa')}) ";
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
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="descricao" id="filterDescricao" type="text" class="form-control" value="'.$request->query('descricao').'" placeholder="seu dado aqui" />
                <label for="filterDescricao" class="form-label">Descri&ccedil;&atilde;o</label>
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
