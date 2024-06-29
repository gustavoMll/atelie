<?php

class Fantasia extends Flex {
    protected $tableName = 'fantasias';
    protected $mapper = array(
        'id' => 'int',
		'descricao' => 'string',
		'id_tipo' => 'int',
		'preco' => 'float',
		'tamanho' => 'string',
		'img' => 'string',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Fantasias',
        'class' => __CLASS__,
        'ordenacao' => 'descricao ASC',
        'envia-arquivo' => true,
        'show-menu'=> true,
        'icon' => 'ti ti-hanger'
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `fantasias`;
        CREATE TABLE `fantasias` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `descricao` VARCHAR(100) NOT NULL,
            `id_tipo` INT(2) NOT NULL,
            `preco` FLOAT(11, 2) NOT NULL,
            `tamanho` VARCHAR(50) NOT NULL,
            `img` VARCHAR(255) NULL,
            `ativo` INT(1) NOT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_tipo) REFERENCES tipos(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }

    public static $tamImg = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
    );

    protected $tipo_fantasia = null;
    
    public function getTipo()
    {
        if (!$this->tipo_fantasia || $this->tipo_fantasia->get('id') != $this->get('id')) {
            if (Tipo::exists((int) $this->get('id'), 'id')) {
                $this->tipo_fantasia = Tipo::load($this->get('id'));
            } else {
                $this->tipo_fantasia = new Tipo();
                $this->tipo_fantasia->set('nome', '');
            }
        }
        return $this->tipo_fantasia;
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
        
        if(!isset($_POST['tamanho']) || $_POST['tamanho'] == ''){
    		$error .= '<li>O campo "Tamanho" n&atilde;o foi informado</li>';
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
			$obj->set('tamanho', $_POST['tamanho']);
            $imgBefore = '';
            if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
                $imgBefore = $obj->get('img');
                $obj->set('img', Image::configureName($_FILES['img']['name']));
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
                Image::saveFromUpload($imgBefore,'img', self::$tamImg, $id, $obj->getTableName());
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
        $ret = 0;

        if(!ItemAluguel::exists("tipo_item=2 AND id_item IN ({$ids})")){
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
        } 
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
        <div class="col-sm-12 mb-3 required">
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
                    <input class="form-control" name="tamanho" placeholder="" value="'.$obj->get('tamanho').'">
                    <label class="form-label">Tamanho</label>
                </div>
        </div>';

        $string .= '
        <div class="form-group col-sm-12 mb-3">
            <label for="input_img_'.$obj->getTableName().'">Imagem<small class="rule">('.implode(', ',Image::$typesAllowed).')</small></label>
            <input name="img" id="input_img_'.$obj->getTableName().'" onchange="showPreview(this, ``, `'.$obj->getTableName().'`);" type="file" class="form-control" value=""/>
        </div>'.GG::getPreviewImage($obj);

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
        $img = $obj->getImage('t'); 
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
