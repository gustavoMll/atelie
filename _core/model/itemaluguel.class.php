<?php

class ItemAluguel extends Flex {
    protected $tableName = 'itensaluguel';
    protected $mapper = array(
        'id' => 'int',
        'id_item' => 'int',
        'id_aluguel' => 'int',
        'tipo_item' => 'int',
        'qtd' => 'int',
        'modificar' => 'int',
        'obs' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Itens do Aluguel',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu'=> true,
        'icon' => 'ti ti-plus'
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `itensaluguel`;
        CREATE TABLE `itensaluguel` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_item` int(11) NOT NULL,
            `id_aluguel` int(11) NOT NULL,
            `tipo_item` int(11) NOT NULL,
            `qtd` int(11) NOT NULL,
            `modificar` int(1) DEFAULT 0,
            `obs` TEXT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }

    public static $nm_tipos = [
        1 => 'Acessório',
        2 => 'Fantasia'
    ];
    
    public function getItem(){

        if($this->get('tipo_item' != 1 && $this->get('tipo_item') != 2 )){
            return new Fantasia();
        }

        if($this->get('tipo_item') == 1){
            if(!Acessorio::exists($this->get('id_item'))){
                return new Acessorio();
            }
            return Acessorio::load($this->get('id_item'));
        
        }else if($this->get('tipo_item') == 2){
            if(!Fantasia::exists($this->get('id_item'))){
                return new Fantasia();
            }
            return Fantasia::load($this->get('id_item'));
        }
    }
    
    public function getAcessorio(){
        if($this->get('tipo_item') != 1 || !Acessorio::exists($this->get('id_item'))){
            return new Acessorio();
        }

        return Acessorio::load($this->get('id_item'));
    }


    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
    	
        if(!isset($_POST['id_aluguel']) || $_POST['id_aluguel'] == ''){
    		$error .= '<li>O campo "Aluguel" n&atilde;o foi informado</li>';
    	}
        
        if(!isset($_POST['tipo_item']) || $_POST['tipo_item'] == ''){
    		$error .= '<li>O campo "Tipo de Item" n&atilde;o foi informado</li>';
    	}
        
        if(!isset($_POST['qtd']) || $_POST['qtd'] == ''){
    		$error .= '<li>O campo "Quantidade de Item" n&atilde;o foi informado</li>';
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
            
            $obj->set('tipo_item', (int) $_POST['tipo_item']);
           
            if((int) $_POST['tipo_item'] == 1){
                $obj->set('id_item', (int) $_POST['id_acessorio']);
            }elseif((int) $_POST['tipo_item'] == 2){
                $obj->set('id_item', (int) $_POST['id_fantasia']);
            }

			$obj->set('id_aluguel', (int) $_POST['id_aluguel']);
			$obj->set('qtd', (int) $_POST['qtd']);
			$obj->set('modificar', (int) $_POST['modificar']);
			$obj->set('obs', $_POST['obs']);
            
            $obj->save();

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
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }

        // echo $request->getInt('id_aluguel'); exit;
        $string = '<input name="id_aluguel" type="hidden" value="'.$request->getInt('id_aluguel').'"/>';

        $string .= '
        <div class="col-md-3 mb-3 required">
            <div class="form-floating">
                <select class="form-select" id="tipo_item" name="tipo_item" onchange="mudarTipo(this.value)">';
                    foreach(self::$nm_tipos as $k => $v){
                        $string .= '<option value="'.$k.'">'.$v.'</option>';
                    }
                $string .='
                </select>
                <label class="form-label">Tipo de Item</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-6 mb-3 d-none" id="div_fantasia">
            <input type="hidden" name="id_fantasia" id="id_fantasia" value="' . $obj->get('id_fantasia') . '"/>
            <div class="form-floating">
                <input id="nome_fantasia" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="fantasias" data-name="descricao" data-field="id_fantasia" value=""/>
                <label for="id_fantasia">Fantasia</label>
            </div>
        </div>
        ';
        
        $string .= '
        <div class="col-md-6 mb-3" id="div_acessorio">
            <input type="hidden" name="id_acessorio" id="id_acessorio" value="' . $obj->get('id_acessorio') . '"/>
            <div class="form-floating">
                <div class="form-floating">
                    <input id="nomeAcessorio" name="desc_acessorio" type="text" placeholder="seu dado aqui" class="form-control autocomplete" autocomplete="off" data-table="acessorios" data-name="descricao" data-field="id_acessorio" value=""/>
                    <label for="id_acessorio">Acessorio</label>
                </div>
            </div>
        </div>
        ';

        $string .='
        <div class="col-md-3 mb-3 required">
            <div class="form-floating">
                <input type="number" id="qtd" name="qtd" min="1" value="1" class="form-control">
                <label for="qtd">Quantidade</label>
            </div>
        </div>
        ';

        $string .='
        <div class="col-md-4 mb-3"> 
            <div class="form-floating">
                <select class="form-select" name="modificar" id="modificar" onchange="mostrarTxt(this.value)">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
                <label>Modificar?</label>
            </div>
        </div>
        ';

        $string .= '
        <div class="col-sm-12 mb-3" id="div_txt" style="display: none">
            <div class="form-group">
                <label for="">Observa&ccedil;&atilde;o</label>
                <textarea class="form-control ckeditor" name="obs" id="obs'.$obj->getTableName().'">'.$obj->get('obs').'</textarea>
            </div>
        </div>';

        $string .='
        <script>
            function mudarTipo(tipo){
                if(tipo == 1){
                    $(`#div_fantasia`).addClass(`d-none`);
                    $(`#div_acessorio`).removeClass(`d-none`);
                }else if(tipo == 2){
                    $(`#div_acessorio`).addClass(`d-none`);
                    $(`#div_fantasia`).removeClass(`d-none`);
                }
            }

            function mostrarTxt(op){
                if(op == 1){
                    $(`#div_txt`).fadeIn(`slow`);
                }else{
                    $(`#div_txt`).fadeOut(`slow`);
                }
            }
        </script>
        ';
    	

        return $string;
    }

  public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-3">Tipo</th>
                    <th class="col-sm-4">Descri&ccedil;&atilde;o</th>
                    <th class="col-sm-2 text-center">Quantidade</th>
                    <th class="col-sm-3">Pre&ccedil;o (R$)</th>
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
        <td>'.self::$nm_tipos[$obj->get('tipo_item')].'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getItem()->get('descricao')).'</td>
        <td class="text-center">'.$obj->get('qtd').'</td>
        <td>'.Utils::parseMoney($obj->getItem()->get('preco')).'</td>
        '.GG::getResponsiveList([
            'Tipo' => self::$nm_tipos[$obj->get('tipo_item')],
            'Descri&ccedil;&atilde;o' => $obj->getItem()->get('descricao'),
            'Quantidade' => $obj->get('qtd'),
            'Pre&ccedil;o' => Utils::parseMoney($obj->getItem()->get('preco')),
        ], $obj).'
        ';
    }


    public static function filter($request) {
        $paramAdd = '1=1';
        
        if($request->query('id_aluguel') != ''){
            $paramAdd .= " AND `id_aluguel` = {$request->query('id_aluguel')}";
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