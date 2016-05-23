<?php
namespace Application\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\AppController;

class AppControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator);
    {
        $controller = new BaseController($serviceLocator->getServicelocator());
        return $controller;
    }
}

?>