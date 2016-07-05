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
use Web\Model\Websites;
use Web\Module as WebModule;

class IndexController extends AppController
{
    public function indexAction()
    {
        $this->setHead(array(
            'title' => WebModule::getConfig('meta_head.title')
        ));
        $param = $this->getParams(array(                      
            'force' => 0,            
        ));
        if (isset($param['force']) && $param['force'] == 1) {
            $wesiteModel = new Websites;
            $wesiteModel->removeCache();
        }
        $blocks = Products::homepage($param);
        return $this->getViewModel(array(
                'blocks' => $blocks
            )
        );
    }    
    
}
