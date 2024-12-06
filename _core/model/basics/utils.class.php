<?php

class Utils {

    public static function isoToUtf8($str){
        return mb_convert_encoding($str, 'utf-8', 'iso-8859-1');
    }

    public static function utf8ToIso($str){
        return mb_convert_encoding($str, 'iso-8859-1', 'utf-8');
    }

    public static function getRgbCode($hexColor){
        $hexColor = ltrim($hexColor, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        return "{$r},{$g},{$b}";
    }

    public static function ajaxHeader($noIndex=true){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        if($noIndex)
            header("X-Robots-Tag: noindex", true);
    }

    public static function jsonResponse($message='', $success=false, $vars=[], $httpCode=200){
        $retorno = [
            'success' => $success,
            'message' => $message,
        ];
        if(count($vars) > 0) $retorno += $vars;
        http_response_code($httpCode);
        echo json_encode($retorno);
        exit;
    }

    public static function getDataImage($path){
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function generateSitemap(){
        self::getFileByCurl($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].__BASEPATH__.'gerar-sitemap');
    }
    
    public static function replacePath($desc){
        return str_replace('__BASEPATH__',__BASEPATH__,str_replace('__PATH__',__PATH__,$desc));
    }

    public static function moduleLoadData($action, $text="Carregar dados"){
        return '
        <div class="text-center">
            <button class="btn btn-default" type="button" onclick="'.$action.'">
                <span class="glyphicon glyphicon-refresh"></span> '.$text.'
            </button>
        </div>
        ';
    }

    public static function getMaskedDoc($doc){
        $doc = self::replace('/[^0-9]/','',$doc);
        return self::mask($doc,(strlen($doc)>11?'##.###.###/####-##':'###.###.###-##'));
    }

    public static function getTel($phone){
        $phone = self::replace('/[^0-9]/','',$phone);
        if(strlen($phone) == 0) return '';
        return self::mask($phone,(strlen($phone)>10?'(##) #####-####':'(##) ####-####'));
    }

    public static function getTelWhats($phone="",$msg="Opa, tudo bem?"){
        $phone = self::replace('/[^0-9]/','',$phone);
        if(strlen($phone) == 0) return '';
        if(in_array(substr($phone, 2, 1), array(8,9))){
            return '<a href="http://api.whatsapp.com/send?phone=55'.$phone.'&text='.$msg.'" target="_blank" data-toggle="tooltip" data-placement="right" title="Chamar no WhatsApp">'.self::getTel($phone).'</a>';
        }else{
            return self::getTel($phone);
        }
    }

    public static function getIp(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function decodeText($text)
    {
        return html_entity_decode(strip_tags(self::replace('/\s\s+/', ' ', $text)), ENT_QUOTES, $GLOBALS['Charset']);
    }

    public static function subText($text, $length)
    {
        if ($length)
        {
            $dec = self::decodeText($text);
            $ret = substr($dec, 0, $length);

            $ret = $ret.(strlen($dec) > $length ? "..." : "");
        }
        else
            $ret = $text;

        return $ret;
    }

    public static function show($texto, $class="success", $return=false, $close=true) {
        $retorno = '<div class="alert alert-'.$class.' '.($close ? 'alert-dismissible' : '').'" role="alert">'.
        $texto
        .
        ($close ? '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' : '').'
        </div>';
        if($return)
            return $retorno;
        else
            print($retorno);
    }
        
    public static function alert($texto, $class="success", $returnJs=false) {
        $alert = "tAlert('', '{$texto}', '{$class}');";
        if(!$returnJs){ 
            print("<script type=\"text/javascript\">{$alert}</script>");
        }else{
            return $alert;
        }
    }

    public static function friendlyUrl($id, $name){
        $nome = self::replace('/[ ]/i','-',self::replace('/[^0-9A-Za-z ]/i','',self::removeDiatrics(strip_tags($name))));
        return $id."/".trim(substr(strtolower($nome),0,50)).".html";
    }

    public static function hotUrl($id, $name) {
        $nome = self::replace('/[ ]/i','-',self::replace('/[^0-9A-Za-z ]/i','',self::removeDiatrics(strip_tags($name))));
        return preg_replace('/-+/', '-', trim(substr(strtolower($nome), 0, 80)).'-').$id;
    }

    public static function checkEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

     public static function convertEmail($email) {
        $p = str_split(trim($email));
        $new_mail = '';
        foreach ($p as $val) {
            $new_mail .= '&#'.ord($val).';';
        }
        return $new_mail;
    }
    
    public static function parseFloat($valor) {
        return (float) str_replace(",",".", str_replace(".","", self::Replace('/[^0-9\,\.]/','', $valor)));
    }

    public static function parseMoney($valor, $precision=2) {
        $valor = str_replace(",",".",$valor);
        return number_format($valor,$precision,",",".");
    }

    public static function redirect($pagina){
        print("<script type=\"text/javascript\"> location='".$pagina."';</script>");
    }

    public static function location($pagina){
        header('Location:'.$pagina);
        exit;
    }

    public static function getFileExtension($file_name) {
        $arr = explode('.',$file_name);
        $extension = end($arr);
        return strtolower($extension);
    }
    
    public static function getFileByCurl($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public static function cpfValid($cpf) { 
        $cpf = str_pad(preg_replace('/[^0-9]/i', '', $cpf), 11, '0', STR_PAD_LEFT);
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return false;
        } else {  
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf[$c] != $d) {
                    return false;
                }
            }

            return true;
        }
    }

