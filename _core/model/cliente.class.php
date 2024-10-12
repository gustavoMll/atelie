<?php

class Cliente extends Flex {

    protected $tableName = 'clientes';
    protected $mapper = array(
        'id' => 'int',
        'id_pessoa' => 'int',
        'obs' => 'string',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Clientes',
        'class' => __CLASS__,
        'ordenacao' => 'id ASC',
        'envia-arquivo' => false,
        'show-menu' => true,
        'icon' => 'ti ti-users'
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `clientes`;
        CREATE TABLE `clientes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_pessoa` int(11) NOT NULL,
            `obs` TEXT,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`),
            FOREIGN KEY (id_pessoa) REFERENCES pessoas(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
        ";
    }

    protected $pessoa = null;
    public function getPessoa()
    {
        if (!$this->pessoa || $this->pessoa->get('id') != $this->get('id_pessoa')) {
            if (Pessoa::exists((int) $this->get('id_pessoa'), 'id')) {
                $this->pessoa = Pessoa::load($this->get('id_pessoa'));
            } else {
                $this->pessoa = new Pessoa();
            }
        }
        return $this->pessoa;
    }

    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

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
        $objPessoa = new Pessoa();
        if(self::validate() && $objPessoa::validate()){
            $id = $request->getInt('id');
            $obj = new $classe(array($id));
            if ($id > 0) {
                $obj = self::load($id);
                $objPessoa = Pessoa::load($obj->get('id_pessoa'));
            }

            $ret = $objPessoa::saveForm(); 
            $id_pessoa = $ret['obj']->get('id');
            if($id_pessoa){
                $obj->set('id_pessoa', $id_pessoa);
                $obj->set('obs', $_POST['obs']);
                $obj->save();

                echo 'Registro salvo com sucesso!';

                $ret['success'] = true;
                $ret['obj'] = $obj;   
            }
        }

        return $ret;
    }

    public static function delete($ids) {
        global $defaultPath;
        $classe = __CLASS__;
        $obj = new $classe();
        $ret = 0;

        if(!Aluguel::exists("id_cliente IN ({$ids})")){
            Flex::dbDelete(new Pessoa(), "id IN(SELECT id_pessoa FROM {$obj->tableName} WHERE id IN({$ids}))");
            return Flex::dbDelete($obj, "id IN({$ids})");
        }

        return $ret;
    }

    public static function form($codigo = 0){
        global $request, $objSession;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);
        $id_pessoa = 0;
        
        if ($codigo > 0) {
            $obj = self::load($codigo);
            $id_pessoa = $obj->get('id_pessoa');
        }else{
            $codigo = time();
            $string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }
        
        $string = '<input name="id_pessoa" type="hidden" value="'.$id_pessoa.'"/>';
        $string .= Pessoa::form($id_pessoa);
        $string .= '
        <div class="col-sm-12 my-3">
            <div class="d-flex justify-content-start gap-2">
                <label for="">Observa&ccedil;&atilde;o</label>
                <a class="d-flex" style="align-items: center;" id="btn_mostrar">
                    <i class="ti ti-eye-off" style="cursor: pointer" onclick="mduarVisibilidade()" data-toggle="tooltip" data-bs-placement="top" title="Mostrar"></i>
                </a>
            </div>
            <div class="form-group" style="display: none" id="div_obs">
                <textarea class="form-control ckeditor" name="obs" id="obs'.$obj->getTableName().'">'.$obj->get('obs').'</textarea>
            </div>
        </div>';

        $string .= '
        <script>
            function mduarVisibilidade(){
                var icon = $("#btn_mostrar i");
                icon.toggleClass("ti-eye-off ti-eye");

                if(icon.hasClass("ti-eye")){
                    icon.attr("title", "Esconder");
                    $(`#div_obs`).fadeIn(`slow`)
                } else {
                    icon.attr("title", "Mostrar");
                    $(`#div_obs`).fadeOut(`slow`)
                }
                
            }
        </script>
        ';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <div class="table-responsive px-xs-0"><table class="table table-hover">
            <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-4">Nome</th>
                    <th class="col-sm-3">Telefone</th>
                    <th class="col-sm-5">Endere&ccedil;o</th>
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
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getPessoa()->get('nome')).($obj->get('obs') != '' ? '*' : '').'</td>
        <!--'.GG::getResponsiveList([
            'Nome' => $obj->getPessoa()->get('nome'),
            'Telefone' => $obj->getPessoa()->get('telefone1'),
            'Endere&ccedil;o' => $obj->getPessoa()->getAddress(), 
        ], $obj).'-->
        <td>'.($obj->getPessoa()->get('telefone1') != '' ? Utils::mask($obj->getPessoa()->get('telefone1'), "(##) #####-####"): '-').'</td>
        <td>'.$obj->getPessoa()->getAddress().'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';

        foreach(['nome', 'cpf', 'cep', 'endereco', 'bairro', 'cidade', 'estado'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND id_pessoa IN (SELECT id FROM pessoas WHERE `$key` LIKE '%{$request->query($key)}%')";
            }
        }

        if($request->query('telefone') != ''){
            $paramAdd .= " AND id IN (SELECT id FROM pessoas WHERE `telefone1` LIKE '%{$request->query('telefone')}%' OR `telefone2` LIKE '%{$request->query('telefone')}%')";
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

        $string .= Pessoa::searchForm($request);
        
        return $string;
    }
}