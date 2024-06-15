<?php

class Conta extends Flex {

    protected $tableName = 'contas';
    protected $mapper = array(
        'id' => 'int',
        'id_projeto' => 'int',
        'id_categoria' => 'int',
        'numero_nf' => 'string',
        'tipo' => 'int',
        'status' => 'int',
        'vencimento' => 'date',
        'pagamento' => 'date',
        'valor' => 'number',
        'descricao' => 'string',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');

    public $modalSize = 'modal-xl';

    public static $nmTipo = array(
        1 => 'Pagar',
        2 => 'Receber',
    );
    public static $nmStatus = array(
        0 => 'Cancelado',
        1 => 'Aberto',
        2 => 'Liquidado parcial',
        3 => 'Liquidado',
        4 => 'Negociada',
    );
    

    public static $configGG = array(
        'nome' => 'Contas',
        'class' => __CLASS__,
        'ordenacao' => 'vencimento',
        'envia-arquivo' => false,
        'show-menu' => false,
        'icon' => 'ti ti-calculator'
    );

    public function getValor() {
        return $this->params['valor'];
    }

    protected $projeto = null;
    public function getProjeto() {
        if(!$this->projeto || $this->projeto->get('id') != $this->get('id_projeto')){
            if($this->get('id_projeto')>0){
                $this->projeto = Projeto::load($this->get('id_projeto'));
            }else{
                $this->projeto = new Projeto();
            }
        }
        return $this->projeto;        
    }
    
    protected $categoria = null;
    public function getCategoria() {
        if(!$this->categoria || $this->categoria->get('id') != $this->get('id_categoria')){
            if($this->get('id_categoria')>0){
                $this->categoria = Categoria::load($this->get('id_categoria'));
            }else{
                $this->categoria = new Categoria();
            }
        }
        return $this->categoria;        
    }

    public function getStatusConta(){
        $status_conta = '';

        if($this->get('vencimento') <= date('Y-m-d')){
            $text = 'text-danger';
            $title = 'Conta em atraso';
            if($this->get('vencimento') == date('Y-m-d')){
                $text = 'text-warning';
                $title = 'Esta conta vence hoje';
            }

            $status_conta = '<i class="ms-2 ti ti-alert-triangle '.$text.'" data-toggle="tooltip" data-bs-placement="top" title="'.$title.'"></i>';
        }
        return $status_conta;
    }

    public function getValorPago(){
        $valor_total = 0;
       
        $rs = Movimentacao::search([
            's' => 'id',
            'w' => "id_conta={$this->get('id')} AND tipo = {$this->get('tipo')}",
        ]);

        while($rs->next()){
            $objMov = Movimentacao::load($rs->getInt('id'));
            $valor_total += $objMov->get('valor') + $objMov->get('juro') + $objMov->get('multa') - $objMov->get('desconto');
        }

        return $valor_total;
    }

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `contas`;
        CREATE TABLE `contas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_projeto` int(11) NOT NULL,
            `id_categoria` int(11) NOT NULL DEFAULT 0,
            `descricao` varchar(255) NULL,
            `numero_nf` varchar(50) DEFAULT NULL,
            `tipo` int(1) NOT NULL,
            `status` int(1) NOT NULL,
            `vencimento` date NOT NULL,
            `pagamento` date DEFAULT NULL,
            `valor` float(11,2) NOT NULL DEFAULT 0.00,
            `ref` varchar(6) DEFAULT NULL,
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
       
        
        if(!isset($_POST['vencimento']) || $_POST['vencimento'] == ''){
            $error .= '<li>O campo "Vencimento" n&atilde;o foi informado</li>';
        }
        if(!isset($_POST['valor']) || $_POST['valor'] == ''){
            $error .= '<li>O campo "Valor" n&atilde;o foi informado</li>';
        }
        if(!isset($_POST['descricao']) || $_POST['descricao'] == ''){
            $error .= '<li>O campo "Descrição" n&atilde;o foi informado</li>';
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
            $obj = new $classe();
            
            if ($id > 0) {
                $obj = self::load($id);
            }else{
                $obj->set('status', 1);
            }

            $obj->set('id_projeto', (int) $_POST["id_projeto"]);
            $obj->set('numero_nf', $_POST["numero_nf"]);
            $obj->set('tipo', (int) $_POST["tipo"]);
            $obj->set('id_categoria', (int) $_POST['id_categoria_2']);
            if($obj->get('tipo') == 1) $obj->set('id_categoria', (int) $_POST['id_categoria_1']);
      
            if(isset($_POST['parcelar']) && $_POST['parcelar'] == 1 && $_POST['parcelas'] > 1){
                for($i=0; $i<count($_POST['pvenc']); $i++){
                    $parcelaObj = new $classe();

                    $parcelaObj->set('id_projeto', (int) $_POST["id_projeto"]);
                    $parcelaObj->set('numero_nf', $_POST["numero_nf"]);
                    $parcelaObj->set('status', 1);
                    $parcelaObj->set('tipo', (int) $_POST["tipo"]);
                    $parcelaObj->set('id_categoria', $obj->get('id_categoria'));
                    $parcelaObj->set('vencimento', Utils::dateFormat($_POST["pvenc"][$i],'Y-m-d'));
                    $parcelaObj->set('valor', (float) str_replace(',','.',str_replace('.','',$_POST["pvalor"][$i])));
                    $parcelaObj->set('descricao', $_POST["pdesc"][$i]);
                    $parcelaObj->save();
                }
            } else {
                $obj->set('vencimento', Utils::dateFormat($_POST["vencimento"],'Y-m-d'));
                $obj->set('valor', (float) str_replace(',','.',str_replace('.','',$_POST["valor"])));
                $obj->set('descricao', $_POST["descricao"]);
                $obj->save();
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
        global $request;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();

        $obj->set('id', $codigo);
        $obj->set('status',1);
        $obj->set('valor',0);
        $obj->set('tipo', $request->getInt('tipo') == 0 ? 1 : $request->getInt('tipo'));

        if($obj->get('id_projeto') == '') {
            $obj->set(('id_projeto'), $request->get('id_projeto'));
        }

        if ($codigo > 0) {
            $obj = self::load($codigo);
        }
       
        $string .= '
        <div class="row g-2 align-items-center">
            <input type="hidden" name="id_projeto" value="'.$obj->get('id_projeto').'">

            <div class="col-sm-6 mb-3">
                <div class="form-floating">
                    <input type="text" name="projeto" id="projeto" placeholder="Seu dado aqui" class="form-control" readonly value="'.$obj->getProjeto()->get('nome').'">
                    <label>Projeto</label>
                </div>
            </div>
            <div class="col-sm-6 mb-3">
                <div class="form-floating">
                    <input type="text" name="projeto" id="projeto" placeholder="Seu dado aqui" class="form-control" readonly value="'.$obj->getProjeto()->getCliente()->get('nomefantasia').'">
                    <label>Cliente</label>
                </div>
            </div>
            <div class="col-sm-4 col-xl-2 mb-3">
                <div class="form-floating">
                    <select class="form-select" name="tipo" id="tipo" onchange="mudarCategoria(this.value)">';
                    foreach(self::$nmTipo as $key=>$value){
                        $string .= '<option value="'.$key.'" '.($obj->get('tipo') == $key? "selected" : "").'>'.$value.'</option>';
                    }
                    $string .= ' 
                    </select>
                    <label for="">Tipo</label>
                </div>
            </div>';

            $string .= '
            <script>
                function  mudarCategoria(tipo){
                    if(tipo == 1){
                        $(`#saida`).removeClass(`d-none`);
                        $(`#entrada`).addClass(`d-none`);
                    }else if (tipo == 2){
                        $(`#entrada`).removeClass(`d-none`);
                        $(`#saida`).addClass(`d-none`);
                    }
                }
            </script>
            ';

        
            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3 '.($obj->get('tipo') == 2 ? 'd-none' :'').'" id="saida">
                <div class="form-floating">
                    <select class="form-select" name="id_categoria_1" >';
                    $rs = Categoria::search([
                        's' => 'id, nome',
                        'w' => 'tipo=1',
                        'o' => 'nome'
                    ]);
                    while($rs->next()){
                        $string .= '<option value="'.$rs->getInt('id').'" '.($rs->getInt('id') == $obj->get('id_categoria') ? 'selected' : '').'>'.$rs->getString('nome').'</option>';
                    }
                    $string .= '
                    </select>
                    <label for="">Categoria</label>
                </div>
            </div>';
        
            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3 '.($obj->get('tipo') == 1 ? 'd-none' :'').'" id="entrada">
                <div class="form-floating">
                    <select class="form-select" name="id_categoria_2" >';
                    $rs = Categoria::search([
                        's' => 'id, nome',
                        'w' => 'tipo=2',
                        'o' => 'nome'
                    ]);
                    while($rs->next()){
                        $string .= '<option value="'.$rs->getInt('id').'" '.($rs->getInt('id') == $obj->get('id_categoria') ? 'selected' : '').'>'.$rs->getString('nome').'</option>';
                    }
                    $string .= '
                    </select>
                    <label for="">Categoria</label>
                </div>
            </div>';
        
            
            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3 required">
                <div class="form-floating">
                    <input name="valor" id="valor_original" required type="text" class="form-control money" maxlength="255" value="'.Utils::parseMoney($obj->get('valor')).'"/ placeholder="">
                    <label for="">Valor Original</label>
                </div>
            </div>';

            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3 required">
                <div class="form-floating">
                    <input type="date" class="form-control" required name="vencimento" id="venc" value="'.($obj->get('vencimento') != '' ? Utils::dateFormat($obj->get('vencimento'), 'Y-m-d') : $obj->get('vencimento')).'">
                    <label for="">Vencimento</label>
                </div>
            </div>';

            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control date" disabled name="vencimento" value="'.($obj->get('pagamento') != '' ? Utils::dateFormat($obj->get('pagamento'), 'Y-m-d') : $obj->get('pagamento')).'">
                    <label for="">Pagamento</label>
                </div>
            </div>';

            $string .= '
            <div class="col-sm-4 col-xl-2 mb-3 required">
                <div class="form-floating">
                    <select class="form-select" name="status" disabled id="status">';
                    foreach(self::$nmStatus as $key=>$value){
                        $string .= '<option value="'.$key.'" '.($obj->get('status') == $key? 'selected' : '').'>'.$value.'</option>';
                    } 
            $string .= '
                    </select>
                    <label for="">Situa&ccedil;&atilde;o</label>
                </div>
            </div>';

            $string .= '<div class="col-sm-4 mb-3 col-xl-2">';
            $string .= '<div class="form-floating">';
            $string .= '<input name="numero_nf" type="text" class="form-control" maxlength="255" value="' . $obj->get('numero_nf') . '" placeholder=""/>';
            $string .= '<label for="">Nr. NF</label>';
            $string .= '</div>';
            $string .= '</div>';

            $string .= '<div class="col-sm-12 mb-3 required">';
            $string .= '<div class="form-floating">';
            $string .= '<input id="descricao" required name="descricao" type="text" class="form-control" value="' . $obj->get('descricao') . '" placeholder=""/>';
            $string .= '<label for="">Descrição</label>';
            $string .= '</div>';
            $string .= '</div>';
                    
            $string.= '
            <style>[class*="col"]:has(.parc[disabled]) { display:none }</style>
            <script>
                function toggleInputs(element) {
                    var isChecked = element.checked;
                    $(`.parc`).prop(`disabled`, !isChecked);
                }
            </script>';
            
            
            if(!$codigo){
                $string.='
                <div class="col-sm-4">
                    <div class="p-3 rounded-2 border border-opacity-10 position-relative">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="parcelar" value="1" onclick="toggleInputs(this)" name="parcelar">
                        <label class="form-check-label stretched-link" for="parcelar">Gerar parcelamento dessa conta</label>
                    </div>
                    </div>
                </div>';

                $string .= '<div class="col-sm-2">';
                $string .= '<div class="form-floating">';
                $string .= '<input name="parcelas" id="parcelas" type="text" class="form-control onlynumbers parc" disabled maxlength="3" value="2" placeholder=""/>';
                $string .= '<label for="">Parcelas</label>';
                $string .= '</div>';
                $string .= '</div>';

                $string .= '<div class="col-sm-2">';
                $string .= '<div class="form-floating">';
                $string .= '<select class="form-select parc" disabled id="tipo_parcela">';
                $string .= '<option value="1">Dividir</option>';
                $string .= '<option value="2">Repetir</option>';
                $string .= '</select>';
                $string .= '<label for="">Valor</label>';
                $string .= '</div>';
                $string .= '</div>';

                $string.= '
                <div class="col-sm-2">
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control money parc" name="juroparcela" id="juroparcela" disabled maxlength="255" placeholder="Juro a.m" value="0,00" />
                        <label for="juroparcela">Juros a.m (%)</label>
                    </div>
                </div>';

                $string .= '<div class="col-sm-2">';
                $string .= '<button class="btn btn-outline-primary parc w-100" disabled type="button" onclick="geraParcelas(); return false;">Gerar</button>';
                $string .= '</div>';

                $string .= '<div class="col-sm-12 mt-5" id="div-parcelas"></div>';
                $string .= '
                <script>
                function geraParcelas(){
                    var parcelas = parseInt($(\'#parcelas\').val());
                    var juros = parseFloat($(\'#juroparcela\').val().replace(".","").replace(",","."));
                    var valor = parseFloat($(\'#valor_original\').val().replace(".","").replace(",","."));
                    var venc = $(\'#venc\').val();

                    if(valor == 0) showErrorAlert("Erro", "&Eacute; necess&aacute;rio digitar um valor.");
                    else if(venc == "") showErrorAlert("Erro", "&Eacute; necess&aacute;rio digitar o vencimento.");
                    else if(parcelas <= 1) showErrorAlert("Erro", "&Eacute; necess&aacute;rio que a quantidade de parcelas seja maior que 1.");
                    else {
                        var pts = venc.split(\'-\');
                        var baseData = new Date();
                        baseData.setFullYear(pts[0], pts[1]-1, pts[2]);
                        var str=\'\';
                        if($(\'#tipo_parcela\').val() == 1){
                            if(juros > 0)
                                var vl_parcela = (valor + (valor * (juros/100) * parcelas ))/parcelas;
                            else
                                var vl_parcela = valor/parcelas;
                        }else{
                            var vl_parcela = valor;
                        }
                        vl_parcela = Math.round(vl_parcela*100)/100;
                        var descricao = $(\'#descricao\').val();
                        var data = baseData;

                            
                        str += \' \
                        <div class="d-flex flex-column gap-2 bg-dark p-4 rounded-3 bg-opacity-10">\
                        <h3 class="h5">Demonstrativo de parcelamento</h3>\
                        \';
                        
                        for(var i=0; i<parcelas; i++){
                            //var fdata = (\'0\'+data.getDate()).substr(-2)+\'/\'+(\'0\'+(data.getMonth()+1)).substr(-2)+\'/\'+data.getFullYear();
                            var fdata = data.getFullYear()+\'-\'+(\'0\'+(data.getMonth()+1)).substr(-2)+\'-\'+(\'0\'+data.getDate()).substr(-2);
                            if(i+1 == parcelas && vl_parcela*parcelas < valor){
                                vl_parcela += valor-(vl_parcela*parcelas);
                            }
                            str += \' \
                            <div class="row g-2 g-lg-3">\
                                <div class="col-sm-3">\
                                    <div class="form-floating">\
                                        <input type="date" class="form-control" name="pvenc[]" value="\'+fdata+\'" placeholder="">\
                                        <label for="">Vencimento \'+(i+1)+\'</label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-3">\
                                    <div class="form-floating">\
                                        <input name="pvalor[]" type="text" class="form-control money" maxlength="255" value="\'+number_format(vl_parcela,2,",",".")+\'"/>\
                                        <label for="">Valor \'+(i+1)+\'</label>\
                                    </div>\
                                </div>\
                                <div class="col-sm-6">\
                                    <div class="form-floating">\
                                        <input type="text" class="form-control" name="pdesc[]" value="\'+descricao+\' - Parcela \'+(i+1)+\'/\'+parcelas+\'">\
                                        <label for="">Descri&ccedil;&atilde;o \'+(i+1)+\'</label>\
                                    </div>\
                                </div>\
                            </div>\
                                \';
                                //data.setMonth(data.getMonth()+1);
                                data = new Date(baseData.getFullYear(), baseData.getMonth(), baseData.getDate(), 0, 0, 0, 0);
                                data.addMonths(i+1);
                        }
                        
                    str += \' \
                    </div>\
                    \';
                        $(\'#div-parcelas\').html(str);
                        fieldFunctions();

                    }
                }
                </script>';
            }

            if($codigo > 0){
                $string .= '
                <div class="col-sm-12" id="movimentacoes">';
                $string .= '
                    <a class="btn btn-secondary text-white" onclick="javascript: modalForm(`movimentacoes`,0,`/id_conta/'.$codigo.'/tipo/'.$obj->get('tipo').'`,reloadConta)">
                    <i class="ti ti-plus"></i>
                        Adicionar movimenta&ccedil;&atilde;o
                    </a>';
                $string.= '
                    <script>
                        function loadMovimentacoes(){ 
                            tableList(`movimentacoes`, `id_conta='.$codigo.'&offset=10`, `txtMovimentacoes`, false);
                        } 
                        loadMovimentacoes();

                        function reloadConta(){
                            $.get({
                                url: `${__PATH__}ajax/edit/class/contas/id/'.$codigo.'`,
                                dataType: `json`,
                                success: (resp) => {
                                    if (!resp.success) {
                                        MessageBox.error(resp.message);
                                        return false;
                                    }
                                    
                                    if($(`#movimentacoes`).closest(`.modal-body`).length > 0){
                                        $(`#movimentacoes`).closest(`.modal-body`).html(resp.html);
                                        getLine(`contas`, '.$codigo.');
                                    }
                                    fieldFunctions();
                                },
                                error: errorFunction,
                            });
                            
                        }   
                    </script>';

                $string .= '<div class="form-group col-sm-12 mt-3" id="txtMovimentacoes">'.GG::moduleLoadData('loadMovimentacoes();').'</div>';
            }
        $string.= '</div>';
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <div class="table-responsive">
            <table class="table  table-hover">
            <thead>
              <tr>
                <th></th>
                <th class="col-sm-2">Tipo</th>
                <th class="col-sm-6">Descrição</th>
                <th class="col-sm-2">Vencimento</th>
                <th class="text-end col-sm-2">Valor</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr class="position-relative '.(self::$nmStatus[$obj->get('status')] == "Cancelado"? "text-opacity-50" :"").'" id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table></div>';
        
        return $string;
    }

    public static function getLine($obj){
        $string = '';

        $string .= '<td class="position-relative"><div class="d-flex gap-2 align-items-center lh-1">';

        $string .= '</div></td>';
        $string .= '<td class="link-edit '.(self::$nmTipo[$obj->get('tipo')]=="Receber"?'':'text-black-50').'">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), self::$nmTipo[$obj->get('tipo')], false).'</td>';
        $string .= '
        <td class="small">
            <span class="small">'.$obj->getCategoria()->get('nome').'</span><br>
            '.Utils::subText($obj->get('descricao'), 100).'
        </td>
        <td class="text-black-50">'.Utils::dateFormat($obj->get('vencimento'), 'd/m/Y').'</td>
        <td class="text-end text-nowrap">R$ '.Utils::parseMoney($obj->get('valor')).'</td>
        <td><span class="badge text-black '.self::$nmStatus[$obj->get('status')].'">'.self::$nmStatus[$obj->get('status')].'</span></td>
        <!--'.GG::getResponsiveList(['Nome' => self::$nmTipo[$obj->get('tipo')]], $obj).'-->
        ';

        return $string;
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['descricao'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }
        
        if((int) $request->query('id_projeto') > 0) {
            $paramAdd.= " AND id_projeto = {$request->query('id_projeto')}";
        }

        if((int) $request->query('tipo') > 0) {
            $paramAdd.= " AND tipo = {$request->query('tipo')}";
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
        <div class="col-sm-12 mb-3">
            <div class="form-floating">
                <input name="nome" type="text" placeholder="seu dado aqui" class="form-control" value="'.$request->query('nome').'"/>
                <label for="">Nome</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="inicio" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('inicio').'"/>
                <label for="">Cadastrados desde</label>
            </div>
        </div>';
        
        $string .= '
        <div class="col-sm-3  mb-3">
            <div class="form-floating">
                <input name="fim" type="text" placeholder="seu dado aqui" class="form-control date" value="'.$request->query('fim').'"/>
                <label for="">Cadastrados at&eacute;</label>
            </div>
        </div>';

        $string .= '
        <div class="col-sm-3">    
            <div class="form-floating">
                <select class="form-select" name="order">';
                foreach([
                    'descricao' => 'A-Z',
                    'descricao desc' => 'Z-A',
                    'vencimento' => 'Próximas a vencer',
                    'vencimento desc' => 'Últimas a vencer',
                    'id' => 'Mais antigo primeiro',
                    'id desc' => 'Mais recente primeiro',
                ] as $key => $value){
                    $string .= '<option value="'.$key.'"'.($request->query('order') == $key ? ' selected':'').'>'.$value.'</option>';
                }
        $string .= '
                </select>
                <label for="">Ordem</label>
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
                <label for="">Registros</label>
            </div>
        </div>';

        return $string;
    }

    public function totalRecebido($field=''){
        $mov = new Movimentacao();
        $conn = new Connection();
        $sql = "SELECT id_conta, SUM(juro) juro, SUM(multa) multa, SUM(desconto) desconto, SUM(valor) valor FROM {$GLOBALS['DBPREFIX']}{$mov->getTableName()} WHERE tipo={$this->get('tipo')} AND id_conta = {$this->get('id')} and status > 1 GROUP BY id_conta";
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

        switch($field){
            case 'juro' : case 'juros' :
                return $juro;
                break;
            case 'multa' : case 'multas' :
                return $multa;
                break;
            case 'desconto' : case 'descontos' :
                return $desconto;
                break;
            case 'tudo' :
                return $valor+$juro+$multa-$desconto;
                break;
            default : 
                return $valor;
        }
    }


    // public static function printContas($contas, $title='Relatório de Contas'){
    //     $headerFooter = Utils::defaultHeaderFooterPDF('
	// 	<div style="font-size:24px;text-align: right;margin-top:10px">'.$title.'</div>
	// 	');

    //     $string = '
    //     <style>
    //         * {
    //             margin: 0px;
    //             padding: 0px;
    //             font-size: 11px;
    //         }

    //         table.list {
    //             border-spacing: 0px;
    //             border-collapse: collapse;
    //         }

    //         th, td {
    //             border: 1px solid #333;
    //             padding: 3px;
    //             margin:0px;
    //         }

    //         tr.head {
    //             color: #000; 
    //             background-color:#ccc
    //         }

    //         .text-right { text-align: right; }
    //         .text-center { text-align: center; }
    //         .text-left { text-align: left; }

    //     </style>
    //     <page backtop="25mm" backbottom="15mm" pagegroup="new">'.$headerFooter.'

    //     ';
          
    //     $i=0;
    //     $total = $totalGeral = array(0,0);
    //     $mes = '00000';
    //     if(count($contas) > 0){
    //         $string .= '<table class="list">';
        
    //         foreach($contas as $obj){ 

    //             if(Utils::dateFormat($obj->get('vencimento'),'mY') != $mes){
    //                 $mes = Utils::dateFormat($obj->get('vencimento'),'mY');
    //                 if($total[0] > 0){
    //                     $string .= '
    //                     <tr>
    //                         <td colspan="2">Subtotal:</td>
    //                         <td class="text-right text-primary"><strong>R$ '.Utils::parseMoney($total[0]).'</strong></td>
    //                         <td class="text-right text-success"><strong>R$ '.Utils::parseMoney($total[1]).'</strong></td>
    //                         <td class="text-right text-success"><strong>R$ '.Utils::parseMoney($total[0]-$total[1]).'</strong></td>
    //                     </tr>
    //                     ';
    //                 }
    //                 $string .= '
    //                 <tr class="head">
    //                     <th colspan="5" class="text-center"><strong>'.Utils::dateFormat($obj->get('vencimento'),'m/Y').'</strong></th>
    //                 </tr>
    //                 <tr class="head">
    //                     <th style="width: 80px; text-align: center">Vencimento</th>
    //                     <th style="width: 300px">Descri&ccedil;&atilde;o</th>
    //                     <th style="width: 90px; text-align: right">Valor</th>
    //                     <th style="width: 90px; text-align: right">Pago</th>
    //                     <th style="width: 90px; text-align: right">Restante</th>
    //                 </tr>';
    //                 $total = array(0,0);
    //             }

    //             $i++;
    //             $atraso = (Utils::dateFormat($obj->get('vencimento'),'U') < mktime(0,0,1));
    //             $recebido = ($obj instanceof Projecao ? 0 : $obj->totalRecebido());
    //             $total[0] += $obj->get('valor');
    //             $total[1] += $recebido;
    //             $totalGeral[0] += $obj->get('valor');
    //             $totalGeral[1] += $recebido;

    //             $string .= '
    //             <tr> 
    //                 <td class="text-center">'.Utils::dateFormat($obj->get('vencimento'),'d/m/Y').'</td>
    //                 <td style="width: 300px">
    //                 '.str_pad($obj->get('id'),7,'0',0).' - '.$obj->getCliente()->get('nomefantasia').'
    //                 <br><span class="small">'.$obj->get('descricao').'</span>
    //                 </td>
    //                 <td class="text-right">R$ '.Utils::parseMoney($obj->get('valor')).'</td>
    //                 <td class="text-right">R$ '.Utils::parseMoney($recebido).'</td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($obj->get('valor')-$recebido).'</strong></td>
    //             </tr>';
                  
    //         } 

    //         $string .=  '
    //             <tr>
    //                 <td colspan="2" class="text-right">Subtotal:</td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($total[0]).'</strong></td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($total[1]).'</strong></td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($total[0]-$total[1]).'</strong></td>
    //             </tr>
    //             <tr>
    //                 <td colspan="2" class="text-right">Total Geral:</td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($totalGeral[0]).'</strong></td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($totalGeral[1]).'</strong></td>
    //                 <td class="text-right"><strong>R$ '.Utils::parseMoney($totalGeral[0]-$totalGeral[1]).'</strong></td>
    //             </tr>
    //         </table>';
    //     }

    //     $string .= '</page>';

    //     return $string;
    // }

}