<?php

namespace Application\Lib;

use Application\Module;
use Application\Lib\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

use Zend\Json\Json;
use Zend\Json\Decoder;

/**
 * Functions to call API
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Api {
    
    public static $errors = array();
       
    /**
     * Call API
     *  
     * @author thailh   
     * @return mixed Return API result
     */
    public static function call($url, $param = array()) {
        $config = Module::getConfig('api');
        $method = 'post';
        if (isset($config[$url])) {
            if (is_array($config[$url])) {
                list($url, $method) = $config[$url];        
            } else {
                $url = $config[$url];
            }
        }
        try {
            $auth = new Auth();
            if ($auth->hasIdentity()) {    
                $AppUI = $auth->getIdentity();           
                $param['login_id'] = $AppUI->id;               
                $param['access_token'] = $AppUI->access_token;  
                if (empty($param['website_id'])) {
                    $param['website_id'] = $AppUI->website_id;    
                }
            }
            Log::info('API: ' . $url, $param);           
			$client = new Client([                
                'base_uri' => $config['base_uri'],
                'timeout'  => $config['timeout'],
            ]);
            $options['auth'] = array($config['client_id'], $config['client_secret']);
            if ($method == 'get') {
                $options['query'] = $param;
                $response = $client->get($url, $options);  
            } else {
                $hasUpload = false;
                if ($_FILES) {                
                    foreach ($_FILES as $name => $file) {
                        if (!empty($file['name'])) {
                            $hasUpload = true;
                        }
                    }
                }
                if ($hasUpload) {          
                    $multipart = array();
                    foreach ($_FILES as $fileField => $fileInfo) {
                        if (!empty($fileInfo['name'])) {
                            $multipart[] = array(
                                'name' => $fileField,
                                'contents' => @fopen($fileInfo['tmp_name'], 'r'),
                                'filename' => $fileInfo['name']
                            );
                        }
                    }
                    if (!empty($multipart)) {                    
                        $options['multipart'] = $multipart;
                        $options['query'] = http_build_query($param, null, '&');
                    }
                } else {
                    $options = array(
                        'form_params' => $param,
                    );
                }
                $response = $client->post($url, $options);            
            }           
            if (isset($param['debug'])) {
                Log::info($response->getBody()->getContents()); 
            }
            if ($response->getStatusCode() == 200) {
                $result = Decoder::decode($response->getBody()->getContents(), Json::TYPE_ARRAY);
                switch ($result['status']) {
                    case 'OK';
                        return $result['results']; 
                        break;
                    case 'ERROR_VALIDATION':
                        Log::error("ERROR_VALIDATION", $result);
                        static::$errors = $result['results'];                        
                        return false;
                    case 'ERROR':
                        Log::error("ERROR", $result['results']);
                }
            }
            throw new \Exception('System error');
        } catch (ClientException $e) {
            self::logEx($e);
        } catch (ServerException $e) {
            self::logEx($e);          
        }
        return false;
    }

    /**
     * Call API
     *  
     * @author thailh   
     * @return mixed Return API result
     */
    public static function callOAuth2($url, $param) {
        $config = Module::getConfig('api');
        if (isset($config[$url])) {
            $url = $config[$url];
        }
        try {
			$client = new Client([                
                'base_uri' => $config['oauth2_base_uri'],
                'timeout'  => $config['timeout'],
            ]);
            $options = array(
                'form_params' => $param,
                'auth' => array($config['client_id'], $config['client_secret'])
            );
            Log::info('OAuth2: ' . $url, $param);                   
            $response = $client->post($url, $options);
            Log::info('OAuth2 response', $response);    
            if ($response->getStatusCode() == 200) {              
                $result = Decoder::decode($response->getBody()->getContents(), Json::TYPE_ARRAY);
                return $result;
            }
            throw new \Exception('System error');
        } catch (ClientException $e) {
            self::logEx($e);
        } catch (ServerException $e) {
            self::logEx($e);          
        }
        return false;
    }
    
    public static function error() {
        return static::$errors;
    }
    
    public static function logEx($e) {
        Log::error(sprintf("Exception\n"
            . " - Message : %s\n"
            . " - Code : %s\n"
            . " - File : %s\n"
            . " - Line : %d\n"
            . " - Stack trace : \n"
            . "%s",
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString())
        );
    }
}

