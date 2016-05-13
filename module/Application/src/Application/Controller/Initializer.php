<?php
namespace Application\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class Initializer implements InitializerInterface
{
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {        
        $instance->translator = $serviceLocator->getServiceLocator()->get('translator');
        if ($instance instanceof \stdClass) {
            $instance->initialized = 'initialized!';
        }        
    }
}
