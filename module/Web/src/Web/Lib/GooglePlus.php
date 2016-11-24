<?php

namespace Web\Lib;

use Application\Lib\Log;
use Web\Module as WebModule;

/**
 * GooglePlus
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class GooglePlus {

    protected static $service; // for application module

    public static function instance($accessToken) {
        static::$service or static::_init($accessToken);
        return static::$service;
    }

    public static function _init($accessToken) {
        $scopes = implode(' ' , [
            \Google_Service_Oauth2::PLUS_LOGIN, 
            \Google_Service_Plus::PLUS_ME
        ]);
        $client = new \Google_Client();
        $client->setClientId(WebModule::getConfig('google_app_id2'));
        $client->setClientSecret(WebModule::getConfig('google_app_secret2'));
        $client->setRedirectUri(WebModule::getConfig('google_app_redirect_uri2'));
        $client->addScope($scopes);    
        $client->setAccessToken($accessToken);
        static::$service = new \Google_Service_Plus($client);        
    }

    public static function post($data, $accessToken, &$errorMessage = '') {     
        try {
            $momentBody = new \Google_Service_Plus_Moment();
            $momentBody->setType("http://schemas.google.com/AddActivity"); 
            
            $itemScope = new \Google_Service_Plus_ItemScope();      
			$itemScope->setType("http://schemas.google.com/AddActivity");	          
			$itemScope->setName($data['name']);			
            $itemScope->setUrl($data['url']);          
            $itemScope->setDescription('Test');          
            $momentBody->setTarget($itemScope);
            $momentResult = static::instance($accessToken)->moments->insert('me', 'vault', $momentBody);

			return $momentResult;
//            if ($postId = $post->getId()) {
//                return $postId;
//            }            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage(); echo $errorMessage;
            Log::warning($errorMessage);
        }
        return false;
    }

}
