<?php

namespace OAuth2\Controller;

use OAuth2;
use Application\Lib\Log;

class OAuth2Controller extends AppController {

    protected $server;
        
    public function _init()
	{ 
        if (!empty($this->server)) {
            return $this->server;
        }

        $config = $this->getServiceLocator()->get('Config');
        $dsn = $config['db_oauth2']['dsn'];
        $username = $config['db_oauth2']['username'];
        $password = $config['db_oauth2']['password'];

        // Autoloading (composer is preferred, but for this example let's just do this)
        //require_once('oauth2-server-php/src/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new OAuth2\Storage\Pdo(array(
                'dsn' => $dsn, 
                'username' => $username, 
                'password' => $password
            )
        );

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new OAuth2\Server($storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
        
        return $this->server;
    }
    
    public function indexAction() {
        echo 'Welcome to OAuth2';
        exit;
    }

    public function tokenAction() { 
        $this->_init();
        $request = OAuth2\Request::createFromGlobals();
        Log::info('Request token', $request->request);        
        $this->server->handleTokenRequest($request)->send();
        exit;
    }
    
    public function authorizeAction() {
        $this->_init();
        $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
        exit;
    }
    
    public function resourceAction() {
        $this->_init();
        // Handle a request to a resource and authenticate the access token
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->server->getResponse()->send();
            die;
        }
        echo json_encode(array('success' => true, 'message' => 'You accessed my APIs!'));
        exit;
    }
    
}
