<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Arr;
use Application\Lib\Util;
use Web\Lib\Api;
use Web\Form\Product\ReviewForm;
use Web\Form\Product\CopyForm;
use Web\Model\ProductCategories;
use Web\Model\Websites;
use Web\Model\Products;
use Web\Model\UrlIds;
use Web\Module as WebModule;

class F5sController extends AppController
{    
    /**
     * construct
     * 
     */
    public function __construct()
    {        
        parent::__construct();        
    }
    
    /**
     * Product list
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {        
        $data = [];
        return $this->getViewModel(array(
                'data' => $data,               
            )
        );  
    } 
    
    
    
}