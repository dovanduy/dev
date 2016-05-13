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
use Web\Model\ProductCategories;
use Web\Model\Brands;
use Web\Model\Banners;

class IndexController extends AppController
{
    public function indexAction()
    {        
        $blocks = Products::homepage();   
        $request = $this->getRequest();        
        return $this->getViewModel(array(
                'blocks' => $blocks, 
                'featuredBrands' => Brands::getAll(1),  
                'banners' => Banners::getAll(),                  
                'categories' => ProductCategories::getSubCategories(array(), $lastLevel, 0, false),
            )
        );
    }    
    
}
