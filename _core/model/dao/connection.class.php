<?php

class Connection { 
     
    /** 
     * PHP Connection resource 
     * 
     * @var unknown_type 
     */ 
    public $conn_id; 
    public $dataConnection = array(
        'user' => '',
        'pass' => '',
        'name' => '',
        'host' => '',
        'drive' => '',
        'charset' => '',
        'port' => '',
    );
    /** 
     * whether we are in a transaction 
     * 
     * @var boolean 
     */ 
    private $inTransaction = false; 
    /** 
     * are we connected? 
     * 
     * @var unknown_type 
     */ 
    private $isConnected = false; 
    /** 
     * Debugger 
     * 
     * @var Debugger 
     */ 
    private $_debugger = null;
     
    /** 
     * Create a connection 
     * 
     * @param unknown_type $host 
     * @param unknown_type $user 
     * @param unknown_type $pass 
     * @param unknown_type $db 
     * @return Connection 
     */ 
    function __construct($dataConnection=array()) 
    {
        if(count($dataConnection)>0){
            $this->dataConnection = $dataConnection;
            $GLOBALS['DBCONN'] = false;
        }else{
            $this->dataConnection = array(
               'user' => $GLOBALS['DBUser'],
               'pass' => $GLOBALS['DBPassWord'],
               'name' => $GLOBALS['DBName'],
               'host' => $GLOBALS['DBHost'],
               'port' => $GLOBALS['DBPort'],
               'drive' => $GLOBALS['DBDriver'],
               'charset' => $GLOBALS['DBCharset'],
           );
        }
        
        if(!isset($GLOBALS['DBCONN']) || !$GLOBALS['DBCONN']){
            if(substr(strtolower($this->dataConnection['drive']),0,3) == 'pdo'){
                $parts = explode('_', strtolower($this->dataConnection['drive']));
                if(count($parts) != 2){
                    throw new SQLException("N&atilde;o foi informado o drive do PDO. Informe pdo_drive. Ex: pdo_mysql.", 1);
                }
                $this->conn_id = new PDO("{$parts[1]}:dbname={$this->dataConnection['name']};host={$this->dataConnection['host']};port={$this->dataConnection['port']};charset={$this->dataConnection['charset']}", $this->dataConnection['user'], $this->dataConnection['pass']);
                $this->conn_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }elseif(strtolower($this->dataConnection['drive']) == 'oracle'){
                $this->conn_id = oci_connect($this->dataConnection['user'], $this->dataConnection['pass'], $this->dataConnection['host'].":".$this->dataConnection['port']."/".$this->dataConnection['name'], $this->dataConnection['charset']);//"WE8ISO8859P1"
            
            }elseif(strtolower($this->dataConnection['drive']) == 'sqlserver'){
                $this->conn_id = mssql_connect($this->dataConnection['host'].":".$this->dataConnection['port'], $this->dataConnection['user'], $this->dataConnection['pass']);
            
            }elseif(strtolower($this->dataConnection['drive']) == 'sqlsrv'){
                $this->conn_id = sqlsrv_connect(
                    'tcp:'.$this->dataConnection['host'].",".$this->dataConnection['port'], 
                    array(
                        "Database"=> $this->dataConnection['name'],  
                        "Uid"=>$this->dataConnection['user'], 
                        "PWD"=>$this->dataConnection['pass'],
                        "CharacterSet" => $this->dataConnection['charset'],
                    )
                );

            }elseif(strtolower($this->dataConnection['drive']) == 'postgre'){
                $this->conn_id=pg_connect("host={$this->dataConnection['host']} port={$this->dataConnection['port']} dbname={$this->dataConnection['name']} user={$this->dataConnection['user']} password={$this->dataConnection['pass']}");
                pg_set_client_encoding($this->conn_id, $this->dataConnection['charset']);
                
            }else{
                $this->conn_id = new mysqli($this->dataConnection['host'], $this->dataConnection['user'], $this->dataConnection['pass'], $this->dataConnection['name'],$this->dataConnection['port']);
                if (mysqli_connect_errno()) trigger_error(mysqli_connect_error());
                $this->conn_id->set_charset($this->dataConnection['charset']);
            }
            
            if (!$this->conn_id)
                throw new SQLException("N&atilde;o foi poss&iacute;vel conectar ao Banco de Dados.", 1);
            
            $GLOBALS['DBCONN']['conn']= $this->conn_id;
            $GLOBALS['DBCONN']['data']= $this->dataConnection;
            
        }else{
            $this->conn_id = $GLOBALS['DBCONN']['conn'];
        }
        
        $this->isConnected = true;
        //$this->setDebugger(new Debugger(true)); 
    } 
     
