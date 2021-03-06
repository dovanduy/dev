<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Model\UrlIds;
use Web\Model\Pages;

class PagesController extends AppController
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
     * 
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {  
        $this->setHead(array(
            'title' => $pageDetail['title']
        ));
        $param = $this->getParams(array(                      
            'force' => 0,            
        ));
        $urlName = $this->params()->fromRoute('name', '');
        UrlIds::getDetail($urlName, $categoryId, $brandId, $productId, $optionId, $id);        
        if (empty($id)) {
            return $this->notFoundAction();
        }
        if (isset($param['force']) && $param['force'] == 1) {            
            Pages::removeCache($id);
        }
        $pageDetail = Pages::getDetail($id);        
        if (empty($pageDetail)) {
            return $this->notFoundAction();
        }
        $this->setHead(array(
            'title' => $pageDetail['title']
        )); 
        $navigationPage = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_pages_index');
        if (!empty($navigationPage)) {
            $navigationPage->setLabel('');
            $navigationPage->addPage(array( 
                'uri' => '',
                'label' => $pageDetail['title'],
                'active' => true
            ));
        }        
        return $this->getViewModel(array(
                'pageDetail' => $pageDetail,
                'pages' => Pages::getAll(),
            )
        );
    }
    
    /**
     * 
     *
     * @return Zend\View\Model
     */
    public function paymentAction()
    { 
        $request = $this->getRequest(); 
        return $this->getViewModel(array(
                             
            )
        );
    }  
    
    
}
