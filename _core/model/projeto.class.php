<?php

class Projeto extends Flex {

    protected $tableName = 'projetos';
    protected $mapper = array(
        'id' => 'int',
        'id_cliente' => 'int',
        'nome' => 'string',
        'valor' => 'numeric',
        'area' => 'number',
        'dt_ini' => 'string',
        'dt_entrega' => 'string',
        'status' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');

    
    public static $configGG = array(
        'nome' => 'Projetos',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => false,
        'show-menu' => true,
        'icon' => 'ti ti-clipboard-list'
    );

    public static $key = '@KV@';

    public function getToken(){
        return md5(self::$key.$this->get('id'));
    }

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `projetos`;
        CREATE TABLE `projetos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_cliente` int(11) NOT NULL,
            `nome` varchar(255) NOT NULL,
            `valor` FLOAT(11,2) DEFAULT NULL,
            `area` FLOAT(11,2) DEFAULT NULL,
            `dt_ini` date DEFAULT NULL,
            `dt_entrega` date DEFAULT NULL,
            `status` INT(1) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    }

    public static $nm_status = [
        2 => 'Em andamento',
        3 => 'Finalizado'
    ];

    protected $cliente = null;
    public function getCliente()
    {
        if (!$this->cliente || $this->cliente->get('id') != $this->get('id_cliente')) {
            if (Pessoa::exists((int) $this->get('id_cliente'), 'id')) {
                $this->cliente = Pessoa::load($this->get('id_cliente'));
            } else {
                $this->cliente = new Pessoa();
                $this->cliente->set('nome', 'Pessoa n&atilde;o encontrada');
            }
        }
        return $this->cliente;
    }

    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

        if(!isset($_POST['id_cliente']) || $_POST['id_cliente'] == ''){
            $error .= '<li>O campo "Cliente" n&atilde;o foi informado</li>';
        }

        if(!isset($_POST['nome']) || $_POST['nome'] == ''){
            $error .= '<li>O campo "Obra" n&atilde;o foi informado</li>';
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
            $id = $request->getInt('id');
            $obj = new $classe(array($id));

            if ($id > 0) {
                $obj = self::load($id);
            }
           
            $obj->set('id_cliente', (int) $_POST["id_cliente"]);
            $obj->set('nome', $_POST["nome"]);
            $obj->set('status', (int)$_POST["status"]);
            $obj->set('valor', (float) str_replace(',','.',str_replace('.','',$_POST["valor"])));
            $obj->set('area', (float) str_replace(',','.',str_replace('.','',$_POST["area"])));
            $obj->set('dt_ini', Utils::dateFormat($_POST["dt_ini"], 'Y-m-d'));
            $obj->set('dt_entrega', Utils::dateFormat($_POST["dt_entrega"], 'Y-m-d'));

            $obj->save();

            $id = $obj->get('id');
            
            echo 'Registro salvo com sucesso!';

            $ret['success'] = true;
            $ret['obj'] = $obj;   
        }

