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
use Web\Form\Product\ReviewForm;
use Web\Model\ProductCategories;
use Web\Model\Products;
use Web\Model\UrlIds;
use Web\Module as WebModule;

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
        $metaArea = array();        
        $urlName = $this->params()->fromRoute('name', '');
        $urlName2 = $this->params()->fromRoute('name2', '');
        $urlName3 = $this->params()->fromRoute('name3', '');
        
        UrlIds::getDetail($urlName, $categoryId, $brandId, $productId, $optionId);
        if (!empty($urlName2)) {
            UrlIds::getDetail($urlName2, $categoryId, $brandId, $productId, $optionId);
        }
        if (!empty($urlName3)) {
            UrlIds::getDetail($urlName3, $categoryId, $brandId, $productId, $optionId);
        }
        
        if (empty($categoryId) && empty($brandId) && empty($productId) && empty($optionId)) {
            return $this->notFoundAction();
        }
        
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => WebModule::getConfig('limit.products'),
            'category_id' => $categoryId,            
            'brand_id' => $brandId,            
            'option_id' => $optionId,            
        ));
        
        if (!empty($param['category_id']) || !empty($param['brand_id']) || !empty($param['option_id'])) {
                        
            if (!empty($param['brand_id'])) {                
                $filter = ProductCategories::getFilter(0, $param['brand_id']);         
                $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', 'web_products_index');                    
                if (!empty($page)) {
                    $detailBrand = Api::call(
                        'url_brands_detail', 
                        array(
                            'brand_id' => $param['brand_id'],                 
                        )
                    );
                    // not found data
                    if (empty($detailBrand)) {
                        return $this->notFoundAction();
                    }  
                    $page->setLabel($detailBrand['name']);  
                    $metaArea[] = trim($detailBrand['name']);
                }
                
                if (!empty($param['category_id'])) {
                    $categoryId = $param['category_id'];
                    $categories = ProductCategories::findAll($categoryId); 
                    foreach ($categories as $category) {
                        if ($category['category_id'] == $categoryId) {
                            $detailCategory = $category;
                            break;
                        }
                    }                    
                }
                
                $this->setHead(array(
                    'title' => $detailBrand['name'],
                    'meta_name' => array(
                        'description' => $detailBrand['meta_description'],
                        'keywords' => $detailBrand['meta_keyword'],
                        'area' => $metaArea,
                        'classification' => $detailBrand['name'],
                    ),
                    'meta_property' => array(
                        'og:title' => $detailBrand['name'],
                        'og:description' => $detailBrand['meta_description'],                    
                    ),
                ));
            
            } elseif (!empty($param['category_id'])) { 
                
                $categoryId = $param['category_id'];
                $categories = ProductCategories::findAll($categoryId);
                $id = 'web_products_index';
                foreach ($categories as $category) {
                    if ($category['category_id'] == $categoryId) {
                        $detailCategory = $category;
                    }
                    $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);                    
                    if (!empty($page)) {
                        if ($id == 'web_products_index') {
                            $page->setLabel('');
                        }
                        $id = 'web_products_index_' . $category['category_id'];
                        $page->addPage(array(                        
                            'id' => $id,
                            'uri' => $this->url()->fromRoute(
                                'web/products', 
                                array(
                                    'name' => name_2_url($category['name']),                             
                                )
                            ),
                            'label' => $category['name'],
                            'active' => true
                        ));
                        $metaArea[] = trim($category['name']);
                    }
                }                
                if (ProductCategories::hasSubCategory($categoryId, $lastLevel)) {
                    $param['category_id'] = implode(',', $lastLevel);
                    $filter = ProductCategories::getFilter($param['category_id']); 
                    $openCategoryId = $categoryId;
                } else {
                    $openCategoryId = $detailCategory['parent_id'];
                    $filter = ProductCategories::getFilter($categoryId); 
                }          
                $subCategories = ProductCategories::getSubCategories(array(), $lastLevel, $categories[0]['category_id'], false); 
                
                $this->setHead(array(
                    'title' => $detailCategory['name'],
                    'meta_name' => array(
                        'description' => $detailCategory['meta_description'],
                        'keywords' => $detailCategory['meta_keyword'],
                        'area' => $metaArea,
                        'classification' => $detailCategory['name'],
                    ),
                    'meta_property' => array(
                        'og:title' => $detailCategory['name'],
                        'og:description' => $detailCategory['meta_description'],                    
                    ),
                ));
            }           
            
            $result = Products::getList($param);      
            return $this->getViewModel(array(
                    'params' => $this->params()->fromQuery(),                                             
                    'optionId' => $optionId,
                    'result' => $result,               
                    'subCategories' => isset($subCategories) ? $subCategories : array(),               
                    'detailCategory' => isset($detailCategory) ? $detailCategory : array(),               
                    'detailBrand' => isset($detailBrand) ? $detailBrand : array(),               
                    'filter' => isset($filter) ? $filter : array(),
                    'openCategoryId' => isset($openCategoryId) ? $openCategoryId : 0,
                )
            );
            
        } elseif (!empty($productId)) { // detail page
            $request = $this->getRequest();
        
            $id = $productId;    

            // invalid parameters
            if (empty($id)) {
                return $this->notFoundAction();
            }

            // get detail             
            $data = Products::getDetail($id);
            
            // not found data
            if (empty($data)) {
                return $this->notFoundAction();
            }
            
            $categoryId = 0;
            if (!empty($data['category_id'])) {
                $categoryId = $data['category_id'][rand(0, count($data['category_id']) - 1)];
            }
            
            if (!empty($categoryId)) {
                $categories = ProductCategories::findAll($categoryId);
                $id = 'web_products_index';            
                foreach ($categories as $category) {
                    $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);                    
                    if (!empty($page)) {
                        if ($id == 'web_products_index') {
                            $page->setLabel('');
                        }
                        $id = 'web_products_index_' . $category['category_id'];
                        $page->addPage(array(                        
                            'id' => $id,
                            'uri' => $this->url()->fromRoute(
                                'web/products', 
                                array(
                                    'name' => name_2_url($category['name']),                             
                                )
                            ),
                            'label' => $category['name'],
                            'active' => true
                        ));
                        $metaArea[] = trim($category['name']);
                    }
                } 
                $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);
                if (!empty($page)) {
                    $page->addPage(array(                        
                        'id' => $id,
                        'uri' => $this->url()->fromRoute(
                            'web/products', 
                            array(
                                'name' => name_2_url($data['name']),                             
                            )
                        ),
                        'label' => $data['name'],
                        'active' => true
                    ));                    
                }
            }        
            $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);
            if (!empty($page)) { 
                $page->addPage(array( 
                    'uri' => '',
                    'label' => $data['name'],
                    'active' => true
                ));
            }
           
            if (empty($data['meta_keyword'])) {
                $data['meta_keyword'] = implode(', ', array_merge(array($data['name']), $metaArea));
            }
            if (empty($data['meta_description'])) {
                $data['meta_description'] = 'Mua ' . $data['name'] . ' chính hãng chất lượng tại ' . $_SERVER['SERVER_NAME'];
            }
           
            $this->setHead(array(
                'title' => $data['name'],
                'meta_name' => array(
                    'description' => $data['meta_description'],
                    'keywords' => $data['meta_keyword'],
                    'area' => $metaArea,
                    'classification' => !empty($data['categories'][0]['name']) ? $data['categories'][0]['name'] : '',
                ),
                'meta_property' => array(
                    'og:title' => $data['name'],
                    'og:description' => $data['meta_description'],
                    'og:image' => !empty($data['url_image']) ? $data['url_image'] : '',
                    'og:price:amount' => !empty($data['price']) ? app_money_format($data['price']) : '0',
                    'og:price:currency' => 'VND',
                ),                
            ));   
            
            $reviewForm = new ReviewForm();  
            $reviewForm ->setController($this)
                        ->setAttribute('id', 'comment-form')
                        ->setAttribute('role', 'form')
                        ->create('post');

            // send form
            if ($request->isPost()) {            
                $post = (array) $request->getPost(); 
                $post['product_id'] = $data['product_id'];
                if (isset($post['loadreviews'])) {
                    if ($request->isXmlHttpRequest()) {    
                        foreach ($data['product_reviews'] as $review) {
                            $review['content'] = nl2br($review['content']);
                            $time = app_datetime_format($review['created']);
                            $rating = '';
                            for ($star = 1; $star <= 5; $star++) {
                                if ($review['rating'] >= $star) {
                                    $rating .= "<i class=\"fa fa-star text-default\"></i>"; 
                                } else {
                                    $rating .= "<i class=\"fa fa-star\"></i>";
                                }
                            }
                            echo "<div class=\"comment clearfix\">                               
                                <header>
                                    <h3>{$review['name']}</h3>
                                    <div class=\"comment-meta\"> 
                                        {$rating} | {$time}
                                    </div>
                                </header>
                                <div class=\"comment-content\">
                                    <div class=\"comment-body clearfix\">
                                        <p>{$review['content']}</p>                                        
                                    </div>
                                </div>
                            </div>";
                        }
                        exit;               
                    }
                }

                if (isset($post['name'])) {
                    $reviewForm->setData($post);     
                    if (!$reviewForm->isValid() && !empty($reviewForm->getMessages())) {
                        die($this->getErrorMessageForAjax($reviewForm->getMessages()));
                    } else {
                        $id = Api::call('url_products_reviews_add', $post);
                        if (empty(Api::error())) {   
                            $result['status'] = 'OK';
                            die(\Zend\Json\Encoder::encode($result));
                        }               
                        die($this->getErrorMessageForAjax(array(), $error)); 
                    }
                }
            }
            return $this->getViewModel(array(
                    'data' => $data,
                    'reviewForm' => $reviewForm
                ), 'detail'
            );            
        }
    } 
    
}