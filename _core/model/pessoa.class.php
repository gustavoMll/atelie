<?php

class Pessoa extends Flex {

    protected $tableName = 'pessoas';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'dt_nasc' => 'string',
        'cpf' => 'string',
        'telefone1' => 'string',
        'telefone2' => 'string',
        'email' => 'string',
        'cep' => 'string',
        'endereco' => 'string',
        'numero' => 'int',
        'bairro' => 'string',
        'cidade' => 'string',
        'estado' => 'string',
        'complemento' => 'string',
        'referencia' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Pessoas',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `pessoas`;
        CREATE TABLE `pessoas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `cpf` VARCHAR(11) NOT NULL,
            `dt_nasc` DATE NULL,
            `telefone1` VARCHAR(20) NOT NULL,
            `telefone2` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(200) NOT NULL,
            `cep` VARCHAR(9) DEFAULT NULL,
            `endereco` VARCHAR(255) NOT NULL,
            `numero` int(11) NOT NULL,
            `bairro` VARCHAR(255) NOT NULL,
            `cidade` VARCHAR(255) NULL,
            `estado` VARCHAR(2) NULL,
            `complemento` VARCHAR(255) NULL,
            `referencia` VARCHAR(255) NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
        
        INSERT INTO `pessoas` (`nome`, `cpf`, `dt_nasc`, `telefone1`, `endereco`, `numero`, `bairro`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
        ('Administrador','11111111111', '', '', '', '', '', 0, NOW(), 0, CURDATE());
        ";
    }

    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

        if($_POST['nome'] == ''){
            $error .= '<li>O campo "Nome" n&atilde;o foi informado</li>';
        }
        
        if(!isset($_POST['cpf']) || $_POST['cpf'] == ''){
            $error .= '<li>O campo "CPF" n&atilde;o foi informado</li>';
        }

        if(isset($_POST['dt_nasc']) && $_POST['dt_nasc'] != '' && !Utils::dateValid($_POST['dt_nasc'])){
            $error .= '<li>O campo "Data de Nascimento" &eacute; inv&aacute;lido</li>';
        }
        
        if(isset($_POST['telefone1']) && $_POST['telefone1'] == ''){
            $error .= '<li>O campo "Telefone 1" n&atilde;o foi informado</li>';
        }

        if(isset($_POST['endereco']) && $_POST['endereco'] == ''){
            $error .= '<li>O campo "Rua" n&atilde;o foi informado</li>';
        }

        if(isset($_POST['numero']) && $_POST['numero'] == ''){
            $error .= '<li>O campo "N&uacute;mero" n&atilde;o foi informado</li>';
        }

        if(isset($_POST['bairro']) && $_POST['bairro'] == ''){
            $error .= '<li>O campo "Bairro" n&atilde;o foi informado</li>';
        }

        if($error==''){
            return true;
        }else{
            echo '<ul>'.$error.'</ul>';
            return false;
        }
    }

    public static function saveForm() {
        global $request, $objSession;
        $classe = __CLASS__;
        $ret = array('success'=>false, 'obj'=> null);
        if(self::validate()){
            $id = (int) $request->post('id_pessoa');
            $obj = new $classe(array($id));

            if ($id > 0) {
                $obj = self::load($id);
            }

            $obj->set('nome', $_POST['nome']);
            $obj->set('cpf', Utils::replace('/[^0-9]/','',$_POST['cpf']));
            $obj->set('dt_nasc', $_POST['dt_nasc'], 'd/m/Y');
            $obj->set('telefone1', Utils::replace('/[^0-9]/','',$_POST['telefone1']));
            $obj->set('telefone2', Utils::replace('/[^0-9]/','',$_POST['telefone2']));
            $obj->set('email', $_POST['email']);
            $obj->set('cep', Utils::replace('/[^0-9]/','',$_POST['cep']));
            $obj->set('endereco', $_POST['endereco']);
            $obj->set('numero', $_POST['numero']);
            $obj->set('bairro', $_POST['bairro']);
            $obj->set('cidade', $_POST['cidade']);
            $obj->set('estado', $_POST['estado']);
            $obj->set('complemento', $_POST['complemento']);
            $obj->set('referencia', $_POST['referencia']);

            $obj->save();

            $ret['success'] = true;
            $ret['obj'] = $obj;   
        }

        return $ret;
    }

    public static function form($codigo = 0) {
        global $request, $objSession, $Config;
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
                <input name="nome" id="nome" maxlength="100" type="text" class="form-control" placeholder="seu dado aqui" value="'.$obj->get('nome').'" required/>
                <label for="nome" class="form-label">Nome</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 mb-3 required">
            <div class="form-floating">
                <input name="cpf" id="cpf" maxlength="14" type="text" class="form-control cpf" placeholder="seu dado aqui" value="'.$obj->get('cpf').'" required/>
                <label for="cpf" class="form-label">CPF</label>
            </div>
        </div>';
       
        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input name="dt_nasc" id="dt_nasc" type="date" class="form-control dt_nasc" placeholder="seu dado aqui" value="'.$obj->get('dt_nasc').'"/>
                <label for="dt_nasc" class="form-label">Data de Nascimento</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input name="email" id="email" maxlength="200" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('email').'"/>
                <label for="email" class="form-label">E-mail</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3 required">
            <div class="form-floating">
                <input name="telefone1" id="telefone1" maxlength="11" type="text" class="form-control phone" placeholder="seu dado aqui" value="'.$obj->get('telefone1').'" required/>
                <label for="telefone1" class="form-label">Telefone 1</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="telefone2" id="telefone2" maxlength="11" type="text" class="form-control phone" placeholder="seu dado aqui" value="'.$obj->get('telefone2').'"/>
                <label for="telefone2" class="form-label">Telefone 2</label>
            </div>
        </div>';

       
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="cep" id="pessoacep" maxlength="10" type="text" placeholder="seu dado aqui" class="form-control cep" onchange="getAddress(`pessoa`)" value="'.$obj->get('cep').'"/>
                <label for="pessoacep" class="form-label">CEP</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3 required">
            <div class="form-floating">
                <input name="endereco" id="pessoaendereco" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('endereco').'" required/>
                <label for="pessoaendereco" class="form-label">Rua</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-4 mb-3 required">
            <div class="form-floating">
                <input name="bairro" id="pessoabairro" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('bairro').'" required/>
                <label for="pessoabairro" class="form-label">Bairro</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 col-md-2 mb-3 required">
            <div class="form-floating">
                <input name="numero" id="numero" min="0" type="number" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('numero').'" required/>
                <label for="numero" class="form-label">N&uacute;mero</label>
            </div>
        </div>';
       
        $string .= '
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="form-floating">
                <input name="cidade" id="pessoacidade" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('cidade').'"/>
                <label for="pessoacidade" class="form-label">Cidade</label>
            </div>
        </div>';
       
        $string .= '
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="form-floating">
                <select class="form-select" name="estado" id="pessoaestado">';
                    foreach ($GLOBALS['Estados'] as $key => $value){
                        $string .='<option value="'.$key.'"'.($obj->get('estado') == $key ? " selected " : "").'>'.$value.'</option>';
                    }
                    $string .='
                </select>
                <label for="pessoaestado" class="form-label">Estado</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="referencia" id="referencia" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('referencia').'"/>
                <label for="referencia" class="form-label">Refer&ecirc;ncia</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="complemento" id="complemento" maxlength="255" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('complemento').'"/>
                <label for="complemento" class="form-label">Complemento</label>
            </div>
        </div>';

       
        return $string;
    }

     public static function filter($request) {
        $paramAdd = '1=1';

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        
        $string = '';

        $string .= '
        <div class="col-sm-8 mb-3">
            <div class="form-floating">
                <input name="nome" id="filterNome" type="text" class="form-control" value="'.$request->query('nome').'" placeholder="seu dado aqui" />
                <label for="filterNome" class="form-label">Nome</label>
            </div>
        </div>';
       
        $string .= '
        <div class="col-sm-4 col-md-4 mb-3">
            <div class="form-floating">
                <input name="cpf" id="filterCpf" type="text" class="form-control" value="'.$request->query('cpf').'" placeholder="seu dado aqui" />
                <label for="filterCpf" class="form-label">CPF</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-4 col-md-4 mb-3">
            <div class="form-floating">
                <input name="telefone" id="filterTelefone" type="text" class="form-control" value="'.$request->query('telefone').'" placeholder="seu dado aqui" />
                <label for="filterTelefone" class="form-label">Telefone</label>
            </div>
        </div>';
      
        $string .= '
        <div class="col-sm-4 col-md-4 mb-3">
            <div class="form-floating">
                <input name="cep" id="filterCEP" type="text" class="form-control" value="'.$request->query('cep').'" placeholder="seu dado aqui" />
                <label for="filterCEP" class="form-label">CEP</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 col-md-4 mb-3">
            <div class="form-floating">
                <input name="endereco" id="filterRua" type="text" class="form-control" value="'.$request->query('endereco').'" placeholder="seu dado aqui" />
                <label for="filterendereco" class="form-label">Endere&ccedil;o</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="bairro" id="filterBairro" type="text" class="form-control" value="'.$request->query('bairro').'" placeholder="seu dado aqui" />
                <label for="filterBairro" class="form-label">Bairro</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="cidade" id="filterCidade" type="text" class="form-control" value="'.$request->query('cidade').'" placeholder="seu dado aqui" />
                <label for="filterCidade" class="form-label">Cidade</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="estado" id="filterEstado" maxlength="2" type="text" class="form-control" value="'.$request->query('estado').'" placeholder="seu dado aqui" />
                <label for="filterEstado" class="form-label">Estado</label>
            </div>
        </div>';
      
        $string .= '
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="form-floating">
                <input name="inicio" id="filterInicio" type="text" class="form-control date" value="'.$request->query('inicio').'" placeholder="seu dado aqui" />
                <label for="filterInicio">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-3 mb-3">
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
        <div class="col-sm-6 col-md-12 mb-3">
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

