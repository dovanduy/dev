<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Api;
use Application\Lib\Cache;

class SystemsController extends AppController
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
     * Place list
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    { 
        
    }
    
     /**
     * Delete cache
     *
     * @return Zend\View\Model
     */
    public function deletecacheAction()
    {  
        Cache::flush();
        $this->addSuccessMessage('Cached data delected successfully');
        return $this->redirect()->toRoute('admin');
    }   
    
    /**
     * Refresh data
     *
     * @return Zend\View\Model
     */
    public function refreshAction()
    {  
        $ok = Api::call(
            'url_websites_refresh', 
            array()
        );
        if ($ok) {
            $this->addSuccessMessage('Data refreshed successfully');
        } else {
            $this->addSuccessMessage('System error');
        }
        return $this->redirect()->toRoute('admin');
    } 
    
}
