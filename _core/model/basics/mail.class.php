<?php

class Mail {

    public static function schedule($data=[
        'from' => '',
        'fromName' => '',
        'subject' => '',
        'to' => [],
        'message' => '',
        'attachment' => [],
        'bcc' => [],
    ]){
        foreach($data as $key => $value){
            if(is_array($value)){
                foreach($value as $i => $v){
                    if (is_array($v)) {
                        $data[$key][$i][0] = trim((string) $v[0]);
                        $data[$key][$i][1] = trim((string) $v[1]);
                    }else{
                        $data[$key][$i] = trim((string) $v);
                    }
                }
            }else{
                $data[$key] = (string) $value;
            }
        }
        
        $obj = new EmailAgendado();
        $obj->set('assunto', $data['subject']);
        $obj->set('para', implode(',',$data['to']));
        $obj->set('dados', base64_encode(serialize($data)));
        $obj->save();
    }

    public static function sendScheduled($qtd=10){
        $rs = EmailAgendado::search([
            'fields' => 'id',
            'where' => 'dt_env IS NULL',
            'limit' => '0,'.$qtd
        ]);
        while($rs->next()){
            $obj = EmailAgendado::load($rs->getInt('id'));
            $data = unserialize(base64_decode($obj->get('dados')));
            $error = '';
            if(!isset($data['from']) || $data['from'] == ''){
                $error .= 'Error FROM; ';
            }
            if(!isset($data['fromName']) || $data['fromName'] == ''){
                $data['fromName'] = $data['from'];
            }
            if(!isset($data['subject']) || $data['subject'] == ''){
                $error .= 'Error SUBJECT; ';
            }
            if(!isset($data['to']) || !is_array($data['to']) || count($data['to']) == 0){
                $error .= 'Error TO; ';
            }
            if(!isset($data['message']) || $data['message'] == ''){
                $error .= 'Error MESSAGE; ';
            }
            if(!isset($data['to']) || !is_array($data['to']) || count($data['to']) == 0){
                $error .= 'Error TO; ';
            }
            if(!isset($data['attachment'])){
                $data['attachment'] = [];
            }
            if(!isset($data['bcc'])){
                $data['bcc'] = [];
            }


            if($error == ''){
                $retorno = self::send(
                    $data['from'], 
                    $data['fromName'],
                    $data['subject'], 
                    $data['to'], 
                    $data['message'], 
                    $data['attachment'],
                    $data['bcc']
                );

                $error = $retorno['error'];
                $exception = $retorno['exception'];
                $ok = $retorno['success'];

                if($exception != ''){
                    $error .= ",{$exception}";
                }else{
                    $obj->set('dt_env',date('Y-m-d H:i:s'));   
                }
            }
            
            $obj->set('erro',$error);
            $obj->save();
            
        }
    }

