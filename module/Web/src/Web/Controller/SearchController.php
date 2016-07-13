<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Lib\Api;
use Web\Model\Products;
use Web\Module as WebModule;

class SearchController extends AppController
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
     * Search products
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {
        $param = $this->getParams(array(            
            'page' => 1,
            'limit' => 20,
            'keyword' => $this->params()->fromRoute('q', '')
        ));
        $result = Products::search($param);         
        $id = 'web_index_index';
        $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);
        if (!empty($page)) {
            $page->setLabel($this->translate('Search result by keyword') . ' <strong>' . $param['keyword'] . '</strong> (<strong>' . $result['count'] . '</strong> ' . $this->translate('result') . ')');
            $page->setActive(true);            
        }
        $this->setHead(array(
            'title' => $this->translate('Search result by keyword') . ' ' . $param['keyword']           
        ));
        return $this->getViewModel(array(
                'params' => $this->params()->fromQuery(),
                'keyword' => $param['keyword'],
                'result' => $result,                
            )
        ); 
    }
    
     /**
     * Search products
     *
     * @return Zend\View\Model
     */
    public function isduplicatecodeAction()
    {
        $param = $this->getParams(array(            
            'page' => 1,
            'limit' => 100,            
        ));
        $param['keyword'] = 'Duplicate code';
        $result = Products::getAllDuplicateCode($param);
        $result = [
            'data' => $result,
            'limit' => count($result),
            'count' => count($result),            
        ];
        $id = 'web_index_index';
        $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);
        if (!empty($page)) {
            $page->setLabel($this->translate('Search result by keyword') . ' <strong>' . $param['keyword'] . '</strong> (<strong>' . count($result['data']) . '</strong> ' . $this->translate('result') . ')');
            $page->setActive(true);            
        }
        $this->setHead(array(
            'title' => $this->translate('Search result by keyword') . ' ' . $param['keyword']           
        ));
        return $this->getViewModel(array(
                'params' => $this->params()->fromQuery(),
                'result' => $result,                
            )
        ); 
    }
    
}