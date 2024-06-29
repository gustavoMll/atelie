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
        'show-menu'=> false,
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
        if($this->get('tipo_item') != 1 && $this->get('tipo_item') != 2 ){
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

    public function getQtdItensAlugados($id_item){
        $rs = ItemAluguel::search([
            's' => 'SUM(qtd) AS qtd',
            'w' => "tipo_item=1 AND id_item={$id_item} AND id_aluguel NOT IN (SELECT id FROM alugueis WHERE dt_entrega <> null)",
        ]);

        $rs->next();
        return (int) $rs->getInt('qtd');
    }
    
    public function getAcessorio(){
        if($this->get('tipo_item') != 1 || !Acessorio::exists($this->get('id_item'))){
            return new Acessorio();
        }

        return Acessorio::load($this->get('id_item'));
    }


    public static function validate($id_acessorio) {
    	global $request;
        $error = '';
        $id = $request->getInt('id') > 0;
        $paramAdd = 'AND id NOT IN('.$id.')';
        if(!isset($_POST['id_aluguel']) || $_POST['id_aluguel'] == ''){
    		$error .= '<li>O campo "Aluguel" n&atilde;o foi informado</li>';
    	}
        
        if(!isset($_POST['tipo_item']) || $_POST['tipo_item'] == '' || !in_array($_POST['tipo_item'], [1,2])){
    		$error .= '<li>O campo "Tipo de Item" n&atilde;o foi informado</li>';
    	}
        
        if(isset($_POST['tipo_item']) && $_POST['tipo_item'] == 1){
            if(!isset($_POST['id_acessorio']) || $_POST['id_acessorio'] == ''){
                $error .= '<li>O campo "Acess&oacute;rio" n&atilde;o foi informado</li>';
            }elseif(!Acessorio::exists("id={$_POST['id_acessorio']}")){
                $error .= '<li>O acess&oacute;rio informado n&atilde;o existe</li>';
            }else{
                if(!isset($_POST['qtd']) || $_POST['qtd'] == ''){
                    $error .= '<li>O campo "Quantidade de Item" n&atilde;o foi informado</li>';
                }elseif((int) $_POST['qtd'] <= 0){
                    $error .= '<li>A quantidade informada &eacute; inv&acute;lida</li>';
                }elseif(self::exists("id={$id_acessorio}")){
                    $obj = Acessorio::load($id_acessorio);
                    $qtd_ori = 0;
                    if($id > 0) $qtd_ori = ItemAluguel::load($id)->get('qtd');
                    if(($id == 0 && (int) $_POST['qtd'] > $obj->get('qtd_disp')) || ($id > 0 && $obj->get('qtd_disp') < (int)$_POST['qtd'] - ($qtd_ori))){
                        $error .= '<li>A quantidade de itens n&atilde;o pode ser maior que a quantidade disponível</li>';
                    }
                }
            }
        }elseif(isset($_POST['tipo_item']) && $_POST['tipo_item'] == 2){
            if(!isset($_POST['id_fantasia']) || $_POST['id_fantasia'] == ''){
                $error .= '<li>O campo "Fantasia" n&atilde;o foi informado</li>';
            }elseif(!Fantasia::exists("id={$_POST['id_fantasia']}")){
                $error .= '<li>A fantasia informada n&atilde;o existe</li>';
            }
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
        $id_acessorio = isset($_POST['id_acessorio']) ? (int) $_POST['id_acessorio'] : 0;

        if(self::validate($id_acessorio)){
        	$id = $request->getInt('id');
            $obj = new $classe(array($id));
            $objAcessorio = new Acessorio();
            $qtd_original = 0;
            if ($id > 0) {
                $obj = self::load($id);
                $qtd_original = $obj->get('qtd');
            }
            
            $obj->set('tipo_item', (int) $_POST['tipo_item']);
           
            if((int) $_POST['tipo_item'] == 1){
                $obj->set('id_item', (int) $_POST['id_acessorio']);
            }elseif((int) $_POST['tipo_item'] == 2){
                $obj->set('id_item', (int) $_POST['id_fantasia']);
            }

			$obj->set('id_aluguel', (int) $_POST['id_aluguel']);
			$obj->set('qtd', (int) $_POST['tipo_item'] == 1 ? (int) $_POST['qtd'] : 1);
			$obj->set('modificar', (int) $_POST['modificar']);
			$obj->set('obs', $_POST['obs']);

            if($obj->get('tipo_item') == 1){
                $objAcessorio = Acessorio::load($obj->get('id_item'));
                if($id > 0 && (int) $_POST['qtd'] <= $objAcessorio->get('qtd_disp') + (int) $_POST['qtd']){
                    $nova_qtd = (int) $_POST['qtd'] - $qtd_original;
                    $objAcessorio->set('qtd_disp', $objAcessorio->get('qtd_disp') - $nova_qtd);
                }elseif($id == 0){
                    $objAcessorio->set('qtd_disp', ((int)$objAcessorio->get('qtd_disp') - (int) $_POST['qtd']));
                }
                $objAcessorio->save();
            }
            
            $obj->save();

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

        $string = '<input name="id_aluguel" type="hidden" value="'.($obj->get('id_aluguel') != '' ? $obj->get('id_aluguel') : $request->getInt('id_aluguel')).'"/>';

        $string .= '
        <div class="col-md-3 mb-3 required">
            <div class="form-floating">
                <select class="form-select" id="tipo_item" name="tipo_item" onchange="mudarTipo(this.value)">';
                    foreach(self::$nm_tipos as $k => $v){
                        $string .= '<option value="'.$k.'" '.($obj->get('tipo_item') == $k ? 'selected' : '').'>'.$v.'</option>';
                    }
                $string .='
                </select>
                <label class="form-label">Tipo de Item</label>
            </div>
        </div>';

        $string .= '
        <div class="col-md-9 mb-3 '.($obj->get('tipo_item') == 1 || $obj->get('tipo_item') == '' ? 'd-none' : '').'" id="div_fantasia" >
            <input type="hidden" name="id_fantasia" id="id_fantasia" value="' . $obj->get('id_item') . '"/>
            <div class="form-floating">
                <input id="nome_fantasia" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="fantasias" data-name="descricao-preco" data-field="id_fantasia" value="'.$obj->getItem()->get('descricao') .'"/>
                <label for="id_fantasia">Fantasia</label>
            </div>
        </div>
        ';
        
        $string .= '
        <div class="col-md-6 mb-3 '.($obj->get('tipo_item') == 2 ? 'd-none' : '').'" id="div_acessorio" >
            <input type="hidden" name="id_acessorio" id="id_acessorio" value="' . $obj->get('id_item') . '"/>
            <div class="form-floating">
                <div class="form-floating">
                    <input id="nomeAcessorio" name="desc_acessorio" type="text" placeholder="seu dado aqui" class="form-control autocomplete" autocomplete="off" data-table="acessorios" data-name="descricao-qtd_disp-preco" input-aux="preco-acessorio/qtd-disp" data-field="id_acessorio" value="'.$obj->getItem()->get('descricao').'"/>
                    <label for="id_acessorio">Acessorio</label>
                </div>
            </div>
        </div>
        ';

        $string .='
        <div class="col-md-3 mb-3 '.($obj->get('tipo_item') == 2 ? 'd-none' : '').'" id="div_preco-acessorio" >
            <div class="form-floating">
                <input type="number" readonly id="preco-acessorio" value="'.$obj->getItem()->get('preco').'" class="form-control">
                <label for="preco-acessorio">Pre&ccedil;o</label>
            </div>
        </div>
        ';
        
        $string .='
        <div class="col-md-3 mb-3 '.($obj->get('tipo_item') == 2 ? 'd-none' : '').'" id="div_qtd-disp" >
            <div class="form-floating">
                <input type="number" readonly id="qtd-disp" name="qtd_disp" value="'.$obj->getItem()->get('qtd_disp').'" class="form-control">
                <label for="qtd">Quantidade Disponível</label>
            </div>
        </div>
        ';

        $string .='
        <div class="col-md-4 mb-3"> 
            <div class="form-floating">
                <select class="form-select" name="modificar" id="modificar" onchange="mostrarTxt(this.value)">
                    <option value="0">Não</option>
                    <option value="1" '.($obj->get('obs') != '' ? 'selected' : '').'>Sim</option>
                </select>
                <label>Modificar?</label>
            </div>
        </div>
        ';

        $string .= '
        <div class="col-sm-12 mb-3" id="div_txt" '.($obj->get('obs') == '' ? 'style="display: none"' : '').'>
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
                    $(`#div_preco-fantasia`).addClass(`d-none`);
                    $(`#div_acessorio`).removeClass(`d-none`);
                    $(`#div_qtd`).removeClass(`d-none`);
                    $(`#div_qtd-disp`).removeClass(`d-none`);
                    $(`#div_preco-acessorio`).removeClass(`d-none`);
                }else if(tipo == 2){
                    $(`#div_acessorio`).addClass(`d-none`);
                    $(`#div_qtd`).addClass(`d-none`);
                    $(`#div_qtd-disp`).addClass(`d-none`);
                    $(`#div_preco-acessorio`).addClass(`d-none`);
                    $(`#div_fantasia`).removeClass(`d-none`);
                    $(`#div_preco-fantasia`).removeClass(`d-none`);
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getItem()->get('descricao'), false).'</td>
        <td class="text-center">'.$obj->get('qtd').'</td>
        <td>'.$obj->getItem()->get('preco').'</td>
        '.GG::getResponsiveList([
            'Tipo' => self::$nm_tipos[$obj->get('tipo_item')],
            'Descri&ccedil;&atilde;o' => $obj->getItem()->get('descricao'),
            'Quantidade' => $obj->get('qtd'),
            'Pre&ccedil;o' => $obj->getItem()->get('preco'),
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