    public static function send($from, $fromName, $subject, $destinatarios, $msg, $anexos=array(),$bcc=array()){
        
        try{
            $error = $exception = '';

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP();
            //$mail->SMTPDebug = 1;
            $mail->Host         = $GLOBALS['EmailHost'];
            $mail->Port         = $GLOBALS['EmailPort'];
            $mail->SMTPSecure   = $GLOBALS['EmailSMTPSecure'];
            $mail->SMTPAuth     = $GLOBALS['EmailSMTPAuth'];
            $mail->Username     = $GLOBALS['EmailUsername'];
            $mail->Password     = $GLOBALS['EmailPassword'];
            $mail->CharSet      = $GLOBALS['Charset'];
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            if(strtoupper($GLOBALS['EmailTipo']) == 'XOAUTH2'){
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->AuthType = $GLOBALS['EmailTipo'];
                
                $client = new Google\Client();
                $client->setClientId($GLOBALS['EmailGCID']);
                $client->setClientSecret($GLOBALS['EmailGCSecret']);
                
                $dataOauth = [
                    'provider' => new League\OAuth2\Client\Provider\Google([
                        'clientId' => $client->getClientId(),
                        'clientSecret' => $client->getClientSecret(),
                    ]),
                    'clientId' => $client->getClientId(),
                    'clientSecret' => $client->getClientSecret(),
                    'refreshToken' => stripslashes($mail->Password),
                    'userName' => $mail->Username,
                ];

                $mail->setOAuth(new PHPMailer\PHPMailer\OAuth($dataOauth));
            }

            $mail->AddReplyTo($from, html_entity_decode($fromName,ENT_QUOTES,$mail->CharSet));
            $mail->SetFrom($mail->Username, html_entity_decode($fromName,ENT_QUOTES,$mail->CharSet));
            $mail->Subject = html_entity_decode($subject,ENT_QUOTES,$mail->CharSet);

            $msgPadrao = "
                <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                <html xmlns=\"http://www.w3.org/1999/xhtml\">
                <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset={$mail->CharSet}\" />
                <title></title>

                <style scoped>

                    .wrapper { padding: 30px 0 }

                    .container { 
                        margin: 0 auto;
                        width: 50%; 
                        min-width: 600px }

                    .text-center { text-align: center }

                    h2 { margin-bottom: 2em }

                    .header { border-bottom: 1px solid #EEE }

                    .body { background: #F6F6F6 }

                    .body-message {
                        line-height: 150%; 
                        line-height: 150%; 
                        font-size: 12pt; 
                        color: #777777 }

                    table {
                        border-right: 1px solid #CCC; 
                        border-bottom: 1px solid #CCC }

                    th { font-size: 80% }

                    th, td {
                        color: #333;
                        padding: .5rem 1rem; 
                        border-top: 1px solid #CCC; 
                        border-left: 1px solid #CCC   }

                    .btn {
                        font-size: 80%;
                        border-radius: 5px;
                        background: #00568A;
                        color: #FFF !important;
                        display: inline-block;
                        text-decoration: none;
                        padding: .5rem 1rem }

                    .small { font-size: 80% }

                    .cite { 
                        padding-top: 20px;
                        margin-top: 20px;
                        border-top: 1px solid #DDD;
                        font-size: 120% }

                    .footer {
                        font-size: 12pt;
                        border-top: 1px solid #EEE; 
                        color: #AAA }

                    .footer .container { font-size: 75% }

                    @media(max-width: 767px){
                        .container {  
                            margin: 0;
                            padding: 0 20px; 
                            width: 100%; 
                            min-width: auto }
                    }

                </style>

                </head>
                <body>

                <div class=\"header wrapper\" style=\"background-color:#00568A\">
                    <h1 class=\"container\"><img src=\"".$GLOBALS['EnderecoSite'].__BASEPATH__."uploads/brand.png\" style=\"margin: 0;\"  height=\"50\" /></h1>
                </div>
                <div class=\"body wrapper\">
                     <div class=\"container\">
                        <h2>{$mail->Subject}</h2>
                        <div class=\"body-message\">{$msg}</div>
                    </div>
                </div>

                <div class=\"footer wrapper\">
                    <div class=\"container\">Essa mensagem foi enviado dia ".date("d/m/Y H:i:s")." atrav&eacute;s do site {$GLOBALS['Dominio']}.</div>
                </div>

                </body></html>";

            $mail->MsgHTML($msgPadrao);   
            
            foreach($destinatarios as $destinatario)
                $mail->AddAddress($destinatario);
            foreach($bcc as $destinatario)
                $mail->AddBcc($destinatario);

            if(count($anexos) > 0){
                foreach ($anexos as $anexo){
                    if(is_array($anexo))
                        $mail->AddAttachment($anexo[0], $anexo[1]);
                    else
                        $mail->AddAttachment($anexo);
                }
            }
            
            $ret = $mail->Send();
            
        }catch(Exception $e){
            $exception = $e->getMessage();
        }
        
        return [
            'success' => $ret,
            'error' => $mail->ErrorInfo,
            'exception' => $exception,
        ];
    }
}