<?php

namespace Web\Lib;

use Application\Lib\Log;
use Web\Module as WebModule;

/**
 * Facebook
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Gmail {

    protected static $mail; // for application module

    public static function instance() {
        static::$mail or static::_init();
        return static::$mail;
    }

    public static function _init() {
        
        $client = new \Google_Client();
        $client->setClientId(\Application\Module::getConfig('google_app_id'));
        $client->setClientSecret(\Application\Module::getConfig('google_app_secret'));
        $client->setRedirectUri(\Application\Module::getConfig('google_app_redirect_uri'));
        //$client->addScope(\Google_Service_Gmail::GMAIL_COMPOSE);
        $client->setAccessToken($AppUI->google_access_token);  
            
        static::$mail = new \Facebook\Facebook([
            'app_id' => WebModule::getConfig('facebook_app_id'),
            'app_secret' => WebModule::getConfig('facebook_app_secret'),
            'default_graph_version' => 'v2.6'
        ]);
    }

    public static function postToWall($data, $accessToken, &$errorMessage = '') {     
        try {           
            
            $response = static::instance()->post("/me/feed", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function postToGroup($groupId, $data, $accessToken, &$errorMessage = '') {
       try {
            $response = static::instance()->post("/{$groupId}/feed", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function commentToPost($postId, $data, $accessToken, &$errorMessage = '') {
        try {
            if (empty($data['message'])) {
                $data['message'] = 'vuongquocbalo.com';
            }
            $response = static::instance()->post("/{$postId}/comments", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function meCreateAlbum($data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/me/albums", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function groupCreateAlbum($groupId, $data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/{$groupId}/albums", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function addPhotoToAlbum($albumId, $data, $accessToken, &$errorMessage = '') {
        try {
            $response = static::instance()->post("/{$albumId}/photos", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }

    public static function uploadUpublishedPhoto($data, $accessToken, &$errorMessage = '') {
        try {
            $data['published'] = false;
            $response = static::instance()->post("/me/photos", $data, $accessToken);
            $graphNode = $response->getGraphNode();
            if (!empty($graphNode['id'])) {
                return $graphNode['id'];
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);           
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::warning($errorMessage);
        }
        return false;
    }
}
