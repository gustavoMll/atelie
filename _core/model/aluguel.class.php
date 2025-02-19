<?php

class Aluguel extends Flex {
    protected $tableName = 'alugueis';
    protected $mapper = array(
        'id' => 'int',
        'id_cliente' => 'int',
        'dt_coleta' => 'string',
        'dt_uso' => 'string',
        'dt_prazo' => 'string',
        'dt_entrega' => 'string',
        'local_uso' => 'string',
        'valor_aluguel' => 'float',
        'valor_entrada' => 'float',
        'valor_restante' => 'float',
        'status' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Alugueis',
        'class' => __CLASS__,
        'ordenacao' => 'dt_cad desc',
        'envia-arquivo' => false,
        'show-menu'=> true,
        'icon' => 'ti ti-plus',
        'ordem' => 1
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `alugueis`;
        CREATE TABLE `alugueis` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_cliente` int(11) NOT NULL,
            `dt_coleta` DATE NOT NULL,
            `dt_uso` DATE NOT NULL,
            `dt_prazo` DATE NOT NULL,
            `dt_entrega` DATE NOT NULL,
            `local_uso` VARCHAR(255) NOT NULL,
            `valor_aluguel` FLOAT(11,2) NOT NULL,
            `valor_entrada` FLOAT(11,2) NOT NULL,
            `valor_restante` FLOAT(11,2) NOT NULL,
            `status` INT(1) NOT NULL DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_cliente) REFERENCES clientes(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }

    public function getModificacoesPendentes(){
        $rs = ItemAluguel::search([
            's' => 'id',
            'w' => "modificar = 1 AND id_aluguel = {$this->get('id')}"  
        ]);

        return $rs->next() ? 1 : 0;
    }
    
    public function modificadoUltimoAluguel(){
        $arr = $this->getItensAluguel();
        $acessorios = $fantasias = [];
        $in = "";
        foreach($arr as $k => $objIA){
            $obj = null;
            if($objIA->get('tipo_item') == 1){
                $obj = Acessorio::load($objIA->get('id_item'));
                $acessorios[] = $obj->get('id');
            }elseif($objIA->get('tipo_item') == 2){
                $obj = Fantasia::load($objIA->get('id_item'));
                $fantasias[] = $obj->get('id');
            }
        }
        
        if(count($fantasias) > 0){
            $fantasias_str = $fantasias[0];
            if(count($fantasias) > 1){
                $fantasias_str = implode(",", $fantasias);
            }
            $in = "SELECT id FROM acessorios WHERE id IN ({$fantasias_str})";
        }
        if(count($acessorios) > 0){
            if(count($fantasias) > 0){
                $in .= " UNION ";
            }
            $acessorios_str = $acessorios[0];
            if(count($fantasias) > 1){
                $acessorios_str = implode(",", $acessorios);
            }
            $in .= "SELECT id FROM acessorios WHERE id IN ({$acessorios_str})";
        }

        $objIA = new ItemAluguel();
        $where = "
        (dt_entrega = '0000-00-00' OR dt_entrega >= DATE('{$this->get('dt_cad')}')) AND id IN (
            SELECT id_aluguel 
            FROM itensaluguel 
            WHERE modificar = 1";
        if($in != ''){
            $where .= " AND id_item IN ({$in})";
        } 
        $where .= "
        );";

        $rs = self::search([
            's' => 'id',
            'w' => $where
        ]);
        return $rs->next() ? 1 : 0;
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
        $arr = [];
        $rs = ItemAluguel::search([
            's' => 'id',
            'w' => 'id_aluguel='.$this->get('id'),
            'o' => 'tipo_item',
        ]);

        while($rs->next()){
            $obj = ItemAluguel::load($rs->getInt('id'));
            $arr[] = $obj;
        }

        return $arr;
    }

    public function getItensAluguelString(){
        $string = '';
        $arr = $this->getItensAluguel();
        foreach($arr as $k => $obj){
            $nome = $obj->get('tipo_item') == 1 ? $obj->getAcessorio()->get('descricao') : $obj->getFantasia()->get('descricao');

            $preco = $obj->get('tipo_item') == 1 ? $obj->getAcessorio()->get('preco') : $obj->getFantasia()->get('preco');
            $string .= '
                <p>&nbsp;&bull; '. ($obj->get('tipo_item') == 1 ? $obj->get('qtd').'x ' : '') . $nome .' - (R$) '. Utils::parseMoney($preco) .'</p>
            ';
        }

        return $string;
    }

    public static $arr_situacoes = [
        'atraso' => 'bg bg-danger fw-bold text-white',
        'atencao'=> 'bg bg-warning fw-bold',
    ];

    public static $status_aguardando = 1;
    public static $status_coletado = 2;
    public static $status_devolvido = 3;

    public static $arr_status = [
        1 => 'Aguardando coleta',
        2 => 'Coletado',
        3 => 'Devolvido'
    ];

    public function getStatus($data){
        $dtComparacao = strtotime($data);
        $dataAtual = strtotime(date('Y/m/d'));
        $diferencaDias = ($dtComparacao - $dataAtual) / (60 * 60 * 24);
        
        if($diferencaDias < 0){
            return $this::$arr_situacoes['atraso'];
        }elseif($diferencaDias <=7){
            return $this::$arr_situacoes['atencao'];
        }
        return "";
    }

    

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


    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
    	
        if(!isset($_POST['dt_prazo']) || $_POST['dt_prazo'] == '' || !Utils::dateValid($_POST['dt_prazo'])){
    		$error .= '<li>O campo "Prazo de Devolu&ccedil;&atilde;o" n&atilde;o foi informado</li>';
    	}
        
        if(isset($_POST['id_cliente']) && !Cliente::exists("id={$_POST['id_cliente']}")){
            $error .= '<li>O Cliente informado n&atilde;o existe</li>';
        }

        if(!isset($_POST['status']) || !isset(self::$arr_status[$_POST['status']])){
            $error .= '<li>O Status informado n&atilde;o existe</li>';
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
            $status_orig = 0;
            if ($id > 0) {
                $obj = self::load($id);
                $status_orig = $obj->get('status');
            }


			$obj->set('id_cliente', (int) $_POST['id_cliente']);
			$obj->set('dt_coleta', Utils::dateFormat($_POST['dt_coleta'], 'Y-m-d'));
			$obj->set('dt_uso', Utils::dateFormat($_POST['dt_uso'], 'Y-m-d'));
			$obj->set('dt_prazo', Utils::dateFormat($_POST['dt_prazo'], 'Y-m-d'));
			$obj->set('dt_entrega', Utils::dateFormat($_POST['dt_entrega'], 'Y-m-d'));
			$obj->set('local_uso', $_POST['local_uso']);
			$obj->set('valor_aluguel', Utils::parseFloat($id > 0 ? $obj->getValorAluguel() : $obj->getValorAluguel($_POST['tempId'])));
            $obj->set('valor_entrada', Utils::parseFloat($_POST['valor_entrada']));
            $obj->set('status', $_POST['status']);
            $obj->set('valor_restante', (float)$obj->get('valor_aluguel') - (float)$obj->get('valor_entrada'));
            $obj->set('valor_restante', (float)$obj->get('valor_aluguel') - (float)$obj->get('valor_entrada'));
            // print_r($obj); exit;
            $obj->save();

            $rs = ItemAluguel::search([
                's' => 'id',
                'w' => 'id_aluguel='.(isset($_POST['tempId']) ? $_POST['tempId'] : $obj->get('id')),
            ]);

            while($rs->next()){
                $objIA = ItemAluguel::load($rs->getInt('id'));
                $objIA->set('id_aluguel', $obj->get('id'));
                if($objIA->get('tipo_item') == 1 && $status_orig != $obj->get('status')){
                    $objAcess = Acessorio::load($objIA->get('id_item'));
                    if($obj->get('status') == self::$status_devolvido){
                        $objAcess->set('qtd_disp', $objAcess->get('qtd_disp') + $objIA->get('qtd'));
                        $objAcess->save();
                    }elseif($status_orig == self::$status_devolvido){
                        $objAcess->set('qtd_disp', $objAcess->get('qtd_disp') - $objIA->get('qtd'));
                        $objAcess->save();
                    }
                }
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

        $rs = ItemAluguel::search([
            's' => 'id',
            'w' => "id_aluguel IN($ids)"
        ]);

        while($rs->next()){
            $objIA = ItemAluguel::load($rs->getInt('id'));
            if($objIA->get('tipo_item') == 1){
                $objA = Acessorio::load($objIA->get('id_item'));
                $nova_qtd = $objA->get('qtd_disp') + $objIA->get('qtd');
                if($nova_qtd > $objA->get('qtd')){
                    $nova_qtd = $objA->get('qtd');
                }
                $objA->set('qtd_disp', $objA->get('qtd_disp') + $objIA->get('qtd'));
                $objA->save();
            }
            ItemAluguel::delete($objIA->get('id'));
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
        $obj->set('valor_entrada', 50.00);
        $valor_restante = 0;

        if ($codigo > 0) {
            $obj = self::load($codigo);
            $valor_restante = (float)$obj->get('valor_restante');
        }else{
        	$codigo = time();
        	$string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }

        $string .= '
        <div class="col-sm-12 mb-3 required">
            <input type="hidden" name="id_cliente" id="id_cliente" value="' . $obj->get('id_cliente') . '"/>
            <div class="input-group">
                <div class="form-floating">
                    <input id="nome_fantasia" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="pessoas" data-name="nome-cpf" data-div="()" data-field="id_cliente" data-edit="btn_edit" value="'.$obj->getCliente()->getPessoa()->get('nome').'"/>
                <label for="id_cliente">Cliente</label>
                </div>
                <div class="d-flex">
                    <a type="button" class="btn btn-light btn-sm px-3" id="btn_edit" onclick="javascript:modalForm(`clientes`, '.$obj->getCliente()->get('id').');" style="filter: contrast(0.5);" value="'.$obj->getCliente()->getPessoa()->get('id').'">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a type="button" class="btn btn-secondary btn-sm px-3 text-white fw-bold" onclick="javascript:modalForm(`clientes`,0);">
                        <i class="ti ti-plus"></i>
                    </a>
                </div>
                
            </div>
        </div>';
    	
        $string .= '
        <div class="col-sm-6 mb-3 required">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_coleta" id="dt_coleta" placeholder="" onchage="atualizarDtColeta(this.value)" value="'.(Utils::dateValid($obj->get('dt_coleta')) ? Utils::dateFormat($obj->get('dt_coleta'),'d/m/Y') : '').'" required>
                <label class="form-label">Data de Coleta*</label>
            </div>
        </div>';
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_uso" placeholder="" value="'.(Utils::dateValid($obj->get('dt_uso')) ? Utils::dateFormat($obj->get('dt_uso'),'d/m/Y') : '').'">
                <label class="form-label">Data de Uso</label>
            </div>
        </div>';
    	$string .= '
        <div class="col-sm-6 mb-3 required">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_prazo"  id="dt_prazo" placeholder="Data" required value="'.(Utils::dateValid($obj->get('dt_prazo')) ? Utils::dateFormat($obj->get('dt_prazo'),'d/m/Y') : '').'">
                <label class="form-label">Prazo de Devolução</label>
            </div>
        </div>';
    	
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input class="form-control date" type="text" name="dt_entrega" placeholder="" value="'.(Utils::dateValid($obj->get('dt_entrega')) ? Utils::dateFormat($obj->get('dt_entrega'),'d/m/Y') : '').'">
                <label class="form-label">Entrega</label>
            </div>
        </div>';

        $string .= '
    <div class="col-sm-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between"> 
                    <h5 class="card-title">Itens do Aluguel</h5>
                    <a type="button" class="btn btn-secondary btn-sm px-3 text-white fw-bold" onclick="atualizarDtColeta();">
                        <i class="ti ti-plus"></i>Adicionar Itens
                    </a>
                </div>

                <div>
                    <script> 
                        function atualizarDtColeta(){
                            let dt_coleta = $(`#dt_coleta`).val();
                            let dt_coleta_formatada = dt_coleta.replace(/\//g, `-`); 
                            let dt_prazo = $(`#dt_prazo`).val();
                            let dt_prazo_formatada = dt_prazo.replace(/\//g, `-`); 
                            modalForm(`itensaluguel`,0, `/id_aluguel/'.$codigo.'/dt_coleta/`+ dt_coleta_formatada + `/dt_prazo/`+ dt_prazo_formatada, loadItens)
                        }

                        function loadItens(){
                            tableList(`itensaluguel`, `id_aluguel='.$codigo.'&dt_coleta='.$obj->get('dt_coleta').'&dt_prazo='.$obj->get('dt_prazo').'&offset=10`, `txt_itens`, false);
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
        <div class="col-sm-6 mb-3 required">
            <div class="form-floating">
                <select class="form-select" name="status">';
                foreach (self::$arr_status as $k => $v){
                    $string .= '<option value="'.$k.'" '.($k == $obj->get('status') ? 'selected' : '').'>'.$v.'</option>';    
                }
                $string .='
                </select>
                <label>Status</label>
            </div>
        </div>        
        ';
        $string .= '
            <div class="col-sm-6 mb-3 required">
                <div class="form-floating">
                    <input class="form-control" maxlength="255" name="local_uso" placeholder="" value="'.$obj->get('local_uso').'">
                    <label class="form-label">Local de Uso</label>
                </div>
        </div>';

        $string .= '
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                    <input class="form-control money" name="valor_aluguel" placeholder="" value="'.($obj->get('valor_aluguel') != '' ? Utils::parseMoney($obj->getValorAluguel()) : '').'">
                    <label class="form-label">Valor do Aluguel</label>
                </div>
        </div>';
       
        $string .= '
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                    <input class="form-control money" name="valor_entrada" placeholder="" value="'.($obj->get('valor_entrada') != '' ? Utils::parseMoney($obj->get('valor_entrada')) : '').'">
                    <label class="form-label">Valor de Entrada</label>
                </div>
        </div>';
        $string .= '
            <div class="col-sm-4 mb-3">
                <div class="form-floating">
                    <input class="form-control money" name="valor_restante" placeholder="" value="'. Utils::parseMoney($valor_restante).'">
                    <label class="form-label">Valor Restante</label>
                </div>
        </div>';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th class="col-sm-4 p-3">Cliente</th>
                    <th class="col-sm-2">Status</th>
                    <th class="text-center">Coleta</th>
                    <th class="text-center">Prazo</th>
                    <th class="text-center">Devolu&ccedil;&atilde;o</th>
                    <th class="text-center">Valor (R$)</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr id="tr-'.$obj->getTableName().$obj->get('id').'"">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table>
              ';
        
        return $string;
    }

    public static function getLine($obj){
        $arquivo_pdf = __BASEPATH__.'/uploads/atestado-escoalridade.pdf';
        return '
        <td class="link-edit p-3">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getCliente()->getPessoa()->get('nome') , false).' <span class="small">('.Utils::dateFormat($obj->get('dt_cad'), 'd/m/Y').')</span></td>
        <td>'.self::$arr_status[$obj->get('status')].'</td>
        <td class="text-center '.$obj->getStatus($obj->get('dt_coleta')).'">'.(Utils::dateValid($obj->get('dt_coleta')) ? Utils::dateFormat($obj->get('dt_coleta'), 'd/m/Y') : ' - ').'</td>
        <td class="text-center '.$obj->getStatus($obj->get('dt_prazo')).'">'.(Utils::dateValid($obj->get('dt_coleta')) ? Utils::dateFormat($obj->get('dt_prazo'), 'd/m/Y') : ' - ').'</td>
        <td class="text-center">'.(Utils::dateValid($obj->get('dt_entrega')) ? Utils::dateFormat($obj->get('dt_entrega'), 'd/m/Y') : ' - ').'</td>
        <td class="text-center">'.Utils::parseMoney($obj->getValorAluguel()).'</td>
        <th><a target="_blank" class="btn btn-sm border-transparent opacity-50" title="Imprimir Termos"><i class="ti ti-printer" onclick="montarPdf(`proximas-devolucoes`,'.$obj->get('id').')"></i></a></th>
        '.GG::getResponsiveList([
            'Prazo' => Utils::dateFormat($obj->get('dt_prazo'), 'd/m/Y'),
            'Valor' => Utils::parseMoney($obj->getValorAluguel()),
        ], $obj).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        if($request->query('id_cliente') != ''){
            $paramAdd .= " AND `id_cliente` IN ({$request->query('id_cliente')})";
        }
        
