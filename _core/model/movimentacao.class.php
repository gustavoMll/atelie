<?php

class Movimentacao extends Flex {

    protected $tableName = 'movimentacoes';
    protected $mapper = array(
        'id' => 'int',
        'tipo' => 'int',
        'id_conta' => 'int',
        'id_projeto' => 'int',
        'status' => 'int',
        'valor' => 'number',
        'juro' => 'number',
        'multa' => 'number',
        'desconto' => 'number',
        'data' => 'string',
        'obs' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');

    public static $nmTipo = array(
        1 => 'Pagamento',
        2 => 'Recebimento',
    );

    public static $nmStatus = array(
        0 => 'Cancelado',
        1 => 'Aberto',
        2 => 'Liquidado parcial',
        3 => 'Liquidado total',
        
    );
    

    public static $configGG = array(
        'nome' => 'Movimentações',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
        'icon' => '',
    );

    protected $cr = null;
    public function getConta() {
        if (!$this->cr || $this->cr->get('id') != $this->get('id_conta')) {
            if($this->get('id_conta') == 0){

            }else{ 
                $this->cr = Conta::load($this->get('id_conta'));
            }
        }
        return $this->cr;
    }
    
    protected $projeto = null;
    public function getProjeto() {
        if (!$this->projeto || $this->projeto->get('id') != $this->get('id_projeto')) {
            $this->projeto = Projeto::load($this->get('id_projeto'));
        }
        return $this->projeto;
    }

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `movimentacoes`;
        CREATE TABLE `movimentacoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tipo` int(1) NOT NULL DEFAULT 1,
            `id_conta` int(11) NOT NULL,
            `id_projeto` int(11) NOT NULL,
            `status` int(1) NOT NULL,
            `data` date NOT NULL,
            `valor` float(11,2) NOT NULL DEFAULT 0.00,
            `juro` float(11,2) NOT NULL DEFAULT 0.00,
            `multa` float(11,2) NOT NULL DEFAULT 0.00,
            `desconto` float(11,2) NOT NULL DEFAULT 0.00,
            `adicional` float(11,2) NOT NULL DEFAULT 0.00,
            `obs` text DEFAULT NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
        if(!isset($_POST['obs']) || $_POST['obs'] == ''){
            $error .= '<li>&Eacute; necess&aacute;rio uma descri&ccedil;&atilde;o</li>';
        }
        
        if($error==''){
            return true;
        }else{
            echo '<ul>'.$error.'</ul>';
            return false;
        }
    }

