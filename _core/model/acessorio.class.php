<?php

class Acessorio extends Flex {
    protected $tableName = 'acessorios';
    protected $mapper = array(
        'id' => 'int',
		'descricao' => 'string',
		'id_tipo' => 'int',
		'preco' => 'float',
		'qtd_total' => 'int',
		'qtd_disp' => 'int',
		'foto' => 'string',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Acessórios',
        'class' => __CLASS__,
        'ordenacao' => 'descricao ASC',
        'envia-arquivo' => true,
        'show-menu'=> true,
        'icon' => 'ti ti-hanger-2'
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `acessorios`;
        CREATE TABLE `acessorios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `descricao` VARCHAR(100) NOT NULL,
            `id_tipo` INT(2) NOT NULL,
            `preco` FLOAT(11, 2) NOT NULL,
            `qtd_total` FLOAT(11, 2) NOT NULL,
            `qtd_disp` FLOAT(11, 2) NOT NULL,
            `foto` VARCHAR(255) NULL,
            `ativo` INT(1) NOT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_tipo) REFERENCES tipos(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }

    public static $tamFoto = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
    );

    protected $tipo_acessorio = null;
    
    public function getTipo()
    {
        if (!$this->tipo_acessorio || $this->tipo_acessorio->get('id') != $this->get('id')) {
            if (Tipo::exists((int) $this->get('id'), 'id')) {
                $this->tipo_acessorio = Tipo::load($this->get('id'));
            } else {
                $this->tipo_acessorio = new Tipo();
                $this->tipo_acessorio->set('nome', '');
            }
        }
        return $this->tipo_acessorio;
    }


    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

    	if(!isset($_POST['descricao']) || $_POST['descricao'] == ''){
    		$error .= '<li>O campo "Descrição" n&atilde;o foi informado</li>';
    	}
    	
        if(!isset($_POST['tipo']) || $_POST['tipo'] == '' || !Tipo::exists((int) $_POST['tipo'])){
    		$error .= '<li>O campo "Tipo" n&atilde;o foi informado</li>';
    	}
       
        if(!isset($_POST['preco']) || $_POST['preco'] == ''){
    		$error .= '<li>O campo "Preço" n&atilde;o foi informado</li>';
    	}
        
        if(!isset($_POST['qtd_total']) || $_POST['qtd_total'] == ''){
    		$error .= '<li>O campo "Quantidade Total" n&atilde;o foi informado</li>';
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

            if ($id == 0) {
                $obj->set('ativo', 1);
            }else{
                $obj = self::load($id);
            }
            
			$obj->set('descricao', $_POST['descricao']);
			$obj->set('id_tipo', (int) $_POST['tipo']);
			$obj->set('preco', (float) $_POST['preco']);
			$obj->set('qtd_total', (int) $_POST['qtd_total']);
            $obj->set('qtd_disp',(int) $_POST['qtd_total']);
            
            $imgBefore = '';
            if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
                $imgBefore = $obj->get('foto');
                $obj->set('foto', Image::configureName($_FILES['img']['name']));
            }
           
            $obj->save();

            $id = $obj->get('id');
            if(isset($_POST["tempId"]) && $_POST["tempId"]+0 >0 ){
                $objUp = new Foto();
                $sql = "UPDATE {$objUp->getTableName()} SET id_tipo = {$id} WHERE tipo = '{$obj->getTableName()}' AND id_tipo = ".($_POST['tempId']+0);
                $conn = new Connection();
                $conn->prepareStatement($sql)->executeQuery();
                if(is_dir($defaultPath."uploads/".$obj->getTableName()."/".$_POST["tempId"]."/")){
                    rename($defaultPath."uploads/".$obj->getTableName()."/".$_POST["tempId"]."/",  $defaultPath."uploads/".$obj->getTableName()."/".$id."/");
                }
            
            }
            if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
                Image::saveFromUpload($imgBefore,'img', self::$tamFoto, $id, $obj->getTableName());
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

        foreach ($arrIds as $id) { 
            self::deleteImage($id, 'img');
            Foto::deleteByTipo($id, $obj->getTableName());
            
            $caminho = $defaultPath."uploads/".$obj->getTableName()."/{$id}/";
            if(is_dir($caminho)){
                $ponteiro  = opendir($caminho);
                while ($nome_itens = readdir($ponteiro)) {
                    if($nome_itens != "." && $nome_itens != ".."){
                        @unlink($caminho.$nome_itens);
                    }
                }
               @rmdir($caminho);
            }
            
        } 
        
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
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
    	$string .= '
        <div class="col-sm-8 mb-3 required">
            <div class="form-floating">
                <input class="form-control" name="descricao" placeholder="" value="'.$obj->get('descricao').'">
                <label class="form-label">Descrição</label>
            </div>
        </div>';
    	
        $string .= '
        <div class="col-sm-4 mb-3 required">
            <div class="form-floating">
                <select class="form-select" id="tipo" name="tipo">
                    <option value="">Selecione</option>';
                    $tipos = Tipo::getTipos();
                    while($tipos->next()){
                        $string .= '<option value="'.$tipos->getInt('id').'" '.($tipos->getInt('id') == $obj->get('id_tipo') ? 'selected' : '').'>'.$tipos->getString('nome').'</option>';
                    }
            $string .='
                </select>
                <label class="form-label">Tipo</label>
            </div>
        </div>';

        $string .= '
            <div class="col-sm-4 mb-3 required">
                <div class="form-floating">
                    <input class="form-control money" name="preco" placeholder="" value="'.$obj->get('preco').'">
                    <label class="form-label">Preço</label>
                </div>
        </div>';
       
        $string .= '
            <div class="col-sm-4 mb-3 required">
                <div class="form-floating">
                    <input class="form-control" name="qtd_total" placeholder="" value="'.$obj->get('qtd_total').'">
                    <label class="form-label">Quantidade '.($obj->get('id') > 0 ? 'Total' : '').'</label>
                </div>
        </div>';
        
        if($obj->get('id') > 0){
            $string .= '
                <div class="col-sm-4 mb-3">
                    <div class="form-floating">
                        <input class="form-control" readonly name="qtd_disp" placeholder="" value="'.$obj->get('qtd_disp').'">
                        <label class="form-label">Quantidade Disponível</label>
                    </div>
            </div>';
        }

        $string .= '
        <div class="form-group col-sm-12 mb-3">
            <label for="input_img_'.$obj->getTableName().'">Imagem<small class="rule">('.implode(', ',Image::$typesAllowed).')</small></label>
            <input name="img" id="input_img_'.$obj->getTableName().'" onchange="showPreview(this, `foto`, `'.$obj->getTableName().'`);" type="file" class="form-control" value=""/>
        </div>'.GG::getPreviewImage($obj, 'foto');

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-3">Foto</th>
                    <th class="col-sm-5">Descri&ccedil;&atilde;o</th>
                    <th class="col-sm-2">Tipo</th>
                    <th class="col-sm-2">Pre&ccedil;o</th>
                    <th width="10"></th>
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
        $img = $obj->getImage('t', $obj->get('id'), $obj->getTableName(), 'foto'); 
        $ret = '<i class="ti ti-photo"></i>';        
        
        if($img != ''){
            $ret = '<img src="'.($img!=''?$img:__BASEPATH__.'/img/no-pic.jpg').'" class="imgPreviewList"/>';
        }
        
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="text-center">
            <span class="ratio ratio-1x1">
              '.$ret.'  
            </span>
        </td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('descricao')).'</td>
        '.GG::getResponsiveList([
            'Descri&ccedil;&atilde;o' => $obj->get('descricao'),
            'Tipo' => $obj->getTipo()->get('descricao'),
            'Pre&ccedil;o' => $obj->get('preco'),
        ], $obj).'
        <td>'.$obj->getTipo()->get('descricao').'</td>
        <td>'.$obj->get('preco').'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
       
        foreach(['descricao', 'tamanho'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }
        
        if($request->query('tipo') != ''){
            $paramAdd .= " AND `tipo` = {$request->query('tipo')}";
        }
        
        if($request->query('preco_min') != ''){
            $paramAdd .= " AND `preco` >= {$request->query('preco_min')} ";
        }
      
        if($request->query('preco_max') != ''){
            $paramAdd .= " AND `preco` <= {$request->query('preco_max')} ";
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
        <div class="col-sm-6 col-lg-5 mb-3">
            <div class="form-floating">
                <input name="descricao" id="filterDescricao" type="text" class="form-control" value="'.$request->query('descricao').'" placeholder="seu dado aqui" />
                <label for="filterDescricao" class="form-label">Descri&ccedil;&atilde;o</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 col-lg-2 mb-3">
            <div class="form-floating">
                <select class="form-select" name="tipo" id="tipo">
                <option value="">Selecione</option>';
                $tipos = Tipo::getTipos();
                while($tipos->next()){
                    $string .= '<option value="'.$tipos->getInt('id').'" '.($tipos->getInt('id') == $request->query('tipo') ? 'selected' : '').'>'.$tipos->getString('nome').'</option>';
                }
                $string.='
                </select>
                <label for="tipo" class="form-label">Tipo</label>
            </div>
        </div>
        ';

        $string .= '
        <div class="col-sm-6 col-lg-5 mb-3">
            <div class="form-floating">
                <input name="tamanho" id="filterTamanho" type="text" class="form-control" value="'.$request->query('tamanho').'" placeholder="seu dado aqui" />
                <label for="filterTamanho" class="form-label">Tamanho</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <input name="preco_min" id="filterPrecoMin" type="text" class="form-control" value="'.$request->query('preco_min').'" placeholder="seu dado aqui" />
                <label for="filterPrecoMin" class="form-label">Pre&ccedil;o min</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="form-floating">
                <input name="preco_max" id="filterPrecoMax" type="text" class="form-control" value="'.$request->query('preco_max').'" placeholder="seu dado aqui" />
                <label for="filterPrecoMax" class="form-label">Pre&ccedil;o max</label>
            </div>
        </div>';
      
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
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <select class="form-select" name="order" id="order">';
                foreach([
                    'descricao' => 'A-Z',
                    'descricao desc' => 'Z-A',
                    'preco' => 'Menor pre&ccedil;o',
                    'preco desc' => 'Maior pre&ccedil;o',
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