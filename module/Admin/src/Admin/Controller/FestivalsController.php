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
use Application\Model\LocaleStates;
use Admin\Form\Festival\FestivalSearchForm;
use Admin\Form\Festival\FestivalListForm;
use Admin\Form\Festival\FestivalUpdateLocaleForm;
use Admin\Form\Festival\FestivalUpdateForm;
use Admin\Form\Festival\FestivalAddForm;

/**
 * FestivalsController
 *
 * @package 	Admin\Controller
 * @created 	2015-09-16
 * @version     1.0
 * @author      caolp
 * @copyright   YouGo INC
 */
class FestivalsController extends AppController
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
     * Festival list
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit')
        ));
        
        // create search form
        $searchForm = new FestivalSearchForm();
        if (!empty($param['country_code'])) {
            // set data for dropdown state_code
            $searchForm->setElementOptions(array(
                'state_code' => array(
                    'value_options' => LocaleStates::getAll($param['country_code'])
                )
            ));
        }
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new FestivalListForm();
        $listForm   ->setController($this)
                    ->setDataset(Api::call('url_festivals_lists', $param))
                    ->create();
        
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a Festival
     *
     * @return Zend\View\Model
     */
    public function addAction()
    {
        $param = $this->getParams();
                
        // create add/edit form
        $form = new FestivalAddForm();
        if (!empty($param['country_code'])) {
            // set data for dropdown state_code     
            $form->setElementOptions(array(
                'state_code' => array(
                    'value_options' => LocaleStates::getAll($param['country_code'])
                )
            ));
        }
        $form->setAttribute('enctype','multipart/form-data')
             ->setController($this)
             ->create();
        
        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();            
            $form->setData($post);         
            if ($form->isValid()) { 
                $id = Api::call('url_festivals_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/festivals', 
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
     * Update festival information
     *
     * @return Zend\View\Model
     */
    public function updateAction()
    { 
        $request = $this->getRequest();
        
        $id = $this->params()->fromRoute('id', 0);
        $tab = $this->params()->fromQuery('tab', '');
       
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
        
        // get festival detail             
        $data = Api::call(
            'url_festivals_detail', 
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
                $form = new FestivalUpdateForm();
                if (!empty($data['country_code'])) {
                    // set data for dropdown state_code
                    $form->setElementOptions(array(
                        'state_code' => array(
                            'value_options' => LocaleStates::getAll($data['country_code'])
                        )
                    ));
                }
                $form->setAttribute('enctype','multipart/form-data')
                     ->setController($this)
                     ->create();
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'festivals', true);
                }
                $form->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost(); 
                    $form->setData($post);     
                    if ($form->isValid()) {
                        Api::call('url_festivals_update', $post); 
                        if (!Api::error()) {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/festivals', 
                                array(
                                    'action' => 'update', 
                                    'id' => $id
                                )                               
                            );
                        }
                    }
                }                
                break;
                
            case 'images':
                // create image form
                $image = Images::getAll($id, 'festivals');
                $form = new FestivalImageForm();
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
                        for ($i = 1; $i < \Application\Module::getConfig('festivals.max_images'); $i++) {
                            if (isset($post['remove']['url_image' . $i]) 
                                && isset($image['_id']['url_image' . $i])) {
                                $remove[] = $image['_id']['url_image' . $i];
                            }
                            if (!empty($image['url_image']['url_image' . $i]) 
                                && !empty($_FILES['url_image' . $i]['name'])) {
                                $update['url_image' . $i] = $image['_id']['url_image' . $i];
                            }
                        }
                        $post['src'] = 'festivals';
                        $post['src_id'] = $id;
                        $post['remove'] = $remove;
                        $post['update'] = $update;
                        Api::call('url_images_add', $post);
                        if (!Api::error()) {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/festivals', 
                                array(
                                    'action' => 'update', 
                                    'id' => $id
                                ), 
                                array(
                                    'query' => array(
                                        'tab' => $tab
                                    )
                                )
                            );
                        }
                    }
                }
                break;
                
            default:
                // create add/edit locale form
                $form = new FestivalUpdateLocaleForm();
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
                        Api::call('url_festivals_addupdatelocale', $post); 
                        if (!Api::error()) {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/festivals', 
                                array(
                                    'action' => 'update', 
                                    'id' => $id
                                ),
                                array(
                                    'query' => array(
                                        'tab' => $tab
                                    )
                                )
                            );
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