        return $ret;
    }

    public static function delete($ids) {
        $classe = __CLASS__;
        $obj = new $classe();

        Flex::dbDelete(new Contato(), "id_pessoa IN({$ids})");

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {
        global $request, $objSession;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);

        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
            $codigo = time();
            $string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
            $obj->set('id_cliente', $request->get('id_pessoa'));
        }

        $string.= '
    	<ul class="nav nav-underline gap-4 shadow-sm py-0 bg-white flex-nowrap overflow-x-auto scroll-transparent px-2">
            <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center active" data-bs-toggle="tab" data-bs-target="#dados'.$obj->getTableName().'" role="tab" aria-controls="#dados'.$obj->getTableName().'" aria-selected="true">Dados</button></li>';

            if($obj->get('id') > 0){
                $string.='
                <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#historico'.$obj->getTableName().'" role="tab" aria-controls="#historico'.$obj->getTableName().'" aria-selected="true">Histórico <a href="javascript:void(0);"  data-bs-toggle="tab" data-bs-target="#historico'.$obj->getTableName().'" role="tab" aria-controls="#historico'.$obj->getTableName().'" aria-selected="true" onclick="javascript: modalForm(`historico`,0,`/id_projeto/'.$codigo.'`,()=>{
                    loadHistorico();
                });" class="text-success lh-1 opacity-50"><i class="fs-4 ti ti-circle-plus"></i></a></button></li>

                <li class="active"><button type="button" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#contas'.$obj->getTableName().'" role="tab" aria-controls="#contas'.$obj->getTableName().'" aria-selected="true">Contas <a href="javascript:void(0);"  data-bs-toggle="tab" data-bs-target="#contas'.$obj->getTableName().'" role="tab" aria-controls="#contas'.$obj->getTableName().'" aria-selected="true" onclick="javascript: modalForm(`contas`,0,`/id_projeto/'.$codigo.'`,()=>{
                    loadContasReceber();
                    loadContasPagar();
                });" class="text-success lh-1 opacity-50"><i class="fs-4 ti ti-circle-plus"></i></a></button></li>
                
                <li class="active"><button type="button" onclick="javascript: loadMovimentacoes();" class="nav-link d-flex gap-2 align-items-center" data-bs-toggle="tab" data-bs-target="#movimentacoes'.$obj->getTableName().'" role="tab" aria-controls="#movimentacoes'.$obj->getTableName().'" aria-selected="true">Movimentações <a href="javascript:void(0);"  data-bs-toggle="tab" data-bs-target="#movimentacoes'.$obj->getTableName().'" role="tab" aria-controls="#movimentacoes'.$obj->getTableName().'" aria-selected="true" onclick="javascript: modalForm(`movimentacoes`,0,`/id_projeto/'.$codigo.'`,()=>{
                    loadMovimentacoes();
                });" class="text-success lh-1 opacity-50"><i class="fs-4 ti ti-circle-plus"></i></a></button></li>';
            }
            $string .='
            
        </ul>';

        $string.= '
        <div class="tab-content py-4">';

            $string.= '
                <div class="tab-pane fade show active" id="dados'.$obj->getTableName().'">
                    <div class="row">
                        <input type="hidden" name="id_cliente" id="id_cliente" value="' . $obj->get('id_cliente') . '"/>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input id="nomeCidade" type="text" placeholder="seu dado aqui" class="form-control autocomplete" data-table="pessoas" data-name="nomefantasia" data-field="id_cliente" value="' .($obj->get('id_cliente') > 0 ? $obj->getCliente()->get('nomefantasia') : ''). '"/>
                                <label for="id_cliente">Cliente</label>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <input name="nome" id="nome" maxlength="20" type="text" class="form-control" placeholder="" value="'.$obj->get('nome').'" />
                                <label for="nome">Obra</label>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4 mb-3" required>
                            <div class="form-floating">
                                <input name="valor" id="valor" maxlength="255" type="text" class="form-control money" placeholder="" value="'.Utils::parseMoney((float) $obj->get('valor')).'" />
                                <label for="valor" class="">Valor<small class="rule"> (R$)</small></label>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-lg-4 mb-3" required>
                            <div class="form-floating">
                                <input name="area" id="area" maxlength="255" type="text" class="form-control money" placeholder="" value="'.Utils::parseMoney((float) $obj->get('area')).'" />
                                <label for="area" class="">Área<small class="rule"> (m&#x00B2)</small></label>
                            </div>
                        </div>
                        
                        <div class="col-sm-4 mb-3">
                            <div class="form-floating">
                                <input name="dt_ini" id="dt_ini" type="date" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('dt_ini').'"/>
                                <label for="">Data início</label>
                            </div>
                        </div>
                        
                        <div class="col-sm-4 mb-3">
                            <div class="form-floating">
                                <input name="dt_entrega" id="dt_entrega" type="date" placeholder="seu dado aqui" class="form-control" value="'.$obj->get('dt_entrega').'"/>
                                <label for="">Data Entrega</label>
                            </div>
                        </div>
                    
                        <div class="col-sm-4 mb-3">
                            <div class="form-floating">
                                <select class="form-select" id="status" name="status">';
                                foreach(self::$nm_status as $k => $v){
                                    $string .= '<option value="'.$k.'" '.($k == $obj->get('status') ? 'selected' : '').'>'.$v.'</option>';
                                }
                                $string .='
                                </select>
                                <label for="">Status</label>
                            </div>
                        </div>
                    </div>
                </div>';

                $string.= '
                <div class="tab-pane fade" id="historico'.$obj->getTableName().'">
                    <script> 
                        function loadHistorico(resp){ 
                            tableList(`historico`, `id_projeto='.$codigo.'&offset=10`, `txt_historico`, false);
                        } 
                        loadHistorico();
                    </script>
                    <div class="form-group col-sm-12" id="txt_historico">'.GG::moduleLoadData('loadHistorico();').'</div>    
                </div>'; 

                $string.= '
                <div class="tab-pane fade" id="contas'.$obj->getTableName().'">

                    <ul class="nav nav-underline gap-4 mt-0">
                        <li class="active"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#receber'.$obj->getTableName().'" role="tab" aria-controls="#receber'.$obj->getTableName().'" aria-selected="true">Receber</button></li>

                        <li class="active"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#pagar'.$obj->getTableName().'" role="tab" aria-controls="#pagar'.$obj->getTableName().'" aria-selected="true">Pagar</button></li>
                    </ul>

                    <div class="tab-content py-4">
                        <div class="tab-pane fade show active" id="receber'.$obj->getTableName().'">
                            <script> 
                                function loadContasReceber(){ 
                                    tableList(`contas`, `id_projeto='.$codigo.'&tipo=2&offset=10&order=vencimento`, `txtcontasreceber`, false);
                                } 
                                loadContasReceber();
                            </script>
                            <div class="form-group col-sm-12" id="txtcontasreceber">'.GG::moduleLoadData('loadContasReceber();').'</div>    
                        </div>
                        <div class="tab-pane fade" id="pagar'.$obj->getTableName().'">
                            <script> 
                                function loadContasPagar(){ 
                                    tableList(`contas`, `id_projeto='.$codigo.'&tipo=1&offset=10&order=vencimento`, `txtcontaspagar`, false);
                                } 
                                loadContasPagar();
                                </script>
                            <div class="form-group col-sm-12" id="txtcontaspagar">'.GG::moduleLoadData('loadContasPagar();').'</div>  
                        </div>       
                    </div>                
                </div>
                ';
                
                $string.= '
                <div class="tab-pane fade" id="movimentacoes'.$obj->getTableName().'">
                    <div class="tab-content py-4">
                        <div class="tab-pane fade show active" id="entrada'.$obj->getTableName().'">
                            <script> 
                                function loadMovimentacoes(){ 
                                    tableList(`movimentacoes`, `id_projeto='.$codigo.'&todas=true&offset=10&order=data DESC`, `txtmovimentacoes`, false);
                                } 
                                loadMovimentacoes();
                                </script>
                            <div class="form-group col-sm-12" id="txtmovimentacoes">'.GG::moduleLoadData('loadMovimentacoes();').'</div>    
                        </div>
                    </div>                
                </div>
                ';
        $string.= '
        </div>';

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <div class="table-responsive px-xs-0"><table class="table table-hover">
            <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-3">Nome</th>
                    <th class="col-sm-3">Cliente</th>
                    <th class="col-sm-2">Início</th>
                    <th class="col-sm-2">Entrega</th>
                    <th class="col-sm-1"></th>
                    <th class="col-sm-1"></th>
                </tr>
                </thead>
                <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr class="position-relative" id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table></div>
              ';
        
        return $string;
    }

    public static function getLine($obj){
        global $request;
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="text-uppercase link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome'), false).'</td>
        <td>'.$obj->getCliente()->get('nomefantasia').'</td>
        <td>'.Utils::dateFormat($obj->get('dt_ini'), 'd/m/Y').'</td>
        <td>'.(Utils::dateValid($obj->get('dt_entrega')) ?  Utils::dateFormat($obj->get('dt_entrega'), 'd/m/Y') : ' - ').'</td>
        <td><a href="'.__PATH__.'projeto/'.$obj->get('id').'" class="btn btn-sm" data-toggle="tooltip" data-bs-placement="top" title="Ver Projeto"><i class="ti ti-share-3"></i></a></td>
        <td><a href="'.__BASEPATH__.'projeto/'.$obj->getToken().'" target="_blank"class="btn btn-sm" data-toggle="tooltip" data-bs-placement="top" title="Link do cliente"><i class="ti ti-link"></i></a></td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }


        if($request->query('id_pessoa') != ''){
            $paramAdd .= " AND id_cliente = {$request->query('id_pessoa')}";
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
        <div class="col-sm-12 mb-3">
            <div class="form-floating">
                <input name="nome" id="filterNome" type="text" placeholder="" class="form-control" value="'.$request->query('nome').'"/>
                <label for="filterNome">Nome</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="inicio" id="filterInicio" type="text" class="form-control date" value="'.$request->query('inicio').'" placeholder="" />
                <label for="filterInicio">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-6 mb-3">
            <div class="form-floating">
                <input name="fim" id="filterFim" type="text" class="form-control date" value="'.$request->query('fim').'" placeholder="" />
                <label for="filterFim" class="">Cadastrados at&eacute;</label>
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
                <label for="order" class="">Ordem</label>
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
                <label for="offset" class="">Registros</label>
            </div>
        </div>';

        return $string;
    }

}

