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
    
    public function listsAction()
    {
        return \Api\Bus\Vouchers\Lists::getInstance()->execute(
            $this->getServiceLocator()->get('Vouchers'),
            $this->getParams()
        );
    }
    
    public function addAction()
    {
        return \Api\Bus\Vouchers\Add::getInstance()->execute(
            $this->getServiceLocator()->get('Vouchers'),
            $this->getParams()
        );
    }

}
