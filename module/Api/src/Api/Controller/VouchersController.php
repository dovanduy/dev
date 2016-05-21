<?php

namespace Api\Controller;

class VouchersController extends AppController {
    
    public function __construct()
    {
        
    }
    
    public function checkAction()
    {
        return \Api\Bus\Vouchers\Check::getInstance()->execute(
            $this->getServiceLocator()->get('Vouchers'),
            $this->getParams()
        );
    }

}
