<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Model\Products;

class IndexController extends AppController
{
    public function indexAction()
    {        
        $blocks = Products::homepage();
        return $this->getViewModel(array(
                'blocks' => $blocks
            )
        );
    }    
    
}
