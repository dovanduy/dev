<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Admin\Lib\Api;
use Application\Model\Images;
use Application\Model\ProductCategories;
use Application\Model\ProductSizes;
use Admin\Form\Product\SearchForm;
use Admin\Form\Product\ListForm;
use Admin\Form\Product\AddForm;
use Admin\Form\Product\UpdateForm;
use Admin\Form\Product\UpdateLocaleForm;
use Admin\Form\Product\ImageForm;
use Admin\Form\Product\AttributeForm;
use Admin\Form\Product\AddFromLinkForm;
use Admin\Form\Product\ListSpecialForm;
use Admin\Form\Product\SearchSpecialForm;
use Admin\Form\Product\AddProductForm;

class ProductsController extends AppController
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
     * Product list
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'updated-desc',  
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();    
            
            // on/off websites.active
            if (isset($post['_id']) && isset($post['value'])) {       
                if ($request->isXmlHttpRequest()) {
                    Api::call(
                        'url_products_onoff', 
                        $post
                    );
                    echo 'OK';
                    exit;
                }
            }
            
            // update products.sort
            if (!empty($post['sort'])) {  
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call('url_products_updatesort', $post); 
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
                    ->setDataset(Api::call('url_products_lists', $param))
                    ->create();
        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,                
            )
        );
    }
    
    /**
     * Product list
     *
     * @return Zend\View\Model
     */
    public function specialAction()
    {
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => \Application\Module::getConfig('general.default_limit'),
            'sort' => 'sort-asc',
        ));
        
        if (isset($param['tab'])) {
            switch ($param['tab']) {
                case 'latestarrival':
                    $param['get_latest_arrival'] = '1';
                    $addProductIUrl = 'url_products_addlatestarrival';                    
                    $updateSortUrl = 'url_products_updatesortlatestarrival';
                    break;
                case 'featured':
                    $param['get_featured'] = '1';
                    $addProductIUrl = 'url_products_addfeatured';
                    $updateSortUrl = 'url_products_updatesortfeatured';
                    break;
                case 'topseller':
                    $param['get_top_seller'] = '1';
                    $addProductIUrl = 'url_products_addtopseller';
                    $updateSortUrl = 'url_products_updatesorttopseller';
                    break;                    
            }
        }
        
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
                    $result = Api::call($addProductIUrl, $post);                   
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
                $post['sort'] = \Zend\Json\Encoder::encode($post['sort']);
                Api::call($updateSortUrl, $post); 
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
        $searchForm = new SearchSpecialForm();  
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);
        
        // create list form
        $listForm = new ListSpecialForm();
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
     * Add a Place
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
                    $content = @file_get_contents($post['url']);
                    if ($content == false) {
                        $this->addErrorMessage('Error, please try again');
                        return $this->getViewModel(array(
                                'form' => $form,
                            )
                        );
                    }
                    $content = strip_tags_content($content, '<script><style>', true);
                    $html = str_get_html($content);                 
                    switch ($post['site']) {
                        case 'lazada.vn':                          
                            foreach($html->find('h1[id=prod_title]') as $element) {
                                if (!empty($element->innertext)) {
                                    $post['name'] = trim(strip_tags($element->innertext));
                                    break;
                                }
                            }
                            foreach($html->find('div[class=product-description__block]') as $element) {                            
                                $description = strip_tags_content($element->innertext, '<noscript><script><style>', true);
                                $description = preg_replace('@<h2 class="product-description__title">.*?</h2>@si', '', $description);
                                $description = preg_replace('@<div class="webyclip-thumbnails" id="webyclip_thumbnails">.*?</div>@si', '', $description);
                                $description = preg_replace('@<div class="product-description__webyclip-thumbnails">.*?</div>@si', '', $description);
                                $description = preg_replace('/<img class="productlazyimage"  data-original="(.*?)" alt="image" \/>/', '<img src="$1" />', $description);
                                $post['content'] = $description;
                                break;
                            }
                            $post['images'] = array();
                            foreach($html->find('div[class=productImage]') as $element) {
                                if (!empty($element->attr['data-big'])) {
                                    if (empty($post['images'])) {
                                        $post['url_image'] = $element->attr['data-big'];
                                    }
                                    if (!in_array($element->attr['data-big'], $post['images'])) {
                                        $post['images'][] = $element->attr['data-big'];
                                    }
                                }                               
                            }                            
                            foreach($html->find('span[id=special_price_box]') as $element) {
                                if (!empty($element->innertext)) {                                    
                                    $post['price'] = str_replace(array('VND','.',','), '', trim(strip_tags($element->innertext)));
                                    break;
                                }
                            }
                            foreach($html->find('span[id=price_box]') as $element) {
                                if (!empty($element->innertext)) {                                    
                                    $post['original_price'] = str_replace(array('VND','.',','), '', trim(strip_tags($element->innertext)));
                                    break;
                                }
                            }
                            break;
                        case 'bibomart.com.vn': 
                            foreach($html->find('h1[itemprop=name]') as $element) {
                                if (!empty($element->innertext)) {
                                    $post['name'] = trim(strip_tags($element->innertext));
                                    break;
                                }
                            } 
                            foreach($html->find('h3[style=text-align: justify;]') as $element) {
                                $post['short'] = trim(strip_tags($element->innertext));
                                break;
                            }
                            if (empty($post['short'])) {
                                foreach($html->find('p[style=text-align: justify;]') as $element) {
                                    $post['short'] = trim(strip_tags($element->innertext));
                                    break;
                                }
                            }
                            foreach($html->find('div[class=detail_content]') as $element) {                            
                                $post['content'] = strip_tags_content($element->innertext, '<script><style>', true);
                                break;
                            }
                            foreach($html->find('ul[id=example3]') as $element) {                            
                                $imageHtml = str_get_html($element->innertext);
                                foreach($imageHtml->find('img') as $element) {    
                                    if (!empty($element->src)) {
                                        if ($element->attr['class'] == 'etalage_source_image') {
                                            $post['url_image'] = $element->src;
                                            break;
                                        }                                        
                                    }
                                }
                                break;
                            }
                            $post['images'] = array();
                            foreach($html->find('ul[id=example3]') as $element) {                              
                                $imageHtml = str_get_html($element->innertext);
                                foreach($imageHtml->find('img') as $element) {
                                    if (!empty($element->attr['src'])) {
                                        if ($element->attr['class'] == 'etalage_source_image') {
                                            $post['images'][] = $element->attr['src'];
                                        }
                                    }
                                }
                            }
                            foreach($html->find('span[itemprop=price]') as $element) {
                                if (!empty($element->innertext)) {                                    
                                    $post['price'] = str_replace('đ', '', trim(strip_tags($element->innertext)));
                                    break;
                                }
                            }
                            foreach($html->find('span[class=box-price-old old-price]') as $element) {
                                if (!empty($element->innertext)) {                                    
                                    $post['original_price'] = str_replace('đ', '', trim(strip_tags($element->innertext)));
                                    break;
                                }
                            }
                            //p($post, 1);
                            
                    }
                }
                $id = Api::call('url_products_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    ProductCategories::removeCache();
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/products', 
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
            'url_products_detail', 
            array(
                '_id' => $id, 
                'locale' => $locale,
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        $data['type'] = array('featured');
        switch ($tab) {
            case '':
                // create edit form
                $form = new UpdateForm();                 
                $form->setAttribute('enctype', 'multipart/form-data')
                     ->setController($this)
                     ->create();
                if (!empty($data['image_id'])) {
                    $data['url_image'] = Images::getUrl($data['image_id'], 'products', true);
                }
                $data['locale'] = $locale;
                $form->bindData($data);
                
                // save general form
                if ($request->isPost()) {
                    $post = (array) $request->getPost();              
                    $form->setData($post);
                    if ($form->isValid()) {
                        if (!empty($post['remove']['url_image'])) {
                            $post['image_id'] = 0;
                        }
                        Api::call('url_products_update', $post);  
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
                
            case 'images':
                // create image form                
                $image = Images::getAll($data['product_id'], 'products');               
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
                        for ($i = 1; $i < \Admin\Module::getConfig('products.max_images'); $i++) {
                            if (isset($post['remove']['url_image' . $i]) 
                                && isset($image['image_id']['url_image' . $i])) {
                                $remove[] = $image['image_id']['url_image' . $i];
                            }
                            if (!empty($image['url_image']['url_image' . $i]) 
                                && !empty($_FILES['url_image' . $i]['name'])) {
                                $update['url_image' . $i] = $image['image_id']['url_image' . $i];
                            }
                        }
                        $post['src'] = 'products';
                        $post['src_id'] = $data['product_id'];
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
             
            case 'attributes':     
                // create field form
                $attributes = !empty($data['attributes']) ? $data['attributes'] : array();
                $form = new AttributeForm();
                $form->setController($this)
                     ->setDataset($attributes)
                     ->create();
                $bind = array();
                foreach ($attributes as $attribute) {
                    $bind["field[{$attribute['field_id']}]"] = (!empty($attribute['value']) ? $attribute['value'] : '');
                }
                $form->bindData($bind);
                $request = $this->getRequest();
                if ($request->isPost()) {
                    $post = (array) $request->getPost();
                    $post['product_id'] = $data['product_id'];
                    Api::call('url_products_saveattribute', $post);
                    if (empty(Api::error())) {
                        $this->addSuccessMessage('Data saved successfully');
                        if (isset($post['saveAndBack']) && $backUrl) {
                            return $this->redirect()->toUrl(base64_decode($backUrl));
                        }
                        return $this->redirect()->toUrl($request->getRequestUri());
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
                        Api::call('url_products_addupdatelocale', $post); 
                        if (empty(Api::error())) {
                            ProductCategories::removeCache();
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
    
    /**
     * Product detail
     *
     * @return Zend\View\Model
     */
    public function detailAction()
    { 
        $request = $this->getRequest();
        
        $id = $this->params()->fromRoute('id', 0);    
        
        $locale = \Application\Module::getConfig('general.default_locale');
       
        // invalid parameters
        if (empty($id)) {
            return $this->notFoundAction();
        }
        
        // get detail             
        $data = Api::call(
            'url_products_detail', 
            array(
                '_id' => $id, 
                'locale' => $locale,
                'get_images' => 1,
            )
        );
       
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        
        if (Api::error()) {
            $this->addErrorMessage();
        }
        
        if (!empty($data['image_id'])) {
            $data['url_image'] = Images::getUrl($data['image_id'], 'products', true);
        } 
        //p($data);
        $this->getViewHelper('HeadScript')->appendFile('/be/plugins/elevatezoom/jquery.elevatezoom.js');
        return $this->getViewModel(array(
               'data' => $data
            )
        );       
    }      
    
    /**
     * Product List (Gird View)
     *
     * @return Zend\View\Model
     */
    public function listsAction()
    {
        $request = $this->getRequest();
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => 15,
            'sort' => 'sort-asc',            
            'active' => 1,            
        ));
        $result = Api::call('url_products_lists', $param);      
        return $this->getViewModel(array(
                'params' => $this->params()->fromQuery(),               
                'result' => $result,               
            )
        );
    }
    
    /**
     * Ajax remove feature product
     *
     * @return Zend\View\Model
     */
    public function removespecialAction()
    { 
        $request = $this->getRequest(); 
        $productId = $this->params()->fromQuery('product_id', 0);
        $tab = $this->params()->fromQuery('tab', '');
        switch ($tab) {
            case 'latestarrival':                
                $removeProductIUrl = 'url_products_removelatestarrival';
                break;
            case 'featured':                
                $removeProductIUrl = 'url_products_removefeatured';               
                break;
            case 'topseller':                
                $removeProductIUrl = 'url_products_removetopseller';
                break;                    
        }
        if (!empty($productId) 
            && $request->isPost() 
            && $request->isXmlHttpRequest()) {
            $post = (array) $request->getPost();
            Api::call($removeProductIUrl, array(               
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
