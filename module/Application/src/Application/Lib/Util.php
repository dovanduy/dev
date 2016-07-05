<?php

namespace Application\Lib;

use Application\Module;
use Application\Lib\Log;
use Zend\Validator\File;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;

/**
 * Common utility
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Util {
    
    public static function isMobile() {
        if (!isset($_SERVER["HTTP_USER_AGENT"])) {
            return false;
        }
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    /**
     * Upload multiple images  
     *  
     * @author thailh   
     * @return array | boolean 
     */
    public static function uploadImage() {
        $config = Module::getConfig('upload.image');       
        $adapter = new \Zend\File\Transfer\Adapter\Http();         
        $destination = $config['path'] . date('/Y/m/d');
        if (mk_dir($destination) === false) {
            return false;  
        }
        $adapter->setDestination($destination);
        $size = new File\Size($config['size']);
        $extension = new File\Extension(array('extension' => $config['extension']));
        $adapter->clearValidators();
        $adapter->setValidators(array($size, $extension));
        $result = array();       
        if ($adapter->isValid()) {
            $files = $adapter->getFileInfo();           
            foreach ($files as $file) {                
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $tmpName = end(explode('\\', $file['tmp_name']));
                } else {
                    $tmpName = end(explode('/', $file['tmp_name']));
                }
                $fileName = str_replace($tmpName . '_', '', $file['name']);
                $target = $file['destination'] . '/' . $config['filename_prefix'] . $fileName;
                $adapter->addFilter('Rename', array( 
                    'target' => $target,
                    'use_upload_name' => true,
                    'randomize' => true
                ));
                if (!$adapter->receive($file['name'])) {           
                    return false; 
                }
            }
            $fileNames = $adapter->getFileName(); 
            if (!is_array($fileNames)) {
                $fileNames = array(key($files) => $fileNames);
            }
            foreach ($fileNames as $fileField => $fileName) { 
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $fileName = end(explode('\\', $fileName));
                } else {
                    $fileName = end(explode('/', $fileName));
                }
                $result[$fileField] = $config['url'] . date('/Y/m/d/') . $fileName;
            }
        } else {
            $result['error'] = $adapter->getMessages();
            Log::warning('Upload error', $result);
        }
        return $result;
    }
    
    /**
     * Upload image from URL
     *  
     * @author thailh   
     * @return array | boolean 
     */
    public static function uploadImageFromUrl($url = '', $maxWidth = 600, $maxHeight = 600, $fileName = '')
    { 
        if (!empty($url)) {          
            $config = Module::getConfig('upload.image');
            $destination = $config['path'] . date('/Y/m/d');
            if (mk_dir($destination) === false) {
                return false;
            }
            $image = app_file_get_contents($url);
            if ($image === false) {
                return null;
            }
            $fileInfo = pathinfo($url);
            if (is_array($fileInfo) && count($fileInfo) >= 4) {
                $ext = strtolower(strrchr($fileInfo["basename"], '.'));
                if (!empty($fileName)) {
                    $fileName = name_2_url($fileName);
                } else {
                    $fileName = $fileInfo["filename"];
                }               
                $newFileName = str_replace($ext, '', $fileName) . '_' . uniqid() . $ext;
                $fileName = $config['filename_prefix'] . $newFileName;                
                $target = $destination . '/' . $fileName;
                if (app_file_put_contents($target, $image) !== false) {                    
                    $image = new \SimpleImage(); 
                    if ($image->load($target)) {
                        if ($image->getWidth() > $maxWidth 
                            || $image->getHeight() > $maxHeight) {
                            $image->maxarea($maxWidth, $maxHeight);
                            $image->save($target);   
                        }
                        return $config['url'] . date('/Y/m/d/') . $fileName;
                    }                    
                }
            }
        }
        return null;
    }

    /**
     * Crypt password
     *  
     * @author thailh   
     * @return array (password = hashed password, salt)
     */
    public static function cryptPassword($password) {
         
        $bcrypt = new Bcrypt(array(
            'salt' => Rand::getString(Bcrypt::MIN_SALT_SIZE)
        ));        
        return array(
            'password' => $bcrypt->create($password),
            'salt' => $bcrypt->getSalt(),
        );
    }
    
    /**
     * Encrypt password
     *  
     * @author thailh   
     * @return boolean
     */
    public static function verifyPassword($password, $hash) {
        $bcrypt = new Bcrypt();
        return $bcrypt->verify($password, $hash);        
    }
    
    /**
     * Get current timezone
     *  
     * @author thailh   
     * @return int
     */
    public static function timezone() {              
        return 7;        
    }
    
    public static function toPrice($value) {              
        return str_replace(array(',', '.'), '', $value); 
    }   
    
    public static function googleShortUrl($longUrl = '') {
        $config = \Application\Module::getConfig('google_urlshortener');
        $url = $config['url'] . '?key=' . $config['key'];            
        $param['longUrl'] = $longUrl;
        $ch = curl_init();     
        $options = array(   
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,              
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SAFE_UPLOAD => false,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($param),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_VERBOSE => false,
            CURLOPT_TIMEOUT => $config['timeout'],
        );
        curl_setopt_array($ch, $options); 
        $jsonResponse = curl_exec($ch);
        $response = json_decode($jsonResponse, true);
        curl_close($ch);            
        if (isset($response['id'])) {            
            return $response['id'];
        }
        return $longUrl;
    }
           
}