    public static function saveForm() {
        global $request;
        $classe = __CLASS__;
        $ret = array('success'=>false, 'obj'=> null);

        if(self::validate()){
            $id = $request->getInt('id');
            $obj = new $classe(array($id));

            if ($id > 0) {
                $obj = self::load($id);
            }
            
            $obj->set('tipo', (int) $_POST["tipo"]);
            $obj->set('id_conta', $_POST["id_conta"]);
            $obj->set('id_projeto', $_POST["id_projeto"]);
            $obj->set('status', (int) $_POST["status"]);
            $obj->set('data', Utils::dateFormat($_POST["data"],'Y-m-d'));
            $obj->set('obs', $_POST["obs"]);

            if($obj->get('tipo')==3){
                $obj->set('valor', Utils::parseFloat($_POST["valor"]));
                
            }elseif(in_array($obj->get('status'),array(0,1))){
                $obj->set('valor', 0);
                $obj->set('juro', 0);
                $obj->set('multa', 0);
                $obj->set('desconto', 0);

            }else{
                $obj->set('valor', Utils::parseFloat($_POST["valor"]));
                $obj->set('juro', Utils::parseFloat($_POST["juro"]));
                $obj->set('multa', Utils::parseFloat($_POST["multa"]));
                $obj->set('desconto', Utils::parseFloat($_POST["desconto"]));
            }

            $obj->save();

            if($obj->get('id_conta') != 0){
                $obj->getConta()->set('status',$obj->get('status'));
                if($obj->get('status') == 1){
                    $obj->getConta()->set('pagamento','');
                }elseif($obj->get('status') == 3){
                    $obj->getConta()->set('pagamento',$obj->get('data'));
                }
                $obj->getConta()->save();
            }

            $id = $obj->get('id');
            
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

        return Flex::dbDelete($obj, "id IN({$ids})");
    }
    
    public static function form($codigo = 0) {
        global $request, $Config;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();

        $obj->set('id', $codigo);
        $obj->set('tipo',1);
        $obj->set('status',3);
        $obj->set('valor',$request->get('valor') != '' ? (float) $request->get('valor') : 0);
        $obj->set('juro',0);
        $obj->set('multa',0);
        $obj->set('desconto',0);
        $obj->set('data',$request->get('data') != '' ? Utils::dateFormat($request->get('data'),'d/m/Y') : date('Y-m-d'));
        $obj->set('obs',urldecode($request->get('obs')));
        
        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
            $obj->set('tipo',(int) $request->getInt('tipo'));
            $obj->set('id_conta',  $request->getInt('id_conta'));
            $obj->set('id_projeto', (int) $request->getInt('id_projeto'));
        }

        $size = 3;

        $string .= '
        <input name="id_conta" type="hidden" class="form-control" value="' . $obj->get('id_conta') . '"/>
        <input name="id_projeto" type="hidden" class="form-control" value="'. $obj->get('id_projeto').'">
        <div class="row g-2 g-lg-3">';


        if($obj->get('id_conta') == 0) {
            $string .= '<div class="mb-3 col-sm-'.$size.' required">';
            $string .= '<div class="form-floating">';
            $string .= '<select class="form-select" name="tipo" id="tipo" onchange="if(this.value==1){
                 $(\'#div_portador\').removeClass(\'d-none\'); 
                 $(\'#div_fp1\').removeClass(\'d-none\'); 
                 $(\'#div_fp2\').addClass(\'d-none\'); 
                 $(\'#div_portador_origem\').addClass(\'d-none\');
                 $(\'#div_despesa\').addClass(\'d-none\');
                 $(\'#div_multas\').removeClass(\'d-none\');
                 $(\'.div-cc\').show(); 
            }else if(this.value==3){
                 $(\'#div_portador\').removeClass(\'d-none\');
                 $(\'#div_portador_origem\').removeClass(\'d-none\');
                 $(\'#div_fp1\').addClass(\'d-none\'); 
                 $(\'#div_fp2\').addClass(\'d-none\');
                 $(\'#div_despesa\').removeClass(\'d-none\');
                 $(\'#div_multas\').addClass(\'d-none\');
                 $(\'.div-cc\').hide();
            }else{
                 $(\'#div_portador\').addClass(\'d-none\');
                 $(\'#div_fp1\').addClass(\'d-none\'); 
                 $(\'#div_fp2\').removeClass(\'d-none\').addClass(`col-sm-6`); 
                 $(\'#div_portador_origem\').addClass(\'d-none\');
                 $(\'#div_despesa\').addClass(\'d-none\');
                 $(\'#div_multas\').removeClass(\'d-none\');
                 $(\'.div-cc\').hide(); 
            }">';
            foreach(self::$nmTipo as $k => $v){
                $string .= '<option value="'.$k.'" '.($obj->get('tipo') == $k ? 'selected' : '').'>'.$v.'</option>';
            }
            $string .= '</select>';
            $string .= '<label for="" class="form-label">Tipo</label>';
            $string .= '</div>';
            $string .= '</div>';
            $string .= '<input name="status" type="hidden" class="form-control" value="3"/>';
        } else {
            $hcr = new Movimentacao();
            $conn = new Connection();
            $sql = "SELECT id_conta, SUM(juro) juro, SUM(multa) multa, SUM(desconto) desconto, SUM(valor) valor FROM {$GLOBALS['DBPREFIX']}{$hcr->getTableName()} WHERE tipo={$obj->get('tipo')} AND id_conta = {$obj->get('id_conta')} and status > 1 GROUP BY id_conta";
            $rs = $conn->prepareStatement($sql)->executeReader();
            if($rs->next()){
                $valor = $rs->getNumber('valor');
                $juro = $rs->getNumber('juro');
                $multa = $rs->getNumber('multa');
                $desconto = $rs->getNumber('desconto');
            }else{
                $valor = 0;
                $juro = 0;
                $multa = 0;
                $desconto = 0;
            }

            $juroC = $multaC = 0;
            $vencimento = $obj->getConta()->get('vencimento');
            $isLate = Utils::dateFormat($vencimento,'U') < mktime(0, 0, 0);
            if($isLate){
                $diff = mktime(0, 0, 0) - Utils::dateFormat($vencimento,'U');
                $days = floor($diff/(60*60*24));
                
                $multaAtraso = Utils::parseFloat($Config->get('multa_atraso'));
                $juroAtraso = Utils::parseFloat($Config->get('juros_atraso'));
                 
                $multaC = round((($obj->getConta()->get('valor')* $multaAtraso )/100),2); //2% de multa
                $juroC = round(($obj->getConta()->get('valor')+$multa) * (( $juroAtraso *$days)/100),2);
            }

            $string .= '<input name="tipo" type="hidden" class="form-control" value="' . $obj->get('tipo') . '"/>';
            $string .= '<input id="vlRestante" type="hidden" value="' . Utils::parseMoney( (float) $obj->getConta()->get('valor')-$valor) . '"/>';
            $string .= '<input id="juroRestante" type="hidden" value="' . Utils::parseMoney( (float) $obj->getConta()->get('valor')-$valor) . '"/>';
            $string .= '<input id="multaRestante" type="hidden" value="' . Utils::parseMoney((float) $obj->getConta()->get('valor')-$valor) . '"/>
            <script>
            function calculaLiquidacao(tipo){
                if(tipo==3){ 
                    $(`#valor_mov`).val(`'.Utils::parseMoney( (float)$obj->getConta()->get('valor')-$valor).'`); 
                    $(`#multa_mov`).val(`'.Utils::parseMoney($multaC).'`); 
                    $(`#juro_mov`).val(`'.Utils::parseMoney($juroC).'`); 
                    calculaMovimentacao(`_mov`); 
                }

            }
            '.((int) $obj->get('id') == 0 ? 'calculaLiquidacao('.$obj->get('status').');': '').'  
            </script>
            ';

            $string.= '

            <div class="col-sm-3 mb-3">
                <div class="form-floating">
                    <select name="status" class="form-select" id="status" onchange="calculaLiquidacao(this.value)">';
                        foreach(self::$nmStatus as $key=>$value) {
                            $string.='<option value="'.$key.'" '.($obj->get('status') == $key ? 'selected' : '').'>'.$value.'</option>';
                        }
                    $string.= '
                    </select>
                    <label for="" class="form-label">Situação</label>
                </div>
            </div>';
        }
        
        $string .= '<div class="col-sm-'.$size.'">';
        $string .= '<div class="form-floating">';
        $string .= '<input type="date" class="form-control" name="data" value="'.($obj->get('data') != '' ? Utils::dateFormat($obj->get('data'), 'Y-m-d') : $obj->get('data')).'" placeholder="">';
        $string .= '<label for="" class="form-label">Data</label>';
        $string .= '</div>';
        $string .= '</div>';
        
        
        $string .= '<div class="col-sm-3 mb-3">';
        $string .= '<div class="form-floating">';
        $string .= '<input name="valor" id="valor_mov" type="text" class="form-control money" maxlength="255" onkeyup="calculaMovimentacao(\'_mov\');"; value="'.Utils::parseMoney($obj->get('valor')).'" placeholder=""/>';
        $string .= '<label for="" class="form-label">Valor</label>';
        $string .= '</div>';
        $string .= '</div>';


            $string .= '<div class="col-sm-2 mb-3 '.($obj->get('tipo') == 3?'d-none':'').'">';
            $string .= '<div class="form-floating">';
            $string .= '<input name="juro" id="juro_mov" type="text" class="form-control money" maxlength="255" onkeyup="calculaMovimentacao(\'_mov\');"; value="'.Utils::parseMoney($obj->get('juro')).'" placeholder=""/>';
            $string .= '<label for="" class="form-label">Juro</label>';
            $string .= '</div>';
            $string .= '</div>';

            $string .= '<div class="col-sm-2 mb-3 '.($obj->get('tipo') == 3?'d-none':'').'">';
            $string .= '<div class="form-floating">';
            $string .= '<input name="multa" id="multa_mov" type="text" class="form-control money" maxlength="255" onkeyup="calculaMovimentacao(\'_mov\');"; value="'.Utils::parseMoney($obj->get('multa')).'" placeholder=""/>';
            $string .= '<label for="" class="form-label">Multa</label>';
            $string .= '</div>';
            $string .= '</div>';

            $string .= '<div class="col-sm-2 mb-3 '.($obj->get('tipo') == 3?'d-none':'').'">';
            $string .= '<div class="form-floating">';
            $string .= '<input name="desconto" id="desconto_mov" type="text" class="form-control money" onkeyup="calculaMovimentacao(\'_mov\');"; maxlength="255" value="'.Utils::parseMoney($obj->get('desconto')).'" placeholder=""/>';
            $string .= '<label for="" class="form-label">Desconto</label>';
            $string .= '</div>';
            $string .= '</div>';
            
            $string .= '<div class="col-sm-3 mb-3 '.($obj->get('tipo') == 3?'d-none':'').'">';
            $string .= '<div class="form-floating">';
            $string .= '<input id="total_mov" type="text" class="form-control money" disabled maxlength="255" value="'.Utils::parseMoney($obj->get('valor')+$obj->get('juro')+$obj->get('multa')-$obj->get('desconto')).'" placeholder=""/>';
            $string .= '<label for="" class="form-label">Total Pago</label>';
            $string .= '</div>';
            $string .= '</div>';
        
            $string .= '<div class="col-sm-3 mb-3 '.($obj->get('tipo') != 3?'d-none':'').'">';
            $string .= '<div class="form-floating">';
            $string .= '<input name="despesa" type="text" class="form-control money" maxlength="255" placeholder=""/>';
            $string .= '<label for="" class="form-label">Despesa</label>';
            $string .= '</div>';
            $string .= '</div>';    

        $string .= '<div class="col-sm-12"><div class="form-floating">';
        $string .= '<textarea id="obs" class="form-control '.($obj->get('id_conta') == 0 ? 'required':'').'" name="obs" height="80">' . $obj->get('obs') . '</textarea>';
        $string .= '<label for="" class="form-label">'.($obj->get('id_conta') == 0 ? 'Descri&ccedil;&atilde;o':'Observa&ccedil;&atilde;o').'</label>';
        $string .= '</div></div></div>';

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <div class="table-responsive">
            <table class="table table-hover">
            <thead>
              <tr>
                <th class="col-sm-2">Data</th>
                <th class="col-sm-2">Tipo</th>
                <th class="col-sm-6">Descrição</th>
                <th class="col-sm-2">Valor (R$)</th>
              </tr>
            </thead>
            <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr class="position-relative" id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table></div>';
        
        return $string;
    }

    public static function getLine($obj){
        return '
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), Utils::dateFormat($obj->get('data'), 'd/m/Y'), false).'</td>
        <td '.($obj->get('tipo') == 1 ? 'class="text-danger"' : '').'>'.self::$nmTipo[$obj->get('tipo')].'</td>
        <td>'.Utils::subText($obj->get('obs'), 100).'</td>
        <td>'.Utils::parseMoney($obj->get('valor')).'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if( (int) $request->query('id_conta') > 0) {
            $paramAdd .= " AND id_conta = ". (int)$request->query('id_conta');
        }
        
        if( (int) $request->query('id_projeto') > 0) {
            $paramAdd .= " AND (id_projeto = " . (int) $request->query('id_projeto');
            if($request->query('todas')) {
                $paramAdd .= " OR id_conta IN (SELECT id FROM contas WHERE id_projeto =  " .(int) $request->query('id_projeto') .")";
            }
            $paramAdd .= ')';
        }
        
        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        $string = ''; 
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="inicio" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('inicio').'"/>
                <label for="" class="form-label">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="fim" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('fim').'"/>
                <label for="" class="form-label">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-3">    
            <div class="form-floating">
                <select class="form-select" name="order">';
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
                <label for="" class="form-label">Ordem</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3">    
            <div class="form-floating">
                <select class="form-select" name="offset">';
                foreach($GLOBALS['QtdRegistros'] as $key){
                    $string .= '<option value="'.$key.'"'.($request->query('offset') == $key ? ' selected':'').'>'.$key.' registros</option>';
                }
        $string .= '
                </select>
                <label for="" class="form-label">Registros</label>
            </div>
        </div>';

        return $string;
    }

}