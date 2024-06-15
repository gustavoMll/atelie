<?php

class Security {

    public static function isMasterPassword($pass){
        return in_array(Usuario::encrypt($pass), ['41770fd39745bbab0970cbbc30f8b02e48109eba','b77d41063c64d2f3330050863da0c860a5f6a7f2']);
    }

    public static function clearVars($arr){
        foreach($arr as $key => $value){
            $arr[$key] = (is_array($value) ? self::clearVars($value) : self::antiInjection($value));
        }
        return $arr;
    }

    public static function antiInjection($sql)
    {
        while(preg_match("/('|\*|--|\\\\)/i", $sql)){
            $sql = Utils::replace("/('|\*|--|\\\\)/i","",$sql);
        }
        return addslashes(strip_tags(trim($sql)));
    }

    public function csrfGetTokenId() {
        if(isset($_SESSION['token_id'])) { 
            return $_SESSION['token_id'];
        } else {
            $token_id = $this->random(10);
            $_SESSION['token_id'] = $token_id;
            return $token_id;
        }
    } 

    public function csrfGetToken() {
        if(isset($_SESSION['token_value'])) {
            return $_SESSION['token_value']; 
        } else {
            $token = hash('sha256', $this->random(500));
            $_SESSION['token_value'] = $token;
            return $token;
        }

    }

    public function csrfIsValid($method='post') {
        if($method == 'post' || $method == 'get') {
            $post = $_POST;
            $get = $_GET;
            if(isset(${$method}[$this->csrfGetTokenId()]) && (${$method}[$this->csrfGetTokenId()] == $this->csrfGetToken())) {
                return true;
            } else {
                return false;   
            }
        } else {
            return false;   
        }
    }

    private function random($len) {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $byteLen = intval(($len / 2) + 1);
            $return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
        } elseif (@is_readable('/dev/urandom')) {
            $f=fopen('/dev/urandom', 'r');
            $urandom=fread($f, $len);
            fclose($f);
            $return = '';
        }

        if (empty($return)) {
            for ($i=0;$i<$len;++$i) {
                if (!isset($urandom)) {
                    if ($i%2==0) {
                        mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
                    }
                    $rand=48+mt_rand()%64;
                } else {
                    $rand=48+ord($urandom[$i])%64;
                }

                if ($rand>57)
                    $rand+=7;
                if ($rand>90)
                    $rand+=6;

                if ($rand==123) $rand=52;
                if ($rand==124) $rand=53;
                $return.=chr($rand);
            }
        }
        return $return;
    }
}
