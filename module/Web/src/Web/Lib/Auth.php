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
     * @param  Adapter\AdapterInterface $adapter
     * @return Result
     * @throws Exception\RuntimeException
     */
    public function authenticate($email = null, $password = null)
    {
        if (empty($email) || empty($password)) {
            throw new Exception\RuntimeException('Email and password is required');
        }
        
        /**
         * ZF-7546 - prevent multiple successive calls from storing inconsistent results
         * Ensure storage has clean state
         */
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }
        
        $user = Api::call('url_admins_login', array(
            'email' => $email,
            'password' => $password,
        ));
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
