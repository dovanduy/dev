<?php

namespace Admin\Controller;

use Admin\Lib\Api;
use Admin\Form\Block\AddProductForm;
use Admin\Form\Block\SearchProductForm;
use Admin\Form\Block\ListProductForm;
use Admin\Form\Block\SearchForm;
use Admin\Form\Block\ListForm;
use Admin\Form\Block\AddForm;
use Admin\Form\Block\UpdateForm;

class BlocksController extends AppController
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
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'name-asc',
        ));      
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();              
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_blocks_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
            // update blocks.sort
            if (!empty($post['sort'])) {              
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_blocks_updatesort', $post); 
                if (empty(Api::error())) {
                    if ($request->isXmlHttpRequest()) {
                        echo 'OK';
                        exit;
                    }
                    $this->addSuccessMessage('Data saved successfully');   
                    return $this->redirect()->toUrl($request->getRequestUri());
                }                
            }
        }
        
        // create search form
        $searchForm = new SearchForm();  
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $data = Api::call('url_blocks_lists', $param);
        $listForm = new ListForm();
        $listForm   ->setController($this)
                    ->setAttribute('sortable', true)
                    ->setDataset($data)
                    ->create();
        
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a Admin
     *
     * @return Zend\View\Model
     */
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
                $id = Api::call('url_blocks_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/blocks', 
                        array(
                            'action' => 'update', 
                            'id' => $id
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
    
    /**
     * Update place information
     *
     * @return Zend\View\Model
     */
    public function updateAction()
    { 
        $request = $this->getRequest();
        
        $id = $this->params()->fromRoute('id', 0);
        $tab = $this->params()->fromQuery('tab', '');
        $backUrl = $this->params()->fromQuery('backurl', '');
       
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get news detail             
        $data = Api::call(
            'url_blocks_detail', 
            array(
                '_id' => $id, 
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        switch ($tab) {
            case '':
                // create edit form
                $form = new UpdateForm();                    
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create();               
                $form->bindData($data);
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();  
                    $form->setData($post);
                    if ($form->isValid()) {                       
                        Api::call('url_blocks_update', $post);  
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
                    }                    
                }                
                break;
                
            default:     
                
        }
        
        if (Api::error() || $this->getErrorMessage()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,                
            )
        );
    }  
    
    /**
     * Product list
     *
     * @return Zend\View\Model
     */
    public function productAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }        
               
        $block = Api::call(
            'url_blocks_detail', 
            array(
                '_id' => $id, 
            )
        );
        // not found data
        if (empty($block)) {
            return $this->notFoundAction();
        }
        
        $param = $this->getParams(array(
            'block_id' => $block['block_id'],
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'sort-asc',
        ));
               
        // create add product form
        $addProductForm = new AddProductForm(); 
        $addProductForm->setController($this)
            ->setAttribute('id', 'productForm')
            ->create();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();               
            // add a product   
            if (isset($post['product_id']) && $request->isXmlHttpRequest()) { 
                $addProductForm->setData($post);
                if (!$addProductForm->isValid() && !empty($addProductForm->getMessages())) {
                    die($this->getErrorMessageForAjax($addProductForm->getMessages()));
                } else {
                    $error = array(
                        array(
                            'field' => 'product_id', 
                            'code' => 1011, 
                            'message' => $this->translate('Product not found')
                        ),                        
                    );
                    $param = array(                        
                        'product_id' => $post['product_id'],
                        'block_id' => $block['block_id'],
                    );
                    $result = Api::call('url_blocks_addproduct', $param);                 
                    if (empty(Api::error())) {                        
                        $result = array(
                            'status' => 'OK',
                            'message' => 'Data saved successfully',
                        );
                        die(\Zend\Json\Encoder::encode($result));
                    }
                    die($this->getErrorMessageForAjax(array(), $error));                           
                }
            }  
           
            // update products.sort
            if (!empty($post['sort'])) {
                $post['block_id'] = $block['block_id'];
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_blocks_updatesortproduct', $post); 
                if (empty(Api::error())) {
                    if ($request->isXmlHttpRequest()) {
                        echo 'OK';
                        exit;
                    }
                    $this->addSuccessMessage('Data saved successfully');   
                    return $this->redirect()->toUrl($request->getRequestUri());
                }                
            }
        }
        
        // create search form
        $searchForm = new SearchProductForm();  
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new ListProductForm();
        $listForm   ->setAttribute('sortable', true)
                    ->setController($this)
                    ->setDataset(Api::call('url_products_lists', $param))
                    ->create();
     
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
                'addProductForm' => $addProductForm,                
            )
        );
    }
    
    /**
     * Ajax remove product
     *
     * @return Zend\View\Model
     */
    public function removeproductAction()
    { 
        $request = $this->getRequest(); 
        $blockId = $this->params()->fromQuery('block_id', 0);        
        $productId = $this->params()->fromQuery('product_id', 0);  
        if (!empty($blockId)
            && !empty($productId) 
            && $request->isPost() 
            && $request->isXmlHttpRequest()) {           
            Api::call('url_blocks_removeproduct', array(               
                'block_id' => $blockId,
                'product_id' => $productId
            ));
            if (empty(Api::error())) {                
                $result['status'] = 'OK';
            } else {
                $result['status'] = 'FAIL';
                $result['error'] = $this->getErrorMessage();
            }
            die(\Zend\Json\Encoder::encode($result));
        }
        exit;
    }
    
}
