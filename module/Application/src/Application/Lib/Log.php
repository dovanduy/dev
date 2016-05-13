<?php

namespace Application\Lib;

/**
 * Write logs
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Log {
    
    protected static $zlog; // for application module 
    protected static $zlogApi; // for api module
    protected static $zlogAdmin; // for admin module
    protected static $zlogOAuth2; // for oauth2 module
        
    /**
     * Return the Zend\Log\Logger instance
     *     
     * @author 	thailh
     * @return	Zend\Log\Logger
     */    
	public static function instance()
	{  
        $domain = domain();
        switch ($domain) {
            case 'admin':
                // make sure we have an instance
                static::$zlogAdmin or static::_init(static::$zlogAdmin, $domain);
                return static::$zlogAdmin;                
            case 'api':
                // make sure we have an instance
                static::$zlogApi or static::_init(static::$zlogApi, $domain);
                return static::$zlogApi;    
            case 'oauth2':
                // make sure we have an instance
                static::$zlogOAuth2 or static::_init(static::$zlogOAuth2, $domain);
                return static::$zlogOAuth2;              
            default:
                // make sure we have an instance
                static::$zlog or static::_init(static::$zlog, $domain);
                return static::$zlog;
        }
	}
    
    /**
     * Initialize the class
     *  
     * @author 	thailh 
     * @return	void
     */      
	public static function _init(&$zlog, $domain = '')
	{        
        $config = \Application\Module::getConfig('log');       
        $zlog = new \Zend\Log\Logger();
        if (!isset($config['path'])) {
            $config['path'] = getcwd() . '/data/log';
        }       
        $destination = $config['path'];
        if (!empty($domain)) {
            $destination .= '/' . $domain;
        }
        $destination .= '/' . date('/Y/m/');
        if (mk_dir($destination) === false) {
            throw new \Exception('Make directory error');            
        }
        $filename = $destination  .date('d') . '.txt';                      
        $writer = new \Zend\Log\Writer\Stream($filename);
        $format = '%timestamp% - %priorityName% - %message%'; // . PHP_EOL;                
        $formatter = new \Zend\Log\Formatter\Simple($format);
        $writer->setFormatter($formatter);       
        $zlog->addWriter($writer);
    }
    
    /**
     * Compile message and data to string 
     *    
     * @author 	thailh
     * @param   string  $msg     message     
     * @param   array   $data    data array (input params or response)     
     * @return	string  $message     
     */ 
    private static function message($msg, $data = null, $method = null)
    {      
        //return $msg . ' - ' . json_encode($data);
        if (!empty($method)) {
			$msg = $msg . ' - ' . $method;
		} 
        if (empty($data)) {
			return $msg;
		}
		if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $msg .= "- $key: " .
                        html_entity_decode(self::mojibake($value, TRUE), ENT_NOQUOTES, 'UTF-8');
                } else {
                    $msg .= " $key: " . self::mojibake($value, FALSE);
                }
            }
        } elseif (is_object($data)) {
			$msg = "{$msg} - " . self::mojibake($data, TRUE);
		} else {
			$msg = "{$msg} - " . self::mojibake($data, FALSE);
		}
        return $msg;
    }
    
    /**
     * Encode data log     
     * @param       array|object|string.. $data
     * @param       json $isjson
     * @return      String
     */
    private static function mojibake($data, $isjson = false) 
    {
        if ($isjson) {
            return preg_replace("/\\\\u([0-9A-Fa-f]{4})/u", "&#x\\1;", @json_encode($data));
        } else {
            return preg_replace("/\\\\u([0-9A-Fa-f]{4})/u", "&#x\\1;", $data);
        }
    }
    
    public static function info($message, $data = null, $method = null) 
    {
        $message = static::message($message, $data, $method);
        return static::instance()->info($message);
    }

    /**
     * Log warning
     *    
     * @author 	thailh
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE     if write log success ELSE FALSE     
     */   
	public static function warning($message, $data = null, $method = null) 
    {
        $message = static::message($message, $data, $method);
        return static::instance()->warn($message);
    }

    /**
     * Log error
     *    
     * @author 	thailh
     * @param   string  $msg     Log message
     * @param   string  $method  method that logged
     * @param   array   $data    log more data (input params or response)
     * @param   bool    $console show on console screen
     * @return	boolean TRUE     if write log success ELSE FALSE     
     */   
	public static function error($message, $data = null, $method = null) 
    {
        $message = static::message($message, $data, $method);
        return static::instance()->err($message);
    }

}

