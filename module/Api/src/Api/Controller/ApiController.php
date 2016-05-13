<?php

namespace Api\Controller;
use Application\Lib\Log;
use Application\Lib\Util;
use Application\Lib\Arr;
use Zend\Http\PhpEnvironment\Request;

class ApiController extends AppController {   

    public function indexAction() {        
       
    }
    
    public function onoffAction()
    {
        return \Api\Bus\Common\OnOff::getInstance()->execute(
            $this->getServiceLocator()->get('Places'),
            $this->getParams()
        );
    }

}
