<?php

namespace Web\Lib;

use Application\Lib\Log;
use Web\Module as WebModule;

/**
 * Blogger
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Blogger {

    protected static $service; // for application module

    public static function instance($accessToken) {
        static::$service or static::_init($accessToken);
        return static::$service;
    }

    public static function _init($accessToken) {
        $scopes = implode(' ' , [
            \Google_Service_Oauth2::USERINFO_EMAIL, 
            \Google_Service_Blogger::BLOGGER_READONLY,
            \Google_Service_Blogger::BLOGGER
        ]);
        $client = new \Google_Client();
        $client->setClientId(WebModule::getConfig('google_app_id2'));
        $client->setClientSecret(WebModule::getConfig('google_app_secret2'));
        $client->setRedirectUri(WebModule::getConfig('google_app_redirect_uri2'));
        $client->addScope($scopes);    
        $client->setAccessToken($accessToken);
        static::$service = new \Google_Service_Blogger($client);        
    }

    public static function post($blogId, $data, $accessToken, &$errorMessage = '') {     
        try {            
            $bloggerPost = new \Google_Service_Blogger_Post();
            $bloggerPost->setTitle($data['name']);
            $bloggerPost->setContent($data['content']);            
            $bloggerPost->setLabels($data['labels']);                  
            $post = static::instance($accessToken)->posts->insert($blogId, $bloggerPost);
            if ($postId = $post->getId()) {
                return $postId;
            }            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

}
