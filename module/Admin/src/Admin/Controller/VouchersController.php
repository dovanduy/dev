<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Application\Lib\Arr;
use Admin\Lib\Api;
use Admin\Form\Voucher\SearchForm;
use Admin\Form\Voucher\ListForm;
use Admin\Form\Voucher\AddForm;
use Admin\Form\Voucher\UpdateForm;

class VouchersController extends AppController
{    
     /**
     * construct
     * 
     */
    public function __construct()
    {        
        parent::__construct();        
    }
    
    public function indexAction()
    {
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'created-desc',            
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            // on/off users.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_vouchers_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
        }
        
        // create search form
        $searchForm = new SearchForm(); 
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new ListForm();
        $listForm   ->setController($this)
                    ->setDataset(Api::call('url_vouchers_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
    public function addAction()
    {
        // create add/edit form
        $form = new AddForm();        
        $form->setAttribute('enctype','multipart/form-data')
             ->setController($this)
             ->create();
        
        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();
            $form->setData($post);         
            if ($form->isValid()) {           
                $vouncherConfig = \Application\Module::getConfig('voucher');                
                $post = array_merge($post, $vouncherConfig);
                $id = Api::call('url_vouchers_add', $post);                
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/vouchers', 
                        array(                          
                        )
                    );
                }
            }
        }        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );
    }    
    
}
