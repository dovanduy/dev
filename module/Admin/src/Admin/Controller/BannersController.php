<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;


use Application\Model\Images;
use Application\Model\Banners;
use Admin\Form\Banner\SearchForm;
use Admin\Form\Banner\ListForm;
use Admin\Form\Banner\AddForm;
use Admin\Form\Banner\UpdateForm;
use Admin\Form\Banner\UpdateLocaleForm;
use Admin\Form\Banner\ImageForm;
use Admin\Lib\Api;

class BannersController extends AppController
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
            'sort' => 'sort-asc',            
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            
            // on/off banners.active
            if (isset($post['_id']) && isset($post['value'])) {                 
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_banners_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
            
            // update banners.sort
            if (!empty($post['sort'])) {  
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_banners_updatesort', $post); 
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
        $listForm = new ListForm();
        $listForm   ->setAttribute('sortable', true)
                    ->setController($this)
                    ->setDataset(Api::call('url_banners_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a Place
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
                $id = Api::call('url_banners_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/banners', 
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
       
        $locales = \Application\Module::getConfig('general.locales');        
        if (!isset($locales[$tab])) {
            $locale = \Application\Module::getConfig('general.default_locale');
        } else {
            $locale = $tab;
        }
        
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get place detail             
        $data = Api::call(
            'url_banners_detail', 
            array(
                '_id' => $id, 
                'locale' => $locale
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        switch ($tab) {
            case '':
                // create edit form
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'banners', true);
                }
                $data['locale'] = $locale;
                $form = new UpdateForm();                 
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create()
                     ->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();          
                    $form->setData($post);
                    if ($form->isValid()) {
                        if (!empty($post['remove']['url_image'])) {
                            $post['image_id'] = 0;
                        }
                        Api::call('url_banners_update', $post);  
                        if (empty(Api::error())) {
                            Banners::removeCache();
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                            return $this->redirect()->toUrl($request->getRequestUri());
                        }
                    }                    
                }                
                break;
                
            case 'images':
                // create image form                
                $image = Images::getAll($data['banner_id'], 'banners');              
                $form = new ImageForm();
                $form->setAttribute('enctype','multipart/form-data')
                    ->setController($this)
                    ->create()
                    ->bindData($image['url_image']);
                
                // save images form
                if ($request->isPost()) {
                    $post = (array) $request->getPost(); 
                    $form->setData($post);     
                    if ($form->isValid()) {                             
                        $remove = array();
                        $update = array();
                        for ($i = 1; $i < \Application\Module::getConfig('banners.max_images'); $i++) {
                            if (isset($post['remove']['url_image' . $i]) 
                                && isset($image['image_id']['url_image' . $i])) {
                                $remove[] = $image['image_id']['url_image' . $i];
                            }
                            if (!empty($image['url_image']['url_image' . $i]) 
                                && !empty($_FILES['url_image' . $i]['name'])) {
                                $update['url_image' . $i] = $image['image_id']['url_image' . $i];
                            }
                        }
                        $post['src'] = 'banners';
                        $post['src_id'] = $data['banner_id'];
                        $post['remove'] = $remove;
                        $post['update'] = $update;
                        Api::call('url_images_add', $post);
                        if (empty(Api::error())) {
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                            return $this->redirect()->toUrl($request->getRequestUri());
                        }
                    }
                }
                break;
                
            default:     
                
                // create add/edit locale form
                $form = new UpdateLocaleForm();
                $form->setController($this)
                     ->create();
                if (!empty($data)) {
                    $data['locale'] = $locale;
                    $form->bindData($data);                    
                }
                
                // save locale form
                if ($request->isPost()) {
                    $post = (array) $request->getPost(); 
                    $form->setData($post);     
                    if ($form->isValid()) { 
                        Api::call('url_banners_addupdatelocale', $post); 
                        if (empty(Api::error())) {
                            Banners::removeCache();
                            $this->addSuccessMessage('Data saved successfully');
                            if (isset($post['saveAndBack']) && $backUrl) {
                                return $this->redirect()->toUrl(base64_decode($backUrl));
                            }
                        }
                    }                    
                }
                break;
        }
        
        if (Api::error()) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );       
    }  
    
}
