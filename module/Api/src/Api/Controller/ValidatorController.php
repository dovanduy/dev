<?php

namespace Api\Controller;
use Application\Lib\Log;
use Application\Lib\Util;
use Application\Lib\Arr;

class ValidatorController extends AppController {   

    public function duplicateEmailAction() {     
        return \Api\Bus\Validator\DuplicateEmail::getInstance()->execute(
            $this->getServiceLocator()->get('Users'),
            $this->getParams()
        );
    }

}
