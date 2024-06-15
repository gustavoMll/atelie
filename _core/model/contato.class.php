<?php

class Contato extends Flex {

    protected $tableName = 'contatos';
    protected $mapper = array(
        'id' => 'int',
		'nome' => 'string',
        'id_pessoa'=>'int',
        'obs'=>'string',
		'setor' => 'string',
		'email' => 'string',
		'tel' => 'string',
		'tel2' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Contatos da pessoa',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
        'show-menu'=> false
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `contatospessoa`;
        CREATE TABLE `contatos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_pessoa` int(11) NOT NULL,
            `nome` VARCHAR(255) NOT NULL,
            `setor` VARCHAR(50) NULL,
            `email` VARCHAR(255) NULL,
            `tel` VARCHAR(18) NOT NULL,
            `tel2` VARCHAR(18) NULL,
            `obs` TEXT NULL,
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
        
    	if($_POST['id_pessoa'] == ''){
    		$error .= '<li>A pessoa n&atilde;o foi informada</li>';
    	}

    	if(!isset($_POST['nome']) || $_POST['nome'] == ''){
    		$error .= '<li>O campo "Nome" n&atilde;o foi informado</li>';
    	}
    	if(!isset($_POST['tel']) || $_POST['tel'] == ''){
    		$error .= '<li>O campo "Telefone" n&atilde;o foi informado</li>';
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
			$obj->set('id_pessoa', $_POST['id_pessoa']);
			$obj->set('setor', $_POST['setor']);
			$obj->set('email', $_POST['email']);
			$obj->set('tel', Utils::replace('/[^0-9]/','',$_POST['tel']));
			$obj->set('tel2', Utils::replace('/[^0-9]/','',$_POST['tel2']));
			$obj->set('obs', $_POST['obs']);

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
        $obj->set('avisos',1);

        if($obj->get('id_pessoa') == '') {
            $obj->set(('id_pessoa'), $request->get('id_pessoa'));
        }

        
        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
        	$codigo = time();
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }

        $string.= '
        <input name="id_pessoa" type="hidden" value="'.$obj->get('id_pessoa').'"/>
        ';
        
    	$string .= '
                <div class="row mt-0 g-lg-3">
                   <div class="col-sm-8 required">
                        <div class="form-floating">
                            <input class="form-control" name="nome" placeholder="" value="'.$obj->get('nome').'">
                            <label class="form-label">Nome</label>
                        </div>
                   </div> 
                   <div class="col-sm-4">
                        <div class="form-floating">
                            <input class="form-control" name="setor" placeholder="" value="'.$obj->get('setor').'">
                            <label class="form-label">Setor</label>
                        </div>
                   </div> 
                   <div class="col-sm-6">
                        <div class="form-floating">
                            <input class="form-control" name="email" placeholder="" value="'.$obj->get('email').'">
                            <label class="form-label">E-mail</label>
                        </div>
                   </div> 
                   <div class="col-sm-3 required">
                        <div class="form-floating">
                            <input class="form-control phone" name="tel" placeholder="" value="'.$obj->get('tel').'">
                            <label class="form-label">Telefone</label>
                        </div>
                   </div> 
                   <div class="col-sm-3">
                        <div class="form-floating">
                            <input class="form-control phone" name="tel2" placeholder="" value="'.$obj->get('tel2').'">
                            <label class="form-label">Telefone 2</label>
                        </div>
                   </div> 
                   
                    <div class="col-sm-12">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" name="obs" rows="5" value="'.$obj->get('obs').'"></textarea>
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
                <th class="col-sm-8">Nome</th>
                <th class="col-sm-4">Telefone</th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome'), false).'</td>
        <td>'.Utils::getTel($obj->get('tel')).'</td>'.GG::getResponsiveList(['Nome' => $obj->nome, 'Telefone' => $obj->get('tel')], $obj).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        if ((int) $request->query('id_pessoa') > 0) {
            $paramAdd .= " AND id_pessoa = {$request->query('id_pessoa')}";
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