    /** 
     * Are we in a transaction? 
     * 
     * @return boolean 
     */ 
    function inTransaction() 
    { 
        return $this->inTransaction; 
    }
    
    /** 
     * start a transaction 
     * 
     */ 
    function beginTransaction() 
    { 
        if ($this->inTransaction) 
            throw new SQLException("Transa&ccedil;&atilde;o j&aacute; iniciada", 3); 

        if(substr(strtolower($this->dataConnection['drive']),0,3) == 'pdo'){
            $this->conn_id->beginTransaction();
        }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle'){
            $st = oci_parse($this->conn_id, "begin");
            oci_execute($st);
        }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
            mssql_query("begin", $this->conn_id);
        }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
            sqlsrv_begin_transaction($this->conn_id);
        }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre'){
            pg_query($this->conn_id,'BEGIN');
        }else{
            $this->conn_id->query("begin");
        }
        
        $this->inTransaction = true; 
    } 
     
    /** 
     * commit transaction 
     * 
     */ 
    function commitTransaction() 
    {
        try{
            if(substr(strtolower($this->dataConnection['drive']),0,3) == 'pdo'){
                $this->conn_id->commit();
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle'){
                $this->inTransaction = false;
                return oci_commit($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
                mssql_query("commit", $this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlsrv'){
                sqlsrv_commit($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre'){
                pg_query($this->conn_id,'COMMIT');
            }else{
                $this->conn_id->query("commit");
            }
            
            $this->inTransaction = false;
            return true;
        }  catch (Exception $e){
            return false;
        }
    } 
     
    /** 
     * Rollover the transaction 
     * 
     */ 
    function rolloverTransaction() 
    { 
        if ($this->inTransaction){
            if(substr(strtolower($this->dataConnection['drive']),0,3) == 'pdo'){
                $this->conn_id->rollBack();
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle'){
                $this->inTransaction = false;
                return oci_rollback($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
                mssql_query("rollover", $this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlsrv'){
                sqlsrv_rollback($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre'){
                pg_query($this->conn_id,'ROLLOVER');
            }else{
                $this->conn_id->query("rollover"); 
            }

        }
        $this->inTransaction = false;
    } 
    
    /** 
     * Close the connection
     * 
     */ 
    function closeConnection() 
    { 
        if ($this->conn_id){
            if(substr(strtolower($this->dataConnection['drive']),0,3) == 'pdo'){
                $this->conn_id = null;
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'oracle'){
                oci_close($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
                mssql_close($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlsrv'){
                sqlsrv_close($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'sqlserver'){
                mssql_close($this->conn_id);
            }elseif(strtolower($GLOBALS['DBCONN']['data']['drive']) == 'postgre'){
                pg_close($this->conn_id);
            }else{
                $this->conn_id->close();
            }
            $GLOBALS['DBCONN']=false;
        }
             
        $this->isConnected = false; 
    } 
    
    /** 
     * Create a statement 
     * 
     * @param String $sql 
     * @return Statement 
     */ 
    function &prepareStatement($sql) 
    { 
        $stmt = new Statement($this); 
        $stmt->setQuery($sql); 
         
        return $stmt; 
    } 
     
    /** 
     * Set a debugger 
     * 
     * @param unknown_type $debugger 
     */ 
    function setDebugger($debugger) 
    { 
        $this->_debugger = $debugger; 
    } 
     
    /** 
     * Get the debugger 
     * 
     * @return Debugger 
     */ 
    function getDebugger() 
    { 
        return $this->_debugger; 
    } 
    
    function isConnected(){
        return (isset($GLOBALS['DBCONN'])&& $GLOBALS['DBCONN']!=false);
    }
}