<?php

class DocumentoPedido extends Flex {

    protected $tableName = 'documentospedido';
    protected $mapper = array(
        'id_documento' => 'int',
        'id_pedido' => 'int',
    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Documentos do Pedido',
        'class' => __CLASS__,
        'ordenacao' => 'id_documento ASC',
        'envia-arquivo' => true,
        'show-menu' => true,
        'icon' => 'ti ti-file',
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `documentospedido`;
        CREATE TABLE `documentospedido` (
            `id_documento` int(11) NOT NULL,
            `id_pedido` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
        ";
    }

    public static function getDocsPedido($id_pedido = 0){
        return self::search([
            's' => 'id_documento',
            'w' => 'id_pedido='.$id_pedido,
        ]);
    }

    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

        if(!isset($_POST['id_documento']) || $_POST['id_documento'] == ''){
            $error .= '<li>O campo "Documento" n&atilde;o foi informado</li>';
        }
        
        if(!isset($_POST['id_documento']) || $_POST['id_documento'] == ''){
            $error .= '<li>O campo "Pedido" n&atilde;o foi informado</li>';
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

            $obj->set('id_documento', $_POST['id_documento']);
            $obj->set('id_pedido', $_POST['id_pedido']);

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

        $conn = new Connection();
        $sql = "DELETE FROM {$obj->tableName} WHERE id_pedido = {$ids}";
        return $conn->prepareStatement($sql)->executeQuery();
    }

    public static function form($codigo = 0){
        global $request, $objSession;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);
        
        return $string;
    }

    public static function getTable($rs) {
        return '';
    }

    public static function getLine($obj){
        return '';
    }

    public static function filter($request) {
        $paramAdd = '1=1';

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        
        $string = '';

        return $string;
    }
}