<?php

class Aluguel extends Flex {
    protected $tableName = 'alugueis';
    protected $mapper = array(
        'id' => 'int',
        'id_pedido' => 'int',
        'dt_uso' => 'string',
        'dt_prazo' => 'string',
        'dt_entrega' => 'string',
        'local_uso' => 'string',
        'valor_aluguel' => 'float',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Alugueis',
        'class' => __CLASS__,
        'ordenacao' => 'dt_entrega ASC',
        'envia-arquivo' => false,
        'show-menu'=> false,
        'icon' => 'ti ti-plus'
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `alugueis`;
        CREATE TABLE `alugueis` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_pedido` int(11) NOT NULL,
            `dt_uso` DATE NOT NULL,
            `dt_prazo` DATE NOT NULL,
            `dt_entrega` DATE NOT NULL,
            `local_uso` VARCHAR(255) NOT NULL,
            `valor_aluguel` FLOAT(11,2) NOT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_pedido) REFERENCES pedidos(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public function getValorAluguel($tempId = 0){
        $valor = 0;
        $id = $tempId > 0 ? $tempId : $this->get('id'); 
        
       
        $rs = ItemAluguel::search([
            's' => "SUM((SELECT `acessorios`.preco FROM `acessorios` WHERE `acessorios`.id = `itensaluguel`.id_item) * `itensaluguel`.qtd) AS valor_total",
            'w' => "`itensaluguel`.tipo_item=1 and id_aluguel={$id}",
        ]);

        $rs->next();

        $valor += $rs->getNumber('valor_total');

        $rs = ItemAluguel::search([
            's' => "SUM((SELECT `fantasias`.preco FROM `fantasias` WHERE `fantasias`.id = `itensaluguel`.id_item) * `itensaluguel`.qtd) AS valor_total",
            'w' => "`itensaluguel`.tipo_item=2 and id_aluguel={$id}",
        ]);

        $rs->next();

        $valor += $rs->getNumber('valor_total');

        return $valor;
    }

    public function getItensAluguel(){
        $string = '';
        $obj = null;
        $rs = ItemAluguel::search([
            's' => 'id',
            'w' => 'id_aluguel='.$this->get('id'),
            'o' => 'tipo_item',
        ]);

        while($rs->next()){
            $obj = ItemAluguel::load($rs->getInt('id'));
            $nome = $obj->get('tipo_item') == 1 ? $obj->getAcessorio()->get('descricao') : $obj->getFantasia()->get('descricao');

            $preco = $obj->get('tipo_item') == 1 ? $obj->getAcessorio()->get('preco') : $obj->getFantasia()->get('preco');
            $string .= '
            <div class="col-sm-4 col-lg-4 mb-2">
                <p>&nbsp;&bull; '. ($obj->get('tipo_item') == 1 ? $obj->get('qtd').'x ' : '') . $nome .' - (R$) '. Utils::parseMoney($preco) .'</p>
            </div>';
        }

        return $string;
    }

    protected $pedido = null;
    public function getPedido(){
        if (!$this->pedido || $this->pedido->get('id') != $this->get('id_pedido')) {
            if (Pedido::exists((int) $this->get('id_pedido'), 'id')) {
                $this->pedido = Pedido::load($this->get('id_pedido'));
            } else {
                $this->pedido = new Pedido();
            }
        }
        return $this->pedido;
    }

    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
    	
        if(!isset($_POST['dt_prazo']) || $_POST['dt_prazo'] == '' || !Utils::dateValid($_POST['dt_prazo'])){
    		$error .= '<li>O campo "Prazo de Devolu&ccedil;&atilde;o" n&atilde;o foi informado</li>';
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

			$obj->set('id_pedido', (int) $_POST['id_pedido']);
			$obj->set('dt_uso', Utils::dateFormat($_POST['dt_uso'], 'Y-m-d'));
			$obj->set('dt_prazo', Utils::dateFormat($_POST['dt_prazo'], 'Y-m-d'));
			$obj->set('dt_entrega', Utils::dateFormat($_POST['dt_entrega'], 'Y-m-d'));
			$obj->set('local_uso', $_POST['local_uso']);
			$obj->set('valor_aluguel', Utils::parseFloat($id > 0 ? $obj->getValorAluguel() : $obj->getValorAluguel($_POST['tempId'])));
            
            $obj->save();

            $rs = ItemAluguel::search([
                's' => 'id',
                'w' => 'id_aluguel='.(isset($_POST['tempId']) ? $_POST['tempId'] : $obj->get('id')),
            ]);

            while($rs->next()){
                $objIA = ItemAluguel::load($rs->getInt('id'));
                $objIA->set('id_aluguel', $obj->get('id'));
                $objIA->save();
            }

            $rs = ItemAluguel::search([
                's' => 'id, id_item, tipo_item',
                'w' => "id_aluguel NOT IN (SELECT id FROM alugueis)"
            ]);

            while($rs->next()){
                $objIA = ItemAluguel::load($rs->getInt('id'));
                if($rs->getInt('tipo_item') == 1){
                    $objA = Acessorio::load($rs->getInt('id_item'));
                    $objA->set('qtd_disp', $objA->get('qtd_disp') + $objIA->get('qtd'));
                }
                $objIA->dbDelete($objIA, "id={$objIA->get('id')}");
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
        
        $rs = ItemAluguel::search([
            's' => 'id, id_item, tipo_item',
            'w' => "id_aluguel NOT IN (SELECT id FROM alugueis)"
        ]);

        while($rs->next()){
            $objIA = ItemAluguel::load($rs->getInt('id'));
            if($rs->getInt('tipo_item') == 1){
                $objA = Acessorio::load($rs->getInt('id_item'));
                $objA->set('qtd_disp', $objA->get('qtd_disp') + $objIA->get('qtd'));
            }
            $objIA->dbDelete($objIA, "id={$objIA->get('id')}");
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

        $entrega = $request->getInt('entrega');
        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
        	$codigo = time();
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
       
        $string .= '<input name="id_pedido" type="hidden" value="'.($obj->get('id_pedido') != '' ? $obj->get('id_pedido') : $request->getInt('id_pedido')).'"/>';
    	$string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_uso" placeholder="" value="'.($obj->get('dt_uso') != '' ? Utils::dateFormat($obj->get('dt_uso'),'d/m/Y') : '').'">
                <label class="form-label">Data de Uso</label>
            </div>
        </div>';
    	$string .= '
        <div class="col-sm-4 mb-3 required">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_prazo" placeholder="Data" required value="'.($obj->get('dt_prazo') != '' ? Utils::dateFormat($obj->get('dt_prazo'),'d/m/Y') : '').'">
                <label class="form-label">Prazo de Devolução</label>
            </div>
        </div>';
    	
        $string .= '
        <div class="col-sm-4 mb-3">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_entrega" placeholder="" value="'.($obj->get('dt_entrega') != '' ? Utils::dateFormat($obj->get('dt_entrega'),'d/m/Y') : '').'">
                <label class="form-label">Entrega</label>
            </div>
        </div>';

        $string .='
        <div class="col-sm-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between"> 
                        <h5 class="card-title">Itens do Aluguel</h5>
                        <a type="button" class="btn btn-secondary btn-sm px-3" onclick="javascript:modalForm(`itensaluguel`,0, `/id_aluguel/'.$codigo.'`, loadItens);">
                            <i class="ti ti-plus"></i>Adicionar Itens
                        </a>
                    </div>

                    <div>
                        <script> 
                            function loadItens(resp){ 
                                tableList(`itensaluguel`, `id_aluguel='.$codigo.'&offset=10`, `txt_itens`, false);
                            } 
                            loadItens();
                        </script>
                        <div class="form-group col-sm-12" id="txt_itens">'.GG::moduleLoadData('loadItens();').'</div>    
                    </div>
                </div>
            </div>
        </div>
        ';
    	
        $string .= '
            <div class="col-sm-9 mb-3 required">
                <div class="form-floating">
                    <input class="form-control" maxlength="255" name="local_uso" placeholder="" value="'.$obj->get('local_uso').'">
                    <label class="form-label">Local de Uso</label>
                </div>
        </div>';

        $string .= '
            <div class="col-sm-3 mb-3">
                <div class="form-floating">
                    <input class="form-control money" readonly name="valor_aluguel" placeholder="" value="'.($obj->get('valor_aluguel') != '' ? Utils::parseMoney($obj->getValorAluguel()) : '').'">
                    <label class="form-label">Valor do Aluguel</label>
                </div>
        </div>';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10" class="p-3">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-4">Cliente</th>
                    <th class="col-sm-4">Prazo</th>
                    <th class="col-sm-4">Valor (R$)</th>
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
        <td class="p-3">'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td>'.$obj->getPedido()->getCliente()->getPessoa()->get('nome').'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), Utils::dateFormat($obj->get('dt_prazo'), 'd/m/Y'), false).'</td>
        <td>'.Utils::parseMoney($obj->getValorAluguel()).'</td>
        '.GG::getResponsiveList([
            'Data' => $obj->getPedido()->getCliente()->getPessoa()->get('nome'),
            'Prazo' => Utils::dateFormat($obj->get('dt_prazo'), 'd/m/Y'),
            'Valor' => Utils::parseMoney($obj->getValorAluguel()),
        ], $obj).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        
        if($request->query('id_pedido') != ''){
            $paramAdd .= " AND `id_pedido` = {$request->query('id_pedido')}";
        }

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
