<?php

namespace Admin\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;

class NavigationFactory extends DefaultNavigationFactory
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_navigation';
    }
	
	
}