    public static function cnpjValid($cnpj) {
        $j = 0;
        for ($i = 0; $i < (strlen($cnpj)); $i++) {
            if (is_numeric($cnpj[$i])) {
                $num[$j] = $cnpj[$i];
                $j++;
            }
        }
        if (count($num) != 14) {
            $isCnpjValid = false;
        }
        if ($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0) {
            $isCnpjValid = false;
        }
        else {
            $j = 5;
            for ($i = 0; $i < 4; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 4; $i < 12; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2) {
                $dg = 0;
            } else {
                $dg = 11 - $resto;
            }
            if ($dg != $num[12]) {
                $isCnpjValid = false;
            }
        }
        
        if (!isset($isCnpjValid)) {
            $j = 6;
            for ($i = 0; $i < 5; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 5; $i < 13; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2) {
                $dg = 0;
            } else {
                $dg = 11 - $resto;
            }
            if ($dg != $num[13]) {
                $isCnpjValid = false;
            } else {
                $isCnpjValid = true;
            }
        }
        return $isCnpjValid;
    }

    public static function translateMoney($valor = 0, $maiusculas = false, $money = true) {
        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

        $z = 0;
        $rt = "";

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for($i=0;$i<count($inteiro);$i++)
            for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                $inteiro[$i] = "0".$inteiro[$i];

        $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
        for ($i=0;$i<count($inteiro);$i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
            $ru) ? " e " : "").$ru;
            $t = count($inteiro)-1-$i;
            
