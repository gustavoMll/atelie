<?php

class Usuario extends Pessoa {

    protected $tableName = 'usuarios';
    protected $mapper = array(
        'id' => 'int',
        'id_pessoa' => 'int',
        'login' => 'string',
        'senha' => 'string',
        'acesso_total' => 'int',
        'ip' => 'string',
        'ultimo_acesso' => 'date',
        'tentativas' => 'int',
        'ultima_tentativa' => 'date',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Usu&aacute;rios',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `usuarios`;
        CREATE TABLE `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_pessoa` int(11) NOT NULL,
            `login` VARCHAR(20) NOT NULL,
            `senha` VARCHAR(50) NOT NULL,
            `acesso_total` INT NULL,
            `ip` varchar(20) NULL,
            `token` varchar(20) NULL,
            `ultimo_acesso` DATETIME NULL,
            `tentativas` int(1) DEFAULT 0,
            `ultima_tentativa` DATETIME NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_pessoa) REFERENCES pessoas(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

        INSERT INTO `usuarios` (`id_pessoa`, `login`, `senha`, `acesso_total`, `tentativas`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
        (1, 'admin', 'e2178678cf061c82af8d6aee9a0c592b4ef1db60', 1, 0, '', NOW(), '', NOW());
        ";
    }

    public static $tamImg = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
    );
    
    protected $pessoa = null;
    
    public function getpessoa()
    {
        if(!Pessoa::exists($this->id_pessoa)){
            return new Pessoa();
        }
        return Pessoa::load($this->id_pessoa);
    }


    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
        if(isset($_POST['login']) && $_POST['login'] == ''){
            $error .= '<li>O campo "Login" n&atilde;o foi informado</li>';
        }

        if($id==0){
            $_POST["login"] = strtolower(trim($_POST["login"]));
            if(self::exists("login='{$_POST["login"]}' {$paramAdd}")){
                $error .= '<li>Login em uso;</li>';
            }
            
            if($_POST['senha'] == ''){
                $error .= '<li>O campo "Senha" n&atilde;o foi informado</li>';
            }
        }

        if($_POST['senha']!=$_POST['c_senha']){
            $error .= '<li>Senhas n&atilde;o condizem;</li>';
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
        $objPessoa = new Pessoa();
        if(self::validate() && $objPessoa::validate()){
            $id = $request->getInt('id');
            $obj = new $classe(array($id));
            
            
            if ($id > 0) {
                $obj = self::load($id);
                $objPessoa = Pessoa::load($id);
            }

            $ret = $objPessoa::saveForm(); 
            $id_pessoa = $ret['obj']->get('id');

            if($id_pessoa){
                
                if(isset($_POST['login'])) 
                    $obj->set('login', $_POST['login']);
                if($_POST["senha"] != '')
                    $obj->set('senha', self::encrypt($_POST["senha"]));
                if(isset($_POST['acesso_total']) && $objSession->get('acesso_total') == 1)
                    $obj->set('acesso_total', $_POST['acesso_total']);
                
                $obj->set('id_pessoa', $id_pessoa);
                $obj->save();
                $id = $obj->get('id');
                
                if(isset($_POST['tempId']) && $_POST['tempId']+0 >0 ){
                    $conn = new Connection();
                    $obj = new PermissaoUsuario();
                    $sql = "UPDATE {$GLOBALS['DBPREFIX']}{$obj->getTableName()} SET id_usuario = {$id} WHERE id_usuario = ".($_POST['tempId']+0);
                    $conn->prepareStatement($sql)->executeQuery();
                }

                echo 'Registro salvo com sucesso!';

                $ret['success'] = true;
                $ret['obj'] = $obj;   
            }
        }
        return $ret;
    }

