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
use Application\Model\NewsCategories;
use Admin\Form\News\SearchForm;
use Admin\Form\News\ListForm;
use Admin\Form\News\AddForm;
use Admin\Form\News\UpdateForm;
use Admin\Form\News\ImageForm;
use Admin\Form\News\AddFromLinkForm;

class NewsController extends AppController
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
            'sort' => 'title-asc',
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            // on/off news.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_news_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
        }
        
        if (!empty($param['category'])) {            
            NewsCategories::getSubCategories($categories = array(), $lastLevel, $param['category']);
            if (!empty($lastLevel)) {
                $param['category_id'] = implode(',', $lastLevel);
            } else {
                $param['category_id'] = $param['category'];
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
                    ->setDataset(Api::call('url_news_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
     /**
     * Add a News
     *
     * @return Zend\View\Model
     */
    public function addAction()
    {
        $tab = $this->params()->fromQuery('tab', '');
        
        // create add/edit form
        switch ($tab) {
            case 'link':
                $form = new AddFromLinkForm();        
                $form->setBindOnValidate(false)
                     ->setController($this)
                     ->create();           
                break;
            default:
                $form = new AddForm();        
                $form->setAttribute('enctype','multipart/form-data')
                     ->setBindOnValidate(false)
                     ->setController($this)
                     ->create();
        }
        
        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();   
            $form->setData($post);
            if ($form->isValid()) { 
                if (!empty($post['site']) && !empty($post['url'])) {
                    include_once getcwd() . '/include/simple_html_dom.php';	
                    $content = file_get_contents($post['url']);    
                    $html = str_get_html($content);
                    switch ($post['site']) {
                        case 'vnexpress.vn':
                            foreach($html->find('div[class=title_news]') as $element) {
                                if (!empty($element->innertext)) {
                                    $post['title'] = trim(strip_tags($element->innertext));
                                    break;
                                }
                            } 
                            foreach($html->find('div[class=short_intro]') as $element) {
                                $post['short'] = trim(strip_tags_content($element->innertext));
                                break;
                            }
                            foreach($html->find('div[id=article_content]') as $element) {                            
                                $post['content'] = strip_tags_content($element->innertext, '<script><style>', true);
                                break;
                            }
                            if (empty($post['content'])) {
                                foreach($html->find('div[class=fck_detail]') as $element) {
                                    $post['content'] = strip_tags_content($element->innertext, '<script><style>', true);
                                    break;
                                }
                            }
                            // get image from meta tag
                            foreach ($html->find('meta[property=og:image]') as $element) {
                                if (!empty($element->attr['content'])) {
                                    $post['url_image'] = $element->attr['content'];
                                    break;
                                }                                
                            }                                
                            if (empty($post['url_image']) && !empty($post['content'])) {
                                // get first image in content
                                $imageHtml = str_get_html($post['content']);
                                foreach($imageHtml->find('img') as $element) {    
                                    if (!empty($element->src)) {
                                        $post['url_image'] = $element->src;
                                        break;
                                    }
                                }
                            }
                            break;
                    }
                }
                $id = Api::call('url_news_add', $post); 
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/news', 
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
            'url_news_detail', 
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
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'news', true);
                }
                $form->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();                
                    $form->setData($post);     
                    if ($form->isValid()) {
                        if (!empty($post['remove']['url_image'])) {
                            $post['image_id'] = 0;
                        }
                        Api::call('url_news_update', $post);  
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
                
            case 'images':
                // create image form                
                $image = Images::getAll($data['news_id'], 'news');               
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
                        for ($i = 1; $i < \Application\Module::getConfig('news.max_images'); $i++) {
                            if (isset($post['remove']['url_image' . $i]) 
                                && isset($image['image_id']['url_image' . $i])) {
                                $remove[] = $image['image_id']['url_image' . $i];
                            }
                            if (!empty($image['url_image']['url_image' . $i]) 
                                && !empty($_FILES['url_image' . $i]['name'])) {
                                $update['url_image' . $i] = $image['image_id']['url_image' . $i];
                            }
                        }
                        $post['src'] = 'news';
                        $post['src_id'] = $data['news_id'];
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
