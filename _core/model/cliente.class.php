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
        if(!Pessoa::exists($this->id_pessoa)){
            return new Pessoa();
        }
        return Pessoa::load($this->id_pessoa);
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

        Flex::dbDelete(new Pessoa(), "id IN(SELECT id_pessoa FROM {$obj->tableName} WHERE id IN({$ids}))");
        return Flex::dbDelete($obj, "id IN({$ids})");
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
            <div class="form-group">
                <label for="">Observa&ccedil;&atilde;o</label>
                <textarea class="form-control ckeditor" name="obs" id="obs'.$obj->getTableName().'">'.$obj->get('obs').'</textarea>
            </div>
        </div>';
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-4">Nome</th>
                    <th class="col-sm-3">Telefone</th>
                    <th class="col-sm-5">Obs</th>
                    <th width="10"></th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->getPessoa()->get('nome')).'</td>
        '.GG::getResponsiveList([
            'Nome' => $obj->getPessoa()->get('nome'),
            'Telefone' => $obj->getPessoa()->get('telefone1'),
            'Obs' => $obj->get('obs') != '' ?  $obj->get('obs') : '-', 
        ], $obj).'
        <td>'.($obj->getPessoa()->get('telefone1') != '' ? $obj->getPessoa()->get('telefone1') : '-').'</td>
        <td>'.($obj->get('obs') != '' ?  Utils::subText($obj->get('obs'), 200) : '-').'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';

        foreach(['nome', 'cpf', 'cep', 'rua', 'bairro', 'cidade', 'estado'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND id IN (SELECT id FROM pessoas WHERE `$key` LIKE '%{$request->query($key)}%')";
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