<?php

class Flex {

    protected $params = array();
    protected $mapper = array();
    protected $persisted = false;
    protected $tableName;
    protected $prefixTable = '';
    protected $primaryKey = array();
    public $modalSize = 'modal-lg';

    public function __construct($id = array()) {

        if (count($id) > 0) {
            if ($id[0] > 0) {
                $i = 0;
                foreach ($this->primaryKey as $pk) {
                    $this->set($pk, $id[$i++]);
                }
                $this->persisted = true;
            }
        }
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->mapper)) {
            throw new Exception("There is no variable called \"{$name}\" available on __set function");
            return null;
        }
        
        else $this->params[$name] = $value;
    }

    public function __get($name){
        if(property_exists(get_called_class(), $name)){
            return $this->$name;
        }

        if (!array_key_exists($name, $this->mapper) &&  !in_array($name, $this->primaryKey)) {
            throw new Exception("There is no variable called \"{$name}\" available on __get function");
            return null;
        }

        if(!array_key_exists($name, $this->params)){
            return '';
        }

        return $this->params[$name];
    }

    public function __isset($name){
        return array_key_exists($name, $this->mapper) || in_array($name, $this->primaryKey) || property_exists(get_called_class(), $name);
    }

    public function set($var, $value) {
        $this->params[$var] = $value;
    }

    public function get($var) {
        if (array_key_exists($var, $this->params)) {
            return $this->params[$var];
        } else {
            return '';
        }
    }
    
    public function image($type = 'r', $nmImg='img', $id = 0, $tableName = ''){
        return $this->getImage($type, $id, $tableName, $nmImg);
    }

    public function getImage($type = 'r', $id = 0, $tableName = '', $nmImg='img') {
        $img = $this->imagePath($type, $nmImg, $id, $tableName, false);
        if($img == '') 
            return '';
        return __BASEPATH__.$img;
    }

    public function imagePath($type = 'r', $nmImg='img', $id = 0, $tableName = '', $insertDefaultPath=true) {
        global $defaultPath;
        if ($tableName == '')
            $tableName = $this->getTableName();
        if ($id == 0)
            $id = $this->get('id');
        $img = "uploads/" . $tableName . ($tableName[(strlen($tableName) - 1)] == '/' ? '' : '/');
        
        $img .= str_pad( (int) $id, 7, '0', STR_PAD_LEFT) . '_';
        switch ($type) {
            case 'thumb' : case 't' : case 'xs' : $img .= 'thumb';
                break;
            case 'small' : case 's' : case 'sm' :  $img .= 'small';
                break;
            case 'zoom' : case 'z' : case 'lg' : $img .= 'zoom';
                break;
            case 'crop' : case 'c' : $img .= 'crop';
                break;
            default : $img .= 'regular';
                break;
        }
        $img .= '_'.$this->get($nmImg);
        return (file_exists($defaultPath . $img) ? ($insertDefaultPath ? $defaultPath : '') . $img : '');
    }

    public function getFile($id = 0, $tableName = '', $nmFile='file') {
        $file = $this->getPhysicalFile($id, $tableName, $nmFile, false);
        if($file == '')
            return '';
        return __BASEPATH__ . $file;
    }
    
    public function getPhysicalFile($id = 0, $tableName = '', $nmFile='file', $insertDefaultPath=true) {
        global $defaultPath;
        if ($tableName == '')
            $tableName = $this->getTableName();
        if ($id == 0)
            $id = $this->get('id');
        $file = "uploads/" . $tableName . ($tableName[(strlen($tableName) - 1)] == '/' ? '' : '/');
        $file .= str_pad( (int) $id, 7, '0', STR_PAD_LEFT) . '_'.$this->get($nmFile);
        return (file_exists($defaultPath . $file) ? ($insertDefaultPath ? $defaultPath : '') . $file : '');
    }
    
    public function getFileSize($id = 0, $tableName = '', $nmFile='file') {
        $file = $this->getPhysicalFile($id, $tableName, $nmFile);
        if($file == '')
            return '0KB';

        $tamanhoarquivo = filesize($file);
        $bytes = array('B','KB', 'MB', 'GB', 'TB');

        if($tamanhoarquivo <= 999)
            $tamanhoarquivo = 1;

        $i=0;

        for($i = 0; $tamanhoarquivo > 999; $i++) {
            $tamanhoarquivo /= 1024;
        }

        return round($tamanhoarquivo).$bytes[$i];
    }
    
    public function getPK() {
        return $this->primaryKey;
    }
    
    public function issetParam($var) {
        return (array_key_exists($var, $this->mapper));
    }

    public function getParams() {
        return $this->params;
    }

    public function getMapper() {
        return $this->mapper;
    }

    public function typeParam($var) {
        return $this->mapper[$var];
    }

    public function getTableName($prefix = false) {
        return ($prefix ? $this->getPrefixTable() : '').$this->tableName;
    }

    public function getPrefixTable() {
        return $this->prefixTable;
    }

    public function save(){
        if ($this->persisted){
            return $this->dbUpdate();
        }
        
        $id = $this->dbInsert();
        if($this->issetParam($this->getPK()[0])){
            $this->set($this->getPK()[0], $id);
            $this->persisted = true;
        }
        return $id;
        
    }

    public function dbInsert() {
        $conn = new Connection();

        $campos = $valores = "";

        $usr = (isset($_SESSION[$GLOBALS['Sessao']]['obj']) ? $_SESSION[$GLOBALS['Sessao']]['obj']->get('login') : 'site');
        
        if ($this->issetParam("usr_cad")) {
            $this->set("usr_cad", $usr);
        }
        if ($this->issetParam("dt_cad")) {
            if (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                $this->set("dt_cad", 'SYSDATE');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver')
                $this->set("dt_cad", 'GETDATE()');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre')
                $this->set("dt_cad", 'current_date');
            else
                $this->set("dt_cad", 'NOW()');
        }
        if ($this->issetParam("usr_ualt")) {
            $this->set("usr_ualt", $usr);
        }
        if ($this->issetParam("dt_ualt")) {
            if (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                $this->set("dt_ualt", 'SYSDATE');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver')
                $this->set("dt_ualt", 'GETDATE()');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre')
                $this->set("dt_ualt", 'current_date');
            else
                $this->set("dt_ualt", 'NOW()');
        }

        foreach ($this->params as $var => $value) {
            if ($var != '') {
                $campos .= ($campos == '' ? '' : ', ') . "{$var}";
                $valores .= ($valores == '' ? '' : ', ') . ":{$var}:";
            }
        }

        if ($this->prefixTable == '') {
            $this->prefixTable = $GLOBALS['DBPREFIX'];
        }

        $statement = $conn->prepareStatement("INSERT INTO {$this->getPrefixTable()}{$this->getTableName()} ({$campos}) VALUES ($valores)");
        foreach ($this->params as $var => $value) {
            if ($var != '') {
                switch (strtolower($this->mapper[$var])) {
                    case 'int' : $statement->setInt($var, $value);
                        break;
                    case 'number' : $statement->setNumber($var, $value);
                        break;
                    case 'sql' : $statement->setSql($var, $value);
                        break;
                    case 'clob' : $statement->setCLOB($var, $value);
                        break;
                    case 'date' :
                        if($value == '') 
                            $statement->setSql($var, "NULL");
                        elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                            $statement->setSql($var, "TO_DATE('{$value}','DD/MM/YYYY HH24:MI:SS')");
                        else
                            $statement->setString($var, $value);
                        break;
                    default : $statement->setString($var, $value);
                        break;
                }
            }
        }

        //pegando a primary key - Usado apenas em oracle
        if (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle') {
            foreach ($this->primaryKey as $pk) {
                if ($this->params[$pk] == '') {
                    $statement->setReturnVar($pk);
                }
            }
        }
        
        return $statement->executeQuery();
    }

    public function dbUpdate() {
        $conn = new Connection();

        $usr = (isset($_SESSION[$GLOBALS['Sessao']]['obj']) ? $_SESSION[$GLOBALS['Sessao']]['obj']->get('login') : 'site');
        
        if ($this->issetParam("usr_ualt")) {
            $this->set("usr_ualt", $usr);
        }
        if ($this->issetParam("dt_ualt")) {
            if (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                $this->set("dt_ualt", 'SYSDATE');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver')
                $this->set("dt_ualt", 'GETDATE()');
            elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre')
                $this->set("dt_ualt", 'current_date');
            else
                $this->set("dt_ualt", 'NOW()');
        }

        $valores = $where = "";

        foreach ($this->params as $var => $value) {
            if (!in_array($var, $this->primaryKey) && $var != "dt_cad" && $var != 'usr_cad') {
                $valores .= ($valores == '' ? '' : ', ') . "{$var} = :{$var}:";
            }
        }

        foreach ($this->primaryKey as $pk) {
            $where .= ($where == '' ? '' : ' AND ') . "{$pk} = :{$pk}:";
        }

        if ($this->prefixTable == '') {
            $this->prefixTable = $GLOBALS['DBPREFIX'];
        }

        $statement = $conn->prepareStatement("UPDATE {$this->getPrefixTable()}{$this->getTableName()} SET {$valores} WHERE {$where}");
        foreach ($this->params as $var => $value) {
            if (!in_array($var, $this->primaryKey) && $var != "dt_cad" && $var != 'usr_cad') {
                switch (strtolower($this->mapper[$var])) {
                    case 'int' : $statement->setInt($var, $value);
                        break;
                    case 'number' : $statement->setNumber($var, $value);
                        break;
                    case 'sql' : $statement->setSql($var, $value);
                        break;
                    case 'clob' : $statement->setCLOB($var, $value);
                        break;
                    case 'date' :
                        if($value == '') 
                            $statement->setSql($var, "NULL");
                        elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                            $statement->setSql($var, "TO_DATE('{$value}','DD/MM/YYYY HH24:MI:SS')");
                        else
                            $statement->setString($var, $value);
                        break;
                    default : $statement->setString($var, $value);
                        break;
                }
            }
        }

        foreach ($this->primaryKey as $pk) {
            switch (strtolower($this->mapper[$pk])) {
                case 'int' : $statement->setInt($pk, $this->params[$pk]);
                    break;
                case 'number' : $statement->setNumber($pk, $this->params[$pk]);
                    break;
                case 'sql' : $statement->setSql($pk, $this->params[$pk]);
                    break;
                case 'date' :
                    if (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle')
                        $statement->setSql($pk, "TO_DATE('{$this->params[$pk]}','DD/MM/YYYY HH24:MI:SS')");
                    else
                        $statement->setString($pk, $this->params[$pk]);
                    break;
                default : $statement->setString($pk, $this->params[$pk]);
                    break;
            }
        }

        return $statement->executeQuery();
    }

    public static function dbDelete($obj, $where='') {
        $conn = new Connection();
        if( trim($where)=='' )
            return false;
        $sql = "DELETE FROM {$obj->getPrefixTable()}{$obj->getTableName()} WHERE {$where}";
        $conn->prepareStatement($sql)->executeQuery();
        return true;
    }

    protected function dbLoad($arr = array()) {
        $conn = new Connection();
        $where = "";
        foreach ($arr as $var => $value) {
            $where .= ($where == '' ? '' : ' AND ') . "" . $var . " = '{$value}'";
        }

        $fields = "";
        foreach ($this->mapper as $var => $type) {
            if (strtolower($type) == 'date' && strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle') {
                $fields .= ",TO_CHAR({$var},'DD/MM/YYYY HH24:MI:SS') as {$var}";
            } else
                $fields .= ",{$var}";
        }
        $fields = substr($fields, 1);
        $statement = $conn->prepareStatement("SELECT {$fields} FROM {$this->getPrefixTable()}{$this->getTableName()} WHERE {$where}");

        return $statement->executeReader();
    
    }

    public function objLoad() {
        $arr = array();
        
        foreach ($this->primaryKey as $pk) {
            $arr = array_merge($arr, array($pk => $this->params[$pk]));
        }
        $rs = $this->dbLoad($arr);

        if ($rs->numRows() > 0) {
            while ($rs->next()) {
                foreach ($this->mapper as $var => $type) {
                    switch ($type) {
                        case 'int' : $this->set($var, $rs->getInt($var));
                            break;
                        case 'number' : $this->set($var, $rs->getNumber($var));
                            break;
                        default : $this->set($var, $rs->getString($var));
                            break;
                    }
                }

                $this->persisted = true;
            }
        }
    }

    public function getExtraTab() {
        return '';
    }

    public function getExtraTabContent() {
        return '';
    }

    public function getExraFilter() {
        return array('name'=>'','data'=>array());
    }
    
    public function getExraFilterHtml() {
        return '';
    }
    
    public function getExtraFunction() {
        return '';
    }

    public static function deleteImage($id, $nmImage='img'){
        $classe = get_called_class();
        $obj = $classe::load($id);
        $tam = 'tam'.ucfirst($nmImage);
        Image::deleteImage($classe::$$tam, $id, $obj->getTableName(), $obj->get($nmImage));
        $obj->set($nmImage, '');
        $obj->save();
        unset($obj);
    }

    public static function deleteFile($id, $nmFile='file'){
        $classe = get_called_class();
        $obj = $classe::load($id);
        File::deleteFile($id, $obj->getTableName(), $obj->get($nmFile));
        unset($obj);
    }

    public static function load($id) {
        $classe = get_called_class();
        $obj = new $classe(array($id));
        $obj->objLoad();
        return $obj;
    }

    public static function loadBy($by, $val){
        $classe = get_called_class();
        $obj = new $classe();

        $rs = self::search([
            's' => $obj->getPK(),
            'w' => "`{$by}` = '{$val}'",
            'l' => '0,1'
        ]);

        if(!$rs->next()){
            return $obj;
        }

        return self::load($rs->getInt($obj->getPK()));
    }

    public static function exists($where=''){
        $className = get_called_class();
        $obj = new $className;

        $rs = $className::search([
            'fields' => 'count('.$obj->getPk()[0].') qtd',
            'where' => $where,
        ]);
        $rs->next();
        return $rs->getInt('qtd') > 0;
    }

    public static function search($data = [
        'select' => '',
        'where' => '', 
        'order' => '',
        'limit' => '',
        'join' => '',
    ]){
        $className = get_called_class();
        $obj = new $className;
        
        if(!isset($data['select'])) $data['select'] = '';
        if(!isset($data['where'])) $data['where'] = '';
        if(!isset($data['order'])) $data['order'] = '';
        if(!isset($data['limit'])) $data['limit'] = '';
        if(!isset($data['join'])) $data['join'] = '';
        
        if(isset($data['fields']) && $data['fields'] != '') $data['select'] = $data['fields'];
        if(isset($data['f']) && $data['f'] != '') $data['select'] = $data['f'];
        if(isset($data['s']) && $data['s'] != '') $data['select'] = $data['s'];
        if(isset($data['w']) && $data['w'] != '') $data['where'] = $data['w'];
        if(isset($data['a']) && $data['a'] != '') $data['all'] = $data['a'];
        if(isset($data['o']) && $data['o'] != '') $data['order'] = $data['o'];
        if(isset($data['l']) && $data['l'] != '') $data['limit'] = $data['l'];
        if(isset($data['j']) && $data['j'] != '') $data['join'] = $data['j'];
        
        if($data['select'] == ''){
            $data['select'] = $obj->getPk()[0].",".implode(',', array_keys($obj->getMapper()));
        }
        
        if(isset($data['all']) && $data['all'] != '') {
            
            $criterio = "(";
            foreach ($obj->getMapper() as $key => $type) {
                if ($type == 'string' && !in_array($key, array('dt_cad', 'usr_cad', 'dt_ualt', 'usr_ualt')))
                    $criterio .= ($criterio == '(' ? '' : ' OR ') . "LOWER({$key}) LIKE LOWER('%".Utils::removeDiatrics($data['all'])."%')";
            }
            $criterio .= ")";

            if($criterio != '()'){
                $data['where'] .= ($data['where'] != '' ? ' AND ' : '').$criterio;
            }

        }

        $conn = new Connection();
        $sql = "SELECT {$data['select']} FROM ".$obj->getTableName().
            (isset($data['join']) && $data['join'] != '' ? $data['join'] : '').
            (isset($data['where']) && $data['where'] != '' ? ' WHERE '.$data['where'] : '').
            (isset($data['order']) && $data['order'] != '' ? ' ORDER BY '.$data['order'] : '');
        
        if(isset($data['limit']) && $data['limit'] != ''){
            $parts = explode(",", $data['limit']);
            $inicio = $parts[0];
            $qtd = isset($parts[1]) ? $parts[1] : 0;
            
            if (in_array( strtolower($GLOBALS['DBCONN']['data']['drive']), ['oracle','sqlserver'])) {
                $sql .= " OFFSET {$inicio} ROWS".($qtd > 0 ? " FETCH NEXT {$qtd} ROWS ONLY" : '');  

            } elseif (strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre') {
                $sql .= ($qtd > 0 ? ' LIMIT '.$qtd : '').' OFFSET '.$inicio;

            } else {
                $sql .= ' LIMIT '.$inicio.($qtd > 0 ? ', '.$qtd : '');
            }
        } 
        return $conn->prepareStatement($sql)->executeReader();
    }

}