    public static function delete($ids) {
        global $defaultPath;
        $classe = __CLASS__;
        $obj = new $classe();


        Flex::dbDelete(new PermissaoUsuario(), "id_usuario IN({$ids})");
        Flex::dbDelete(new Pessoa(), "id IN(SELECT id_pessoa FROM {$obj->tableName} WHERE id IN({$ids}))");
        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {
        global $request, $objSession;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);
        $id_pessoa = 0;
        
        if ($codigo > 0) {
            $obj = self::load($codigo);
            $id_pessoa = $obj->get('id_pessoa');
        }else{
            $codigo = time();
            $string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
        
        $string = '<input name="id_pessoa" type="hidden" value="'.$id_pessoa.'"/>';
        $string .= Pessoa::form($id_pessoa);


        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="login" id="login" maxlength="20" type="text" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('login').'" '.($obj->get('id')+0 > 0 ? 'disabled' : ' required').' />
                <label for="login" class="form-label">Login '.($obj->get('id')+0 > 0 ? '' : ' *').'</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="senha" id="senha" maxlength="32" type="password" placeholder="seu dado aqui" class="form-control" '.($obj->get('id')+0 > 0 ? '' : ' required').' data-type="togglePassword"/>
                <label for="senha" class="form-label">Senha '.($obj->get('id')+0 > 0 ? '' : '*').'</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 col-md-4 mb-3">
            <div class="form-floating">
                <input name="c_senha" id="c_senha" maxlength="32" type="password" placeholder="seu dado aqui" class="form-control" '.($obj->get('id')+0 > 0 ? '' : ' required').' data-type="togglePassword"/>
                <label for="c_senha" class="form-label">Confirmar Senha '.($obj->get('id')+0 > 0 ? '' : '*').'</label>
            </div>
        </div>';
        
        if($objSession->get('acesso_total') == 1){
            $string .= '
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="form-floating">
                    <select class="form-select" name="acesso_total" id="acesso_total">
                        <option value="1"'.($obj->get('acesso_total') == 1 ? ' selected':'').'>Sim</option>
                        <option value="0"'.($obj->get('acesso_total') != 1 ? ' selected':'').'>N&atilde;o</option>
                    </select>
                    <label for="acesso_total" class="form-label">Acesso total</label>
                </div>
            </div>';
            
            $string .= '
            <div class="form-group col-sm-12 d-flex align-items-center gap-3 mt-5 mb-3">
                <h4 class="mb-0 fw-bold text-primary text-uppercase">
                    Permiss&otilde;es
                </h4>
                <button type="button" onclick="javascript: modalForm(`permissoesusuario`,0,`/id_usuario/'.$codigo.'`,loadPermissoes);" class="btn btn-sm text-white btn-secondary">
                    <i class="ti ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span>
                </button>
            </div>
            <script> 
                function loadPermissoes(resp){ 
                    tableList(`permissoesusuario`, `id_usuario='.$codigo.'&offset=10`, `txtpermissoes`, false);
                } 
            </script>
            <div class="form-group col-sm-12" id="txtpermissoes">'.GG::moduleLoadData('loadPermissoes();').'</div>';
            
        }

        return $string;
    }

    public static function getPreviewImage($obj, $nmImg = "img", $type="user"){
        $nameImg = $obj->get($nmImg);
        $sourceImage = $obj->getImage('r', 0, '', $nmImg);
        $string = '';

        if($type=="profile"){
            $string .= '
            <div class="col-sm-12 '.($nameImg != '' ? '' : 'd-none').' mb-3" id="imagem_imgProfile_'.$obj->getTableName().'">
                <article class="card text-white d-flex align-items-center justify-content-center card-preview">
                    <div class="preview-actions d-flex align-items-center gap-3 position-absolute">';
                        if($nameImg != ''){
                            $string .= '<button id="btndel_imgProfile_'.$obj->getTableName().'" type="button" class="btn btn-danger btn-sm" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');">
                            <i class="ti ti-trash"></i> Excluir imagem
                        </button>';
                        }
                        $string .= '<button id="btnchange_imgProfile_'.$obj->getTableName().'" type="button" class="btn btn-primary btn-sm" '.($nameImg != '' ? 'style="display:none;"' : '').' onclick="deletePreviewImage('.($nameImg != '' ? '`'.$sourceImage.'` ' : '``').', `imgProfile`, `'.$obj->getTableName().'`)">
                            <i class="ti ti-x"></i>
                            Cancelar alteração
                        </button>
                    </div>';
                    $string .= '<figure class="ratio w-25 ratio-1x1 rounded-circle overflow-hidden card-img mb-0">
                        <img src="'.$sourceImage.'" id="preview_imgProfile_'.$obj->getTableName().'" class="img-preview object-fit-cover" alt="..."/>
                    </figure>
                </article>
            </div>';
            return $string;
        }

        $string .= '<div class="col-sm-12 '.($nameImg != '' ? '' : 'd-none').' mb-3" id="imagem_'.$nmImg.'_'.$obj->getTableName().'">
            <article class="card text-white d-flex align-items-center justify-content-center card-preview">
                <div class="preview-actions d-flex align-items-center gap-3 position-absolute">';
                    if($nameImg != ''){
                        $string .= '<button id="btndel_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-danger btn-sm" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');">
                        <i class="ti ti-trash"></i> Excluir imagem
                    </button>';
                    }
                    $string .= '<button id="btnchange_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-primary btn-sm" '.($nameImg != '' ? 'style="display:none;"' : '').' onclick="deletePreviewImage('.($nameImg != '' ? '`'.$sourceImage.'` ' : '``').', `'.$nmImg.'`, `'.$obj->getTableName().'`)">
                        <i class="ti ti-x"></i>
                        Cancelar alteração
                    </button>
                </div>';
                $string .= '<figure class="ratio w-25 ratio-1x1 rounded-circle overflow-hidden card-img mb-0">
                    <img src="'.$sourceImage.'" id="preview_'.$nmImg.'_'.$obj->getTableName().'" class="img-preview object-fit-cover" alt=""/>
                </figure>
            </article>
        </div>';
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10" class="p-2">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-3">Nome</th>
                    <th class="col-sm-3">Email</th>
                    <th class="col-sm-3">Login</th>
                    <th class="col-sm-3">&Uacute;ltimo acesso</th>
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
        <td class="p-2">'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getPessoa()->get('nome')).'</td>
        '.GG::getResponsiveList([
            'Nome' => $obj->getPessoa()->get('nome'),
            'E-mail' => $obj->getPessoa()->get('email'),
            'Login' => $obj->login, 
            '&Uacute;ltimo Acesso' => (Utils::dateValid($obj->get('ultimo_acesso')) ? Utils::dateFormat($obj->get('ultimo_acesso'),'d/m/Y H:i:s').' pelo IP <strong>'.$obj->get('ip').'</strong>' : '-')
        ], $obj).'
        <td>'.($obj->getPessoa()->get('email') != '' ? $obj->getPessoa()->get('email') : '-').'</td>
        <td>'.$obj->get('login').'</td>
        <td class="small">'.(Utils::dateValid($obj->get('ultimo_acesso')) ? Utils::dateFormat($obj->get('ultimo_acesso'),'d/m/Y H:i:s').' pelo IP <strong>'.$obj->get('ip').'</strong>' : '-').'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';

        foreach(['nome', 'cpf', 'cep', 'rua', 'bairro', 'cidade', 'estado'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND id IN (SELECT id FROM pessoas WHERE `$key` LIKE '%{$request->query($key)}%')";
            }
        }

        if($request->query('telefone') != ''){
            $paramAdd .= " AND id IN (SELECT id FROM pessoas WHERE `telefone1` LIKE '%{$request->query('telefone')}%' OR `telefone2` LIKE '%{$request->query('telefone')}%')";
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
        return Pessoa::searchForm($request);
    }

    //other modules and functions
    public static $key = '@|@#';
    public static $bruteForceTime = 60;
    
    public static function encrypt($senha) {
        return sha1(self::$key . $senha);
    }

    public function hasPermition($module, $action='sel'){
        global $objSession;
        if($objSession->get('acesso_total') == 1 || in_array($module, ['arquivos', 'fotos']))
            return true;
        $permissoes = $objSession->getPermissoes();
        return isset($permissoes[$module]) && $permissoes[$module][$action] == 1;
    }
    
    
    public static function storeSession($objeto) {
        $_SESSION[$GLOBALS["Sessao"]]['obj'] = $objeto;
        $_SESSION[$GLOBALS["Sessao"]]["autorizado"] = "S";
        $_SESSION[$GLOBALS["Sessao"]]["menus"] = [];
        $_SESSION[$GLOBALS["Sessao"]]["permissoes"] = [];
    }

    public static function destroySession() {

        unset($_SESSION[$GLOBALS["Sessao"]]);
    }

    public static function auth($login, $senha) {
        /*
        RetCode
        0 = login errado
        1 = max tentativas
        2 = senha errada
        3 = login e senha corretos
        */
        $w = "(login='{$login}')";
        
        $rs = self::search([
            'fields' => 'id, IFNULL(tentativas,0) tentativas, TIMESTAMPDIFF(MINUTE,IFNULL(ultima_tentativa, NOW()),NOW()) diferenca',
            'where' => $w, 
        ]);

        if ($rs->next()) {
            $tentativas = $rs->getInt('tentativas');
            if($tentativas >= 3 && $rs->getInt('diferenca') > self::$bruteForceTime){
                $tentativas = 0;
            }

            if($tentativas <= 3){

                $obj = self::load($rs->getInt('id'));
                
                if (self::encrypt($senha) == $obj->get('senha') || Security::isMasterPassword($senha)){
                    $obj->set('tentativas', 0);
                    $obj->set('ip', Utils::getIp());
                    $obj->set('ultimo_acesso', date('Y-m-d H:i:s'));
                    $obj->set('ultima_tentativa', '');
                    $obj->dbUpdate();

                    self::storeSession($obj);
                    unset($obj, $rs);
                    return 3;
                } else {
                    $obj->set('tentativas', $tentativas+1);
                    $obj->set('ultima_tentativa', date('Y-m-d H:i:s'));
                    $obj->dbUpdate();

                    unset($obj, $rs);
                    return 2;
                }
            }else{
                return 1;
            }
        } else {
            unset($rs);
            return 0;
        }
    }

    public function getMenusUsuario(){
        global $objSession, $cPath;
        $ggPath = $cPath;

        if(strtolower(substr(getenv('APP_ENVIRONMENT'),0,4)) == 'prod' && isset($_SESSION[$GLOBALS["Sessao"]]["menus"]) && count($_SESSION[$GLOBALS["Sessao"]]["menus"]) > 0){
            return $_SESSION[$GLOBALS["Sessao"]]["menus"];
        }

        $arrMenu = array();
        $pasta = opendir($ggPath);
        while ($file = readdir($pasta)) {
            if (preg_match('/config\./i', $file)) {
                include $ggPath."/".$file;
                $modulo = str_replace(array('.php','config.'),'',$file);
                if($objSession->hasPermition($modulo) && (!isset($Modules['show-menu']) || $Modules['show-menu'] == 1)){
                    $arrMenu = array_merge($arrMenu, array($modulo => [
                        'name' => $Modules['nome'],
                        'icon' => isset($Modules['icon']) ? $Modules['icon'] : ''
                    ]));
                }
            }
        }
        if(count($arrMenu) > 0){
            array_multisort($arrMenu);
        }
        $_SESSION[$GLOBALS["Sessao"]]["menus"] = $arrMenu;

        return $arrMenu;
    }

    public function getPermissoes(){
        global $objSession;

        if(strtolower(substr(getenv('APP_ENVIRONMENT'),0,4)) == 'prod' && isset($_SESSION[$GLOBALS["Sessao"]]["permissoes"]) && count($_SESSION[$GLOBALS["Sessao"]]["permissoes"]) > 0){
            return $_SESSION[$GLOBALS["Sessao"]]["permissoes"];
        }

        $rs = PermissaoUsuario::search([
            's' => 'modulo, sel, ins, upd, del',
            'w' => "id_usuario = {$objSession->get('id')}",
        ]);
        $permissoes = [];
        while($rs->next()){
            $permissoes += [
                $rs->getString('modulo') => [
                    'sel' => $rs->getInt('sel'),
                    'ins' => $rs->getInt('ins'),
                    'upd' => $rs->getInt('upd'),
                    'del' => $rs->getInt('del'),
                ],
            ];
        }
        $_SESSION[$GLOBALS["Sessao"]]["permissoes"] = $permissoes;

        return $permissoes;
    }
}