        if((float)$request->query('valor_min') > 0.00){
            $val = str_replace(',', '.', $request->query('valor_min'));
            $paramAdd .= " AND `valor_aluguel` >= {$val} ";
        }
      
        if((float)$request->query('valor_max') > 0.00){
            $val = str_replace(',', '.', $request->query('valor_max'));
            $paramAdd .= " AND `valor_aluguel` <= {$val} ";
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
        $cliente = new Cliente();
        if(Cliente::exists("id = {$request->getInt('cliente')}")){
            $cliente = Cliente::load($request->get('cliente'));
        }
        $string = '';

        $string .= '
        <div class="col-sm-12 mb-3">
            <div class="form-floating">
                <select class="form-select" id="id_cliente_filter" name="id_cliente" multiple>';
                        $conn = new Connection();
                        $sql = "SELECT c.id as id_cliente, c.id_pessoa, p.id as id_pessoa, p.nome FROM clientes c
                                INNER JOIN pessoas p ON p.id = c.id_pessoa
                                ORDER BY p.nome";
                        $rs = $conn->prepareStatement($sql)->executeReader();

                        while($rs->next()){
                            $string .= '<option value="'.$rs->getInt('id_cliente').'" '.($rs->getInt('id_cliente') == $request->query('id_cliente') ? 'selected' : '').'>'.$rs->getString('nome').'</option>';
                        }
                    $string .='
                </select>
                <label>Cliente</label>
            </div> 
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="valor_min" id="filterPrecoMin" type="text" class="form-control money" value="'.$request->query('valor_min').'" placeholder="seu dado aqui" />
                <label for="filterPrecoMin" class="form-label">Valor min</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="valor_max" id="filterPrecoMax" type="text" class="form-control money" value="'.$request->query('valor_max').'" placeholder="seu dado aqui" />
                <label for="filterPrecoMax" class="form-label">Valor max</label>
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
                    'id desc' => 'Mais recente primeiro',
                    'id' => 'Mais antigo primeiro',
                    'dt_coleta desc' => 'Coleta mais recente',
                    'dt_coleta' => 'Coleta mais antiga',
                    'valor_aluguel' => 'Menor pre&ccedil;o',
                    'valor_aluguel desc' => 'Maior pre&ccedil;o',
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

    public function realizarDevolucao($dt_devolucao, $valor_pago){
        if(!self::exists($this->get('id'))){
            return [
                'msg' => "Aluguel inválido",
                'success' => false
            ];
        }

        if(!Utils::dateValid($dt_devolucao)){
            return [
                'msg' => "Data de devolução inválida",
                'success' => false,
            ];
        }
        
        if($valor_pago > $this->get('valor_restante')){
            return [
                'msg' => "Valor Restante de devolução inválido",
                'success' => false,
            ];
        }

        $this->set('dt_entrega', Utils::dateFormat($dt_devolucao, 'Y-m-d'));
        $this->set('valor_restante', Utils::parseMoney((float)$this->get('valor_restante') - (float)$valor_pago));
        $this->set('status', 3);
        $this->save();

        $rs = ItemAluguel::search([
            's' => 'id, id_item, qtd',
            'w' => "id_aluguel = {$this->get('id')} AND tipo_item = 1",
        ]);

        while($rs->next()){
            $obj = Acessorio::load($rs->getInt('id_item'));
            $obj->restaurarQtdItens($rs->getInt('qtd'));
        }

        return [
            'msg' => "Devolução realizada com sucesso",
            'success' => true,
        ];
    }

    public function montarContrato(){
        global $defaultPath;
        $ret = [];
        $uploadsPath = $defaultPath . 'uploads/';
        $contratosPath = $uploadsPath . 'contratos/';

        // Garantir que a pasta 'uploads' exista
        if (!is_dir($uploadsPath)) {
            if (!mkdir($uploadsPath, 0777, true) && !is_dir($uploadsPath)) {
                $ret = [
                 'msg' => "Erro: Não foi possível criar o diretório 'uploads'.",
                 'success' => 'false'  
                ];
            }
        }
    
        // Garantir que a pasta 'contratos' dentro de 'uploads' exista
        if (!is_dir($contratosPath)) {
            if (!mkdir($contratosPath, 0777, true) && !is_dir($contratosPath)) {
                $ret = [
                   'msg' => "Erro: Não foi possível criar o diretório 'uploads/contratos'.",
                   'success' => 'false'
                ];
            }
        }

        $cpf = $this->getCliente()->getPessoa()->get('cpf') != '' ? 'inscrito no CPF sob o nº <strong>'.Utils::getMaskedDoc($this->getCliente()->getPessoa()->get('cpf')).'</strong>' : ' CPF não informado';

        $itens = $this->getItensAluguel();
        $lista_itens = '';
        $x = 1;
        foreach($itens as $obj){
            if($obj->get('tipo_item') == 1){
                $lista_itens .= "<p>1.{$x}&nbsp;&nbsp;&nbsp;Acessório: ";
            }elseif($obj->get('tipo_item') == 2){
                $lista_itens .= "<p>1.{$x}&nbsp;&nbsp;&nbsp;Traje: ";
            }
            $lista_itens .= "<strong>{$obj->getItem()->get('descricao')}</strong></p>";
            $x++;
        }

        $data_retirada = Utils::dateValid($this->get('dt_coleta')) ? Utils::dateFormat($this->get('dt_coleta'), 'd/m/Y') : date('d/m/Y');
        $data_devolucao = Utils::dateValid($this->get('dt_prazo')) ? Utils::dateFormat($this->get('dt_prazo'), 'd/m/Y') : 'não informada';

        $total_extenso = Utils::numberToWords($this->getValorAluguel());
        $entrada = Utils::parseMoney($this->get('valor_entrada')); 
        $entrada_extenso = Utils::numberToWords((float)$this->get('valor_entrada'),' e ', ' e ');
        $restante = Utils::parseMoney((float)$this->getValorAluguel() - (float)$this->get('valor_entrada')); 
        $restante_extenso = Utils::numberToWords((float)$restante, ' e ', ' e '); 

        $data_atual = date('d/m/Y');
        $html = '
        <html>
            <head>
                <title>Contrato de Locação</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.3;
                    }
                    h1, h2 {
                        font-size: 14px;
                        text-align: left;
                    }
                    p, ul {
                        text-align: justify;
                        font-size: 14px; 
                        line-height: 1.3;
                        margin-bottom: 0px;
                    }
                    .titulo {
                        font-size: 16px;
                        text-align: center;
                    }
                    .subtitulo{
                        margin-bottom: 0px;
                    }
                    .section {
                        margin-bottom: 5px;
                    }
                    .signature {
                        margin-top: 30px;
                        display: flex;
                        justify-content: space-between; /* Espaça igualmente os blocos */
                        align-items: center; /* Alinha verticalmente ao centro */
                        gap: 20px; /* Espaço extra entre os itens */
                    }
                    .signature div {
                        flex: 1; /* Faz com que cada assinatura ocupe a mesma largura */
                        text-align: center; /* Centraliza o texto em cada bloco */
                    }
                </style>
            </head>
            <body>
            <h1 class="titulo">CONTRATO DE LOCAÇÃO</h1>

                <div class="section">
                    <p>
                    Pelo presente contrato, de um lado, <strong>'.$this->getCliente()->getPessoa()->get('nome').'</strong>, '.$cpf.', residente no endereço <strong>'.$this->getCliente()->getPessoa()->getAddress().'</strong>, doravante chamado de LOCATÁRIO; e de outro lado, a loja Ateliê Festa e Fantasia, localizada na Rua Santa Maria, 250 - Centro, Colatina - ES, 29700-200, doravante chamada de LOCADORA, acordam com as seguintes condições:
                    </p>
                </div>

                <h2 class="subtitulo">1.&nbsp;&nbsp;&nbsp;OBJETO DO CONTRATO</h2>
                <div class="section">
                    <p>
                    Este contrato tem como objetivo a locação dos trajes descritos abaixo:
                    </p>
                    '.$lista_itens.'
                </div>

                <h2 class="subtitulo">2.&nbsp;&nbsp;&nbsp;PRAZO DA LOCAÇÃO</h2>
                <div class="section">
                    <p>2.1.&nbsp;&nbsp;&nbsp;Retirada: <strong>'.$data_retirada.'</strong>.</p>
                    <p>2.2.&nbsp;&nbsp;&nbsp;Devolução: <strong>'.$data_devolucao.'</strong>.</p>
                </div>

                <h2 class="subtitulo">3.&nbsp;&nbsp;&nbsp;VALOR E PAGAMENTO</h2>
                <div class="section">
                    <p>3.1.&nbsp;&nbsp;&nbsp;Valor total: R$ <strong>'.Utils::parseMoney($this->getValorAluguel()).'</strong> (<strong>'.$total_extenso.' reais</strong>).</p>
                    <p>3.2.&nbsp;&nbsp;&nbsp;Entrada: R$ <strong>'.$entrada.'</strong> (<strong>'.$entrada_extenso.' reais</strong>).</p>
                    <p>3.3.&nbsp;&nbsp;Saldo restante: R$ <strong>'.$restante.'</strong> (<strong>'.$restante_extenso.' reais</strong>), a ser quitado na retirada do traje, em dinheiro ou PIX.</p>
                </div>

                <h2 class="subtitulo">4.&nbsp;&nbsp;&nbsp;RESPONSABILIDADES DO LOCATÁRIO</h2>
                <div class="section">
                    <p>4.1.&nbsp;&nbsp;&nbsp;Conferir os trajes no momento da retirada.</p>
                    <p>4.2.&nbsp;&nbsp;&nbsp;Cuidar do traje e evitar manchas ou danos.</p>
                    <p>4.3.&nbsp;&nbsp;&nbsp;Não fazer ajustes ou alterações no traje.</p>
                    <p>4.4.&nbsp;&nbsp;&nbsp;Devolver o traje no prazo combinado.</p>
                    <p>4.5.&nbsp;&nbsp;&nbsp;Usar o traje exclusivamente para uso pessoal.</p>
                </div>

                <h2 class="subtitulo">5.&nbsp;&nbsp;&nbsp;MULTAS E PENALIDADES</h2>
                <div class="section">
                    <p>5.1.&nbsp;&nbsp;&nbsp;Multa por atraso na devolução: R$ 50,00 (cinquenta reais) por dia.</p>
                    <p>5.2.&nbsp;&nbsp;&nbsp;Taxa de pmpeza em caso de sujeira excessiva: R$ 30,00 (trinta reais).</p>
                    <p>5.3.&nbsp;&nbsp;&nbsp;Em caso de perda de acessórios, será cobrado o valor correspondente.</p>
                    <p>5.4.&nbsp;&nbsp;&nbsp;O valor da entrada não será devolvido em caso de desistência.</p>
                </div>

                <div class="section">
                    <p>Data: <strong>'.$data_atual.'</strong></p>
                </div>

                <div class="section" style="margin-top: 30px;">
                    <table style="width: 100%; text-align: center; border-spacing: 30px 0;">
                        <tr>
                            <td style="width: 50%; padding: 10px;">
                            _____________________________<br>
                            '.$this->getCliente()->getPessoa()->get('nome').'<br>
                            '.($cpf != '' ? Utils::getMaskedDoc($this->getCliente()->getPessoa()->get('cpf')) : '').'<br>
                            '.$this->getCliente()->getPessoa()->getAddress(0, 1).'
                            </td>
                            
                            <td style="width: 50%; padding: 10px;">
                            _____________________________<br>
                            Ateliê Festa e Fantasia<br>
                            Rua Santa Maria, 250, Centro,<br>
                            Colatina - ES.
                            </td>
                        </tr>
                    </table>
                </div>
            </body>
        </html>
        ';

        // echo $html; exit;
        try {
            $html2pdf = new Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [30, 5, 20, 8]);
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($html);

            $id = $this->get('id');
            if (empty($id)) {
            $ret = "Erro: ID inválido ou não fornecido.";
            }

            $fileName = $contratosPath . 'contrato_'.$this->get('id') . '.pdf';
            $html2pdf->Output($fileName, 'F');

            return $fileName;
        } catch (Exception $e) {
            return "Erro ao gerar o PDF: " . $e->getMessage();
        }
    }
}
