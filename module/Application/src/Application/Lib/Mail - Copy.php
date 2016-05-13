<?php

namespace Application\Lib;

/**
 * Mail
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Mail {
    
    protected static $transport;
    
    /**
     * Return the \Zend\Mail\Transport\Smtp instance
     *     
     * @author 	thailh
     * @return	\Zend\Mail\Transport\Smtp
     */    
	public static function instance()
	{  
        static::$transport or static::_init(static::$transport);
        return static::$transport;
	}
    
    /**
     * Initialize the class
     *  
     * @author 	thailh 
     * @return	void
     */      
	public static function _init()
	{        
        $config = \Application\Module::getConfig('email.smtp');
        if (empty($config['username']) || empty($config['password'])) {
            throw new \Exception('Username|password is invalid');   
        }
        $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();  
        $smtpOptions
            ->setHost($config['host'])
            ->setPort($config['port'])
            ->setConnectionClass('login')
            ->setName($config['name'])
            ->setConnectionConfig(array(
                'username' => $config['username'],
                'password' => $config['password'],
                'ssl' => $config['ssl'],
            ));
        static::$transport = new \Zend\Mail\Transport\Smtp($smtpOptions);
    }
    
    /**
     * Send email
     *  
     * @author thailh   
     * @return int
     */
    public static function send($param) {         
        if (empty($param['from_email'])) {
            $param['from_email'] = \Application\Module::getConfig('email.from_email');
        }
        if (empty($param['from_name'])) {
            $param['from_name'] = \Application\Module::getConfig('email.from_name');
        }
        

        $message = new \Zend\Mail\Message();
        $message->setFrom($param['from_email'], $param['from_name']);
        $message->addTo($param['to']);
        $message->setBody($param['body']);      
        $message->setSubject($param['subject']); 
        static::instance()->send($message);
    }
           
}