            if($money)
                $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            
            if ($valor == "000")$z++; elseif ($z > 0) $z--;
            if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : ""). (($money) ? $plural[$t] : "");
            if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
            ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if(!$maiusculas){
            return($rt ? $rt : "zero");
        } else {
            if ($rt) $rt=self::replace(" E "," e ",ucwords($rt));
                 return (($rt) ? ($rt) : "Zero");
        }

    }

    public static function reArrayFiles($file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    public static function replace($regularexp, $to, $string){
        return preg_replace_callback($regularexp, function () use ($to) { return $to; }, $string);
    }

    public static function mask($val, $mask) {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            }
            else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
    
    public static function getYoutubeId($url){
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        if($match){
            return $match[1];
        }
        return '';
    }

    public static function getYoutubeImg($url, $type='default.jpg'){
        $id = self::getYoutubeId($url);
        if($id=='')
            return '';
        return 'https://img.youtube.com/vi/'.$id.'/'.$type;

        /*
        The default thumbnail image = default.jpg
        For the high quality version of the thumbnail = hqdefault.jpg
        There is also a medium quality version of the thumbnail = mqdefault.jpg
        For the standard definition version of the thumbnail = sddefault.jpg
        For the maximum resolution version of the thumbnail = maxresdefault.jpg
        */

    }

    public static function getEmbed($link, $width=560,$height=315){
        $defaultVimeo   = '<iframe src="https://player.vimeo.com/video/#ID#?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="#WIDTH#" height="#HEIGHT#" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        $defaultYoutube = '<iframe width="#WIDTH#" height="#HEIGHT#" src="https://www.youtube.com/embed/#ID#" frameborder="0" allowfullscreen></iframe>';

        if(strpos(strtolower($link),'youtu')){
            preg_match('/v=(.{11})|be\/(.{11})|\/v\/(.{11})|\/embed\/(.{11})/i', $link, $matches);
            $id = $matches[2];
            $embed = $defaultYoutube;

        }elseif(strpos(strtolower($link),'vimeo.com')){
            preg_match('/vimeo\.com\/([0-9]{1,10})/i', $link, $matches);
            $id = $matches[1];
            $embed = $defaultVimeo;

        }else{
            $embed = "";
        }

        if($embed!=""){
            $embed = str_replace('#WIDTH#', $width, $embed);
            $embed = str_replace('#HEIGHT#', $height, $embed);
            $embed = str_replace('#ID#', $id, $embed);
        }

        return $embed;
    }

    public static function seemsUtf8($str)
    {
        $length = strlen($str);
        for ($i=0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) $n = 0; # 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
            elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
            elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
            elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
            elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
            else return false; # Does not match any model
            for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }

    /**
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */
    public static function removeDiatrics($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        if (self::seemsUtf8($string)) {
            $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
            // Euro Sign
            chr(226).chr(130).chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194).chr(163) => '');

            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
                .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
                .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
                .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
                .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
                .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
                .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
                .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
                .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
                .chr(252).chr(253).chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }

    public static function getMonthName($mes, $lang) {
        $en = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        $pt = ["Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        if ($lang == "EN") {
            return $en[$mes-1];
        }
        return $pt[$mes-1];
    }

    public static function getWeekName($dia, $lang) {
        $en = ["Sunday", "Monday", "Thursday", "Wednesday", "Tuesday", "Friday", "Saturday"];
        $pt = ["Domingo", "Segunda", "Ter&ccedil;a", "Quarta", "Quinta", "Sexta", "S&aacute;bado"];
        if ($lang == "EN") {
            return $en[$dia];
        }
        return $pt[$dia];
    }

    public static function dateFormat($data, $formato, $lang = 'PT') {
        if (self::dateValid($data)) {
            $regex1 = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})/';
            $regex2 = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})/';
            $arrDate = array(
                'h' => 0,
                'i' => 0,
                's' => 0,
                'd' => 0,
                'm' => 0,
                'y' => 0,
            );
            $pData = explode(" ", $data);
            if (preg_match($regex2, $data)) {
                list($arrDate['y'], $arrDate['m'], $arrDate['d']) = explode('-', $pData[0]);
            } else {
                list($arrDate['d'], $arrDate['m'], $arrDate['y']) = explode('/', $pData[0]);
            }

            if (isset($pData[1])) {
                list($arrDate['h'], $arrDate['i'], $arrDate['s']) = explode(':', $pData[1]);
            }

            $mkDate = mktime($arrDate['h'], $arrDate['i'], $arrDate['s'], $arrDate['m'], $arrDate['d'], $arrDate['y']);

            if (strtolower($formato) == 'extenso') {
                $result = $arrDate['d'] . ' de ' . self::getMonthName($arrDate['m'], $lang) . ' de ' . $arrDate['y'];
            } elseif (strtolower($formato) == 'completa') {
                $replacement = ($lang != 'EN' ? "%s, %s de %s de %s" : '%s, %s %s %s');
                $result = sprintf($replacement, self::getWeekName(date("w", $mkDate), $lang), $arrDate['d'], self::getmonthName($arrDate['m'], $lang), $arrDate['y']
                );
            } else {
                $result = date($formato, $mkDate);
            }

            return $result;
        } else {
            return 'Invalid date.';
        }
    }

    public static function dateValid($data) {
        $regex1 = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})/';
        $regex2 = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})/';
        $arrDate = array(
            'h' => 0,
            'i' => 0,
            's' => 0,
            'd' => 0,
            'm' => 0,
            'y' => 0,
        );
        $pData = explode(" ", $data);
        $test1 = @preg_match($regex1, $data);
        $test2 = @preg_match($regex2, $data);
        if ($test2) {
            list($arrDate['y'], $arrDate['m'], $arrDate['d']) = explode('-', $pData[0]);
        } elseif ($test1) {
            list($arrDate['d'], $arrDate['m'], $arrDate['y']) = explode('/', $pData[0]);
        } else {
            return false;
        }
        return checkdate($arrDate['m'], $arrDate['d'], $arrDate['y']);
    }

    //para ativar a minificacao, insira a funcao no final do index.php do view
    public static function minifyOutput() {
        $buffer = ob_get_contents();
        ob_clean();

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
        $replace = array('>', '<', '\\1', '');
        $buffer = preg_replace($search, $replace, $buffer);
        
        echo $buffer;
    }

    public static function numberToWords($number, $conjunction = ' e ', $separator = ',', $negative = 'menos ', $decimal = 'ponto ', $hyphen = '-') {
        $dictionary = array(
            0 => 'zero',
            1 => 'um',
            2 => 'dois',
            3 => 'três',
            4 => 'quatro',
            5 => 'cinco',
            6 => 'seis',
            7 => 'sete',
            8 => 'oito',
            9 => 'nove',
            10 => 'dez',
            11 => 'onze',
            12 => 'doze',
            13 => 'treze',
            14 => 'quatorze',
            15 => 'quinze',
            16 => 'dezesseis',
            17 => 'dezessete',
            18 => 'dezoito',
            19 => 'dezenove',
            20 => 'vinte',
            30 => 'trinta',
            40 => 'quarenta',
            50 => 'cinquenta',
            60 => 'sessenta',
            70 => 'setenta',
            80 => 'oitenta',
            90 => 'noventa',
            100 => 'cento',
            200 => 'duzentos',
            300 => 'trezentos',
            400 => 'quatrocentos',
            500 => 'quinhentos',
            600 => 'seiscentos',
            700 => 'setecentos',
            800 => 'oitocentos',
            900 => 'novecentos',
            1000 => 'mil'
        );
    
        if (!is_numeric($number)) {
            return false;
        }
    
        if ($number < 0) {
            return $negative . self::numberToWords(abs($number));
        }
    
        $string = $fraction = null;
    
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
    
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $conjunction . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = ((int) ($number / 100)) * 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds];
                if ($remainder) {
                    $string .= $conjunction . self::numberToWords($remainder);
                }
                break;
            case $number < 1000000:
                $baseUnit = 1000;
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::numberToWords($remainder);
                }
                break;
            default:
                return $number;
        }
    
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }
    
        return $string;
    }
    
}
