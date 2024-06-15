<?php
/* 
    Para ativar rode: composer require facebook/php-business-sdk
    Descomente as linhas 
    - em overlays no GG, descomente o campo com o $Config->get('fb-access-token')
    - no index do site, descomente a primeira linha
    - no controller do contato do site, descomente a linha depois do sucesso do ag. email
*/

class FBApi {

	public static function connect(){
		global $Config;
		if( (string) $Config->get('fb-access-token') == '')
			return false;

		$fbapi = FacebookAds\Api::init(null, null, (string) $Config->get('fb-access-token') );
	    $fbapi->setLogger(new FacebookAds\Logger\CurlLogger());

	    return $fbapi;
	}

	public static function userData($data = []){
		return (new FacebookAds\Object\ServerSide\UserData($data))
	        ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
	        ->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);
	}

	public static function customData($data = []){
		return (new FacebookAds\Object\ServerSide\CustomData($data));
	}

	public static function event($event_name='PageView', $event_id='', $userData=[], $customData=[]){
	    global $Config;

	    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	    	
	    if(!self::connect() || (string) $Config->get('fb-pixel') == ''){
			return false;
		}
		
	    $fbevent = (new FacebookAds\Object\ServerSide\Event())
	        ->setEventName($event_name)
	        ->setEventTime(time())
	        ->setEventSourceUrl($actual_link)
	        ->setUserData(self::userData($userData))
	        ->setCustomData(self::customData($customData))
	        ->setActionSource(FacebookAds\Object\ServerSide\ActionSource::WEBSITE);
		if($event_id != '') $fbevent->setEventId($event_id);

	    $fbreq = (new FacebookAds\Object\ServerSide\EventRequest( (string) $Config->get('fb-pixel') ) )
	    	//->setTestEventCode('TEST_ID')
	        ->setEvents([ $fbevent ]);
	    $fbreq->execute();
	}
	
}