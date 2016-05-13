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
use Application\Model\Images;
use Admin\Form\InputField\SearchForm;
use Admin\Form\InputField\ListForm;
use Admin\Form\InputField\AddForm;
use Admin\Form\InputField\UpdateForm;
use Admin\Form\InputField\UpdateLocaleForm;
use Admin\Form\InputField\OptionForm;
use Admin\Form\InputField\UpdateOptionForm;

class InputfieldsController extends AppController
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
            'locale' => \Application\Module::getConfig('general.default_locale'),
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            
            // on/off input_fields.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_inputfields_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
            
            // update input_fields.name + input_fields.sort
            if (!empty($post['name']) || !empty($post['sort'])) {  
                $post['locale'] = $param['locale']; 
                $post['name'] = \Zend\Json\Encoder::encode($post['name']);
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_inputfields_save', $post); 
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
                    ->setDataset(Api::call('url_inputfields_lists', $param))
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
                $id = Api::call('url_inputfields_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/inputfields', 
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
            'url_inputfields_detail', 
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
                $form = new UpdateForm();                 
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create();
                $data['locale'] = $locale;
                $form->bindData($data);
                
                if (count($locales) == 1) {
                    // Option list form
                    $optionForm = new OptionForm();
                    $optionForm->setController($this)
                            ->setAttribute('sortable', true)
                            ->setDataset(!empty($data['options']) ? $data['options'] : array())
                            ->create();
                    
                    $updateOptionForm = new UpdateOptionForm();
                    $updateOptionForm->setController($this)
                        ->create();
                }
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();       
                    $post['field_id'] = !empty($data['field_id']) ? $data['field_id'] : 0; 
                    $post['locale'] = $locale; 
                    
                    // on/off input_options.active
                    if ($request->isXmlHttpRequest()) {
                        Api::call(
                            'url_inputoptions_onoff', 
                            $post
                        );
                        echo 'OK';
                        exit;
                    }
                    
                    if (isset($post['addOption']) && isset($updateOptionForm)) {                        
                        $updateOptionForm->setData($post);
                        if ($updateOptionForm->isValid()) {
                            Api::call('url_inputoptions_add', $post); 
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');   
                                return $this->redirect()->toUrl($request->getRequestUri());
                            }
                        }                    
                    } elseif (isset($post['saveOption']) && isset($optionForm)) {    
                        $optionForm->setData($post);
                        if ($optionForm->isValid()) {
                            $post['name'] = \Zend\Json\Encoder::encode($post['name']);
                            $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                            Api::call('url_inputoptions_save', $post); 
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');   
                                return $this->redirect()->toUrl($request->getRequestUri());
                            }
                        } 
                    } else {
                        $form->setData($post);  
                        if ($form->isValid()) {                       
                            Api::call('url_inputfields_update', $post);  
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');
                                if (isset($post['saveAndBack']) && $backUrl) {
                                    return $this->redirect()->toUrl(base64_decode($backUrl));
                                }
                                return $this->redirect()->toUrl($request->getRequestUri());
                            }
                        }                    
                    }                    
                }                
                break;
              
            default:     
                $data['locale'] = $locale;
                // create add/edit locale form
                $form = new UpdateLocaleForm();
                $form->setController($this)
                     ->create()
                     ->bindData($data); 
                    
                // Option list form
                $optionForm = new OptionForm();
                $optionForm->setController($this)
                        ->setAttribute('sortable', true)
                        ->setDataset(!empty($data['options']) ? $data['options'] : array())
                        ->create();
                
                $updateOptionForm = new UpdateOptionForm();
                $updateOptionForm->setController($this)
                     ->create();
                    
                // save locale form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();
                    $post['field_id'] = !empty($data['field_id']) ? $data['field_id'] : 0; 
                    $post['locale'] = $locale;    
                    
                    // on/off input_options.active
                    if ($request->isXmlHttpRequest()) {
                        Api::call(
                            'url_inputoptions_onoff', 
                            $post
                        );
                        echo 'OK';
                        exit;
                    }
                    
                    if (isset($post['addOption']) && isset($updateOptionForm)) {                        
                        $updateOptionForm->setData($post);
                        if ($updateOptionForm->isValid()) {
                            Api::call('url_inputoptions_add', $post); 
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');   
                                return $this->redirect()->toUrl($request->getRequestUri());
                            }
                        }                    
                    } elseif (isset($post['saveOption']) && isset($optionForm)) {    
                        $optionForm->setData($post);
                        if ($optionForm->isValid()) {
                            $post['name'] = \Zend\Json\Encoder::encode($post['name']);
                            $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                            Api::call('url_inputoptions_save', $post); 
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');   
                                return $this->redirect()->toUrl($request->getRequestUri());
                            }
                        } 
                    } else {
                        $form->setData($post);     
                        if ($form->isValid()) { 
                            Api::call('url_inputfields_addupdatelocale', $post); 
                            if (empty(Api::error())) {
                                $this->addSuccessMessage('Data saved successfully');
                                if (isset($post['saveAndBack']) && $backUrl) {
                                    return $this->redirect()->toUrl(base64_decode($backUrl));
                                }
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
                'updateOptionForm' => isset($updateOptionForm) ? $updateOptionForm : null,
                'optionForm' => isset($optionForm) ? $optionForm : null,
            )
        );       
    }  
    
}
