<?php

class Pedido extends Flex {
    protected $tableName = 'pedidos';
    protected $mapper = array(
        'id' => 'int',
        'id_cliente' => 'int',
		'total' => 'float',
		'forma_pag' => 'int',
        'data' => 'string',
        'valor_entrada' => 'float',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);
    
    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Pedidos',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => true,
        'show-menu'=> true,
        'icon' => 'ti ti-plus'
    );
    
    public static function createTable(){
    return '
    DROP TABLE IF EXISTS `pedidos`;
    CREATE TABLE `pedidos` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_cliente` int(11) NOT NULL,
        `total` FLOAT(11,2) NOT NULL,
        `forma_pag` INT(1) NOT NULL,
        `data` DATE NOT NULL,
        `valor_entrada` FLOAT(11,2) NOT NULL,
        `usr_cad` varchar(20) NOT NULL,
        `dt_cad` datetime NOT NULL,
        `usr_ualt` varchar(20) NOT NULL,
        `dt_ualt` datetime NOT NULL,
        PRIMARY KEY(`id`),
        FOREIGN KEY (id_cliente) REFERENCES cliente(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
        
    public static $formas_pag = [
        1 => 'Cartão Crédito',
        2 => 'Cartão Débito',
        3 => 'Pix',
        4 => 'Dinheiro',
    ];
    
    public static $entrada_minima = 50.00;
    
    protected $cliente = null;
    public function getCliente(){
        if (!$this->cliente || $this->cliente->get('id') != $this->get('id_cliente')) {
            if (Cliente::exists((int) $this->get('id_cliente'), 'id')) {
                $this->cliente = Cliente::load($this->get('id_cliente'));
            } else {
                $this->cliente = new Cliente();
            }
        }
        return $this->cliente;
    }

    public function getValorPedido($tempId = 0){
        $valor = 0;
        $id = $tempId > 0 ? $tempId : $this->get('id'); 

        $rs = Aluguel::search([
            's' => 'id',
            'w' => 'id_pedido='.$id,
        ]);

        while($rs->next()){
            $valor += Aluguel::load($rs->getInt('id'))->getValorAluguel();    
        }
        return $valor;
    }


    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

    	if(!isset($_POST['id_cliente']) || $_POST['id_cliente'] == ''){
    		$error .= '<li>O campo "Cliente" n&atilde;o foi informado</li>';
    	}
    	
        if(isset($_POST['id_cliente']) && !Cliente::exists("id={$_POST['id_cliente']}")){
            $error .= '<li>O cliente informado n&atilde;o existe</li>';
        }
        if(!isset($_POST['total']) || $_POST['total'] == '' || (float) $_POST['total'] < 0){
    		$error .= '<li>O campo "Total" n&atilde;o foi informado</li>';
    	}
       
        if(!isset($_POST['data']) || $_POST['data'] == ''){
    		$error .= '<li>O campo "Data" n&atilde;o foi informado</li>';
    	}

        if(isset($_POST['data']) && !Utils::dateValid($_POST['data'])){
            $error .= '<li>O campo "Data" &eacute foi inv&aacute;lido</li>';
        }

        if(!isset($_POST['valor_entrada']) || $_POST['valor_entrada'] == ''){
            $error .= '<li>O campo "Valor de Entrada" n&atilde;o foi informado</li>';
        }

        if(isset($_POST['valor_entrada']) && ((float) str_replace(',','.',str_replace('.','',$_POST["valor_entrada"])) < (float) self::$entrada_minima)){
            $error .= '<li>O campo "Valor de Entrada" &eacute; inv&aacute;lido/</li>';
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
            
			$obj->set('id_cliente', (int) $_POST['id_cliente']);
			$obj->set('total', Utils::parseMoney($id > 0 ? $obj->getValorPedido() : $obj->getValorPedido($_POST['tempId'])));
			$obj->set('data', Utils::dateFormat($_POST['data'], 'Y-m-d'));
			$obj->set('forma_pag', (int) $_POST['forma_pag']);
			$obj->set('valor_entrada', (float) str_replace(',','.',str_replace('.','',$_POST["valor_entrada"])));
            $obj->save();
            
            DocumentoPedido::delete($obj->get('id'));
            if(isset($_POST['documentos'])){
                foreach($_POST['documentos'] as $k => $v){
                    $objDP = new DocumentoPedido();
                    $objDP->set('id_documento', $v);
                    $objDP->set('id_pedido', $obj->get('id'));
                    $objDP->save();
                }
            }

            if(isset($_POST['tempId'])){
                $rs = Aluguel::search([
                    's' => 'id',
                    'w' => 'id_pedido = '.(isset($_POST['tempId']) ? (int)$_POST['tempId'] : $obj->get('id'))
                ]);

                while($rs->next()){
                    $objAluguel = Aluguel::load($rs->getInt('id'));
                    $objAluguel->set('id_pedido', $obj->get('id'));
                    $objAluguel->save();
                }
            }

            $rs = Aluguel::search([
                's' => 'id',
                'w' => "id_pedido NOT IN (SELECT id FROM pedidos)"
            ]);

            while($rs->next()){
                $objAluguel->delete($rs->getInt('id'));
            }

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

        $rs = ItemAluguel::search([
            's' => 'id_item, qtd',
            'w' => "tipo_item = 2 AND id_aluguel IN (SELECT id FROM alugueis WHERE id_pedido IN ({$ids}))",
        ]);

        while($rs->next()){
            $objA = Acessorio::load($rs->getInt('id_item'));
            $objA->set('qtd_disp', (int) $objA->get('qtd_disp') + (int) $rs->getInt('qtd'));
            $objA->save();
        }

        Flex::dbDelete(new ItemAluguel(), "id_aluguel IN (SELECT id FROM alugueis WHERE id_pedido IN ({$ids}))");
        Flex::dbDelete(new Aluguel(), "id_pedido IN ({$ids})");
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
            <input type="hidden" name="id_cliente" id="id_cliente" value="' . $obj->get('id_cliente') . '"/>
            <div class="input-group">
                <div class="form-floating">
                    <input id="nome_fantasia" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="pessoas" data-name="nome-cpf" data-div="()" data-field="id_cliente" value="'.$obj->getCliente()->getPessoa()->get('nome').'"/>
                <label for="id_cliente">Cliente</label>
                </div>
                <a type="button" class="btn btn-secondary btn-sm px-3" onclick="javascript:modalForm(`clientes`,0);">
                    <i class="ti ti-plus"></i>
                </a>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-4 mb-3 required">
            <div class="form-floating">
                <input type="text" name="data" id="data" value="'.($obj->get('data') != '' ? Utils::dateFormat($obj->get('data'), 'd/m/Y') : date('d-m-Y') ).'" class="form-control date">
                <label>Data</label>
            </div>
        </div>
        ';

        $string .='
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between"> 
                        <h5 class="card-title">Alugueis do Pedido</h5>
                        <a type="button" class="btn btn-secondary btn-sm px-3" onclick="javascript:modalForm(`alugueis`,0, `/id_pedido/'.$codigo.'`, loadAlugueis);">
                            <i class="ti ti-plus"></i>Adicionar Aluguel
                        </a>
                    </div>

                    <div>
                        <script> 
                            function loadAlugueis(resp){ 
                                tableList(`alugueis`, `id_pedido='.$codigo.'&offset=10`, `txt_alugueis`, false);
                            } 
                            loadAlugueis();
                        </script>
                        <div class="form-group col-sm-12" id="txt_alugueis">'.GG::moduleLoadData('loadAlugueis();').'</div>    
                    </div>
                </div>
            </div>
        </div>';

        $string .='
        <div class="col-sm-6">
            <div class="form-floating">
                <select class="form-select" id="forma_pag" name="forma_pag">';
                foreach(self::$formas_pag as $k => $v){
                    $string .= '<option value="'.$k.'" '.($k == $obj->get('forma_pag') ? 'selected' : '').'>'.$v.'</option>';
                }
                $string .='
                </select>
                <label>Forma de Pagamento</label>
            </div>
        </div>
        ';

        $string .='
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input type="text" id="total" value="'.Utils::parseMoney((float) $obj->get('total')).'" name="total" readonly placeholder="Seu dado aqui" class="form-control money">
                <label>Tota do Pedido</label>
            </div>
        </div>
        ';

        $string .= '
            <div class="col-sm-6 mb-3 required">
                <div class="form-floating">
                    <input class="form-control money" name="valor_entrada" min="50" placeholder="" value="'.($obj->get('valor_entrada') != '' ? Utils::parseMoney((float) $obj->get('valor_entrada')) : Utils::parseMoney(self::$entrada_minima)).'">
                    <label class="form-label">Valor de Entrada</label>
                </div>
        </div>';
        
    	
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <select class="form-floating multiselsearch" multiple id="documentos" name="documentos[]">';
                $rs = Documento::getDocs();
                $rs2 = DocumentoPedido::getDocsPedido($obj->get('id'));
                $docs_selecionados = array();
                while($rs2->next()){
                    $docs_selecionados[] = $rs2->getInt('id_documento');
                }
                while($rs->next()){
                    $string .= '<option value="'.$rs->getInt('id').'" '.(in_array($rs->getInt('id'), $docs_selecionados) ? 'selected' : '').'>'.$rs->getString('descricao').'</option>';
                }
                $string .='
                </select>
                <label>Documentos</label>
            </div>
        </div>
        ';

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-6">Cliente</th>
                    <th class="col-sm-3">Data</th>
                    <th class="col-sm-3">Total</th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getCliente()->getPessoa()->get('nome')).'</td>
        <td>'.Utils::dateFormat($obj->get('data'), 'd/m/Y').'</td>
        <td>'.Utils::parseMoney($obj->getValorPedido()).'</td>
         '.GG::getResponsiveList([
            'Cliente' => $obj->getCliente()->getPessoa()->get('nome'),
            'Data' => Utils::dateFormat($obj->get('data'), 'd/m/Y'),
            'Pre&ccedil;o' => Utils::parseMoney($obj->getValorPedido()),
        ], $obj).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
       
        if($request->query('cliente') != ''){
            $paramAdd .= " AND id_cliente IN (SELECT id FROM clientes WHERE id_pessoa IN (SELECT id FROM pessoas WHERE nome LIKE '%{$request->query('cliente')}%'))";
        }
        
        if($request->query('forma_pag') != ''){
            $paramAdd .= " AND `forma_pag` = {$request->query('forma_pag')}";
        }
        
        if(Utils::dateValid($request->query('dt_prazo'))){
            $paramAdd .= " AND id IN (SELECT id_pedido FROM alugueis WHERE DATE(`dt_prazo`) <= '".Utils::dateFormat($request->query('dt_prazo'),'Y-m-d')."')";
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
                <input name="cliente" id="filterDescricao" type="text" class="form-control" value="'.$request->query('cliente').'" placeholder="seu dado aqui" />
                <label for="filterDescricao" class="form-label">Cliente</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input name="dt_prazo" id="filterPrazo" type="text" class="form-control date" value="'.$request->query('dt_prazo').'" placeholder="seu dado aqui" />
                <label for="filterPrazo">Prazo de Devolu&ccedil;&atilde;o</label>
            </div>
        </div>';

        $string .='
        <div class="col-sm-4">
            <div class="form-floating">
                <select class="form-select" id="filterForma" name="forma_pag">
                <option value="">Selecione</option>';
                foreach(self::$formas_pag as $k => $v){
                    $string .= '<option value="'.$k.'" '.($k == $request->get('forma_pag') ? 'selected' : '').'>'.$v.'</option>';
                }
                $string .='
                </select>
                <label>Forma de Pagamento</label>
            </div>
        </div>
        ';

      
        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input name="inicio" id="filterInicio" type="text" class="form-control date" value="'.$request->query('inicio').'" placeholder="seu dado aqui" />
                <label for="filterInicio">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input name="fim" id="filterFim" type="text" class="form-control date" value="'.$request->query('fim').'" placeholder="seu dado aqui" />
                <label for="filteFim" class="form-label">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-4 mb-3">
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
