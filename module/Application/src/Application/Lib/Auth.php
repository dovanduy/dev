<?php

namespace Application\Lib;

use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Result;

/**
 * Authenticate login
 *
 * @package Lib
 * @created 2015-09-05
 * @version 1.0
 * @author thailh
 * @copyright YouGO INC
 */
class Auth implements AuthenticationServiceInterface
{
    /**
     * Persistent storage handler
     *
     * @var Storage\StorageInterface
     */
    protected $storage = null;

    /**
     * Authentication adapter
     *
     * @var Adapter\AdapterInterface
     */
    protected $adapter = null;

    /**
     * Constructor
     *
     * @param  Storage\StorageInterface $storage
     */
    public function __construct(Storage\StorageInterface $storage = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }        
    }   

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session());
        }

        return $this->storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Storage\StorageInterface $storage
     * @return AuthenticationService Provides a fluent interface
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Authenticates against the supplied adapter
     *
     * @param  String $email
     * @param  String $password
     * @param  String $type admin|web|facebook
     * @return Result
     * @throws Exception\RuntimeException
     */
    public function authenticate($email = null, $password = null, $type = 'admin', $socialParam = array())
    {
        if ((empty($email) || empty($password)) && empty($socialParam)) {
            throw new Exception\RuntimeException('Email and password is required');
        }
        
        /**
         * ZF-7546 - prevent multiple successive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }
        if ($type == 'admin') {
            $user = Api::call('url_admins_login', array(
                'email' => $email,
                'password' => $password,
            ));
        } elseif ($type == 'facebook') {
            $param = array(                                       
                'facebook_id' => $socialParam['id'],                            
                'facebook_email' => $socialParam['email'],
                'facebook_name' => !empty($socialParam['name']) ? $socialParam['name'] : '',
                'facebook_username' => !empty($socialParam['username']) ? $socialParam['username'] : '',               
                'facebook_first_name' => !empty($socialParam['first_name']) ? $socialParam['first_name'] : '',               
                'facebook_last_name' => !empty($socialParam['last_name']) ? $socialParam['last_name'] : '',               
                'facebook_link' => !empty($socialParam['link']) ? $socialParam['link'] : '',                            
                'facebook_gender' => !empty($socialParam['gender']) ? $socialParam['gender'] : '',               
                'access_token' => !empty($socialParam['accessToken']) ? $socialParam['accessToken'] : '',               
                'access_token_expires_at' => !empty($socialParam['access_token_expires_at']) ? $socialParam['access_token_expires_at'] : null,               
            );            
            if (!empty($socialParam['generate_voucher'])) {               
                $param['generate_voucher'] = $socialParam['generate_voucher']; 
                $param['voucher_amount'] = $socialParam['voucher_amount']; 
                $param['voucher_type'] = $socialParam['voucher_type']; 
                $param['voucher_expired'] = $socialParam['voucher_expired']; 
                $param['send_email'] = $socialParam['send_email'];                 
            }
            $user = \Web\Lib\Api::call('url_users_fblogin', $param);
            if (!empty($user['access_token'])) {
                $user['fb_access_token'] = $user['access_token'];
                $user['fb_access_token_expires_at'] = $user['access_token_expires_at'];
            }
        } elseif ($type == 'google') {
            $param = array(                                       
                'google_id' => $socialParam['id'],                            
                'google_email' => $socialParam['email'],
                'google_name' => !empty($socialParam['name']) ? $socialParam['name'] : '',
                'google_username' => !empty($socialParam['username']) ? $socialParam['username'] : '',               
                'google_first_name' => !empty($socialParam['givenName']) ? $socialParam['givenName'] : '',               
                'google_last_name' => !empty($socialParam['familyName']) ? $socialParam['familyName'] : '',               
                'google_link' => !empty($socialParam['link']) ? $socialParam['link'] : '',                            
                'google_gender' => !empty($socialParam['gender']) ? $socialParam['gender'] : '',               
                'google_image' => !empty($socialParam['picture']) ? $socialParam['picture'] : '',               
                'access_token' => !empty($socialParam['accessToken']) ? $socialParam['accessToken'] : '',               
                'access_token_expires_at' => !empty($socialParam['access_token_expires_at']) ? $socialParam['access_token_expires_at'] : null,               
            );            
            if (!empty($socialParam['generate_voucher'])) {               
                $param['generate_voucher'] = $socialParam['generate_voucher']; 
                $param['voucher_amount'] = $socialParam['voucher_amount']; 
                $param['voucher_type'] = $socialParam['voucher_type']; 
                $param['voucher_expired'] = $socialParam['voucher_expired']; 
                $param['send_email'] = $socialParam['send_email'];                 
            }
            $user = \Web\Lib\Api::call('url_users_glogin', $param);
            if (!empty($user['access_token'])) {
                $user['google_access_token'] = $user['access_token'];
                $user['google_access_token_expires_at'] = $user['access_token_expires_at'];
            }
        } else {
            $user = \Web\Lib\Api::call('url_users_login', array(
                'email' => $email,
                'password' => $password,
            ));
        } 
        $result = array();
        if (!empty($user)) {            
            $oauth2Result = Api::callOAuth2('url_oauth2_token', array('grant_type' => 'client_credentials'));
            if (!empty($oauth2Result['access_token'])) {
                if (isset($user['password'])) {
                    unset($user['password']);
                }
                if (isset($user['hash_password'])) {
                    unset($user['hash_password']);
                }
                if (empty($user['display_name'])) {
                    $user['display_name'] = $user['name'];
                }
                $user['access_token'] = $oauth2Result['access_token'];
                $result = new Result(Result::SUCCESS, $user);
                if ($result->isValid()) {
                    $this->getStorage()->write((object) $result->getIdentity());
                }
            }
        }
        return $result;
    }

    /**
     * Returns true if and only if an identity is available from storage
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return !$this->getStorage()->isEmpty();
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        $storage = $this->getStorage();

        if ($storage->isEmpty()) {
            return;
        }

        return $storage->read();
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->getStorage()->clear();
    }
}
