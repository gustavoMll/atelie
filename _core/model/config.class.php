<?php

class Config {
    private $configs;

    public function __construct (){
        $conn = new Connection();
        $sql = "SELECT chave, valor FROM configs";
        $this->configs = [];
        $rs = $conn->prepareStatement($sql)->executeReader();
        while($rs->next()){
            $this->configs += [strtolower($rs->getString('chave')) => $rs->getString('valor')];
        }
    }

    public function get($var){
        $var = strtolower(Utils::removeDiatrics($var));
        return isset($this->configs[$var]) ? $this->configs[$var] : '';
    }

    public function set($key, $value){
        $key = strtolower(Utils::replace('/[a-zA-Z0-9\-\_]/','',Utils::removeDiatrics($key)));
        $value = Security::antiInjection($value);

        $conn = new Connection();
        $sql = "SELECT count(chave) FROM configs WHERE chave = '{$key}'";
        if($conn->prepareStatement($sql)->executeScalar() > 0){
            $sql = "UPDATE configs SET valor = '{$value}' WHERE chave = '{$key}'";
        }else{
            $sql = "INSERT INTO configs (chave, valor) VALUES ('{$key}', '{$value}')";
        }
        $conn->prepareStatement($sql)->executeQuery();
    }

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `configs`;

        CREATE TABLE `configs` (
            `chave` varchar(50) NOT NULL,
            `valor` text DEFAULT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
        
        INSERT INTO `configs` (`chave`, `valor`) VALUES
        ('nome-site', ''),
        ('slogan', ''),
        ('meta-desc', ''),
        ('keywords', ''),
        ('head-scripts', ''),
        ('footer-scripts', ''),
        ('email', NULL),
        ('fb-access-token', NULL),
        ('fb-pixel', NULL),
        ('', '');
        ";
    }
}