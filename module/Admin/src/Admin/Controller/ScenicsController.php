<?php
namespace Admin\Controller;

use Application\Lib\Api;
use Admin\Form\Scenics\ScenicSearchForm;
use Admin\Form\Scenics\ScenicListForm;
use Admin\Form\Scenics\ScenicAddForm;
use Admin\Form\Scenics\ScenicUpdateForm;
use Admin\Form\Scenics\ScenicUpdateLocaleForm;
use Admin\Form\Scenics\ScenicsImageForm;
use Application\Model\Images;
use Application\Model\LocaleStates;
use Application\Module as Config;

class ScenicsController extends AppController
{    
    public function __construct()
    {        
        parent::__construct();        
    }
    
    public function indexAction()
    {
        $param = $this->getParams(array(
            'limit' => Config::getConfig('general.default_limit'),
            'page'  => 1
        ));
        
        // create search form
        $searchForm = new ScenicSearchForm();
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

        // process update on list form
      
        $data = Api::call('url_scenics_lists', $param);
        $listForm = new ScenicListForm();
        $listForm   ->setController($this)
                    ->setDataset($data)
                    ->create();
        
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm'   => $listForm,                
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
        $param = $this->getParams();
                
        // create add/edit form
        $form = new ScenicAddForm();
        if ( ! empty($param['country_code']) ) 
        {
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
        if ( $request->isPost() ) 
        {
            $post = (array) $request->getPost();            
            $form->setData($post);         
            if ($form->isValid() ) { 
                $id = Api::call('url_scenics_add', $post);
                if ( Api::error() ) {
                    $this->addErrorMessage($this->getErrorMessage());
                } 
                else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/scenics', 
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
    

    public function updateAction()
    { 
        $request = $this->getRequest();
        
        $id = $this->params()->fromRoute('id', 0);
        $tab = $this->params()->fromQuery('tab', '');
       
        $locales = Config::getConfig('general.locales');        
        if ( ! isset($locales[$tab]) ) {
            $locale = Config::getConfig('general.default_locale');
        } 
        else {
            $locale = $tab;
        }
        
        // invalid parameters
        if ( empty($id) ) {
            return $this->notFoundAction();
        }
        
        // get place detail             
        $data = Api::call(
            'url_scenics_detail', 
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
                $form = new ScenicUpdateForm();
                if ( ! empty($data['country_code']) ) {
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
                    $data['url_image'] = Images::getUrl($data['image_id'], 'places', true);
                }
                $form->bindData($data);
                
                // save general form
                if ( $request->isPost() ) 
                {
                    $post = (array) $request->getPost(); 
                    $form->setData($post);     
                    if ( $form->isValid() ) {
                        Api::call('url_scenics_update', $post); 
                        if ( ! Api::error() ) {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/scenics', 
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
                $image = Images::getAll($id, 'scenics');
                $form  = new ScenicsImageForm();
                $form->setAttribute('enctype','multipart/form-data')
                    ->setController($this)
                    ->create()
                    ->bindData($image['url_image']);
                
                // save images form
                if ( $request->isPost() ) 
                {
                    $post = (array) $request->getPost(); 
                    $form->setData($post);     
                    if ( $form->isValid() ) 
                    {                             
                        $remove    = array();
                        $update    = array();
                        $maxImages = Config::getConfig('scenics.max_images');
                        for ($i = 1; $i < $maxImages; $i++) {
                            if ( isset($post['remove']['url_image' . $i]  ) 
                                && isset($image['_id']['url_image' . $i]) ) 
                            {
                                $remove[] = $image['_id']['url_image' . $i];
                            }
                            if ( ! empty($image['url_image']['url_image' . $i]) 
                                && !empty($_FILES['url_image' . $i]['name'])) {
                                $update['url_image' . $i] = $image['_id']['url_image' . $i];
                            }
                        }
                        $post['src']    = 'scenics';
                        $post['src_id'] = $id;
                        $post['remove'] = $remove;
                        $post['update'] = $update;
                        Api::call('url_images_add', $post);
                        if ( ! Api::error() ) 
                        {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/scenics', 
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
                $form = new ScenicUpdateLocaleForm();
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
                        Api::call('url_scenics_addupdatelocale', $post); 
                        if (!Api::error()) {
                            $this->addSuccessMessage('Data saved successfully');
                            return $this->redirect()->toRoute(
                                'admin/scenics', 
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
        
        if ( Api::error() ) {
            $this->addErrorMessage($this->getErrorMessage());
        }
        
        return $this->getViewModel(array(
                'form' => $form,
            )
        );        
    }
    
}
