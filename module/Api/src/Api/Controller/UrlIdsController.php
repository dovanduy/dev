<?php

namespace Api\Controller;

class UrlidsController extends AppController {   

    public function detailAction()
    {
        return \Api\Bus\UrlIds\Detail::getInstance()->execute(
            $this->getServiceLocator()->get('UrlIds'),
            $this->getParams()
        );
    }

}
