<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Application\Lib\Arr;
use Application\Lib\Util;
use Web\Lib\Api;
use Web\Form\Product\ReviewForm;
use Web\Form\Product\CopyForm;
use Web\Model\ProductCategories;
use Web\Model\Websites;
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
        
        $optionValue = '';
        if (empty($brandId) && empty($optionId) && !empty($urlName2)) {
            $optionValue = $urlName2;
        } elseif (!empty($brandId) && empty($optionId) && !empty($urlName3)) {
            $optionValue = $urlName3;
        }
        
        $param = $this->getParams(array(
            'page' => 1,
            'limit' => WebModule::getConfig('limit.products'),
            'category_id' => $categoryId,            
            'brand_id' => $brandId,            
            'option_id' => $optionId,            
            'option_value' => $optionValue,            
            'force' => 0,            
        ));
      
        $website = Websites::getDetail(); 
        if (!empty($param['category_id']) 
            || !empty($param['brand_id']) 
            || !empty($param['option_id'])) { 
                        
            $result = Products::getList($param);
            
            if (!empty($param['category_id'])) {            
                $categories = ProductCategories::findAll($website['product_categories'], $param['category_id']);                            
                $id = 'web_products_index';
                foreach ($categories as $i => $category) { 
                    if ($category['category_id'] == $param['category_id']) {
                        $detailCategory = $category;
                        if (empty($category['parent_id'])) {
                            $openCategoryId = $category['category_id']; 
                        } else {
                            $openCategoryId = $category['parent_id']; 
                        }                        
                    }                    
                    $page = $this->getServiceLocator()->get('web_navigation')->findBy('id', $id);                    
                    if (!empty($page)) {
                        if ($id == 'web_products_index') {
                            $page->setLabel('');
                        }
                        $id = 'web_products_index_' . $category['category_id'];
                        $lastLabel = array($category['name']);
                        if (!empty($detailBrand) && $i == count($categories) - 1) {
                            $lastLabel[] = $detailBrand['brand_name'];
                        }
                        $lastLabel = implode(' ', $lastLabel);
                        $page->addPage(array(                        
                            'id' => $id,
                            'uri' => $this->url()->fromRoute(
                                'web/products', 
                                array(
                                    'name' => name_2_url($category['name']),                             
                                )
                            ),
                            'label' => $lastLabel,
                            'active' => true
                        ));
                        $metaArea[] = $lastLabel;
                    }
                }
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
            if (Util::isMobile()) {
                $filter = array('categories' => ProductCategories::getSubCategories($website['product_categories'], $lastLevel, $openCategoryId, 0, false)) + $result['filter']; 
            } else {
                $filter = array('categories' => ProductCategories::getSubCategories($website['product_categories'], $lastLevel, 0, 0, false)) + $result['filter']; 
            }
            if (!empty($param['brand_id']) && !empty($filter['brands'])) {
                foreach ($filter['brands'] as $brand) {
                    if ($brand['brand_id'] == $param['brand_id']) {
                        $detailBrand = $brand;
                        break;
                    }
                }
            }
            return $this->getViewModel(array(
                    'params' => $this->params()->fromQuery(),                                             
                    'optionId' => $optionId,
                    'optionValue' => $optionValue,
                    'result' => $result,  
                    'detailCategory' => isset($detailCategory) ? $detailCategory : array(),               
                    'detailBrand' => isset($detailBrand) ? $detailBrand : array(),               
                    'filter' => isset($filter) ? $filter : array(),
                    'openCategoryId' => isset($openCategoryId) ? $openCategoryId : 0,
                )
            );
            
        } elseif (!empty($productId)) { // detail page
            $param = $this->getParams(array(                      
                'force' => 0,            
            ));
            $request = $this->getRequest();
        
            $id = $productId;    

            // invalid parameters
            if (empty($id)) {
                return $this->notFoundAction();
            }
            
            // get detail             
            $data = Products::getDetail($id, $param['force']); //p($data);
          
            // not found data
            if (empty($data)) {
                return $this->notFoundAction();
            }
           
            $categoryId = 0;
            if (!empty($data['category_id'])) {
                $categoryId = $data['category_id'][rand(0, count($data['category_id']) - 1)];
            }
            
            if (!empty($categoryId)) {
                $categories = ProductCategories::findAll($website['product_categories'], $categoryId);
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
            if (!empty($data['code'])) {
                $data['meta_keyword'] = $data['meta_keyword'] . ', ' . $data['code'];                
            }
            if (!empty($data['code_src'])) {
                $data['meta_keyword'] = $data['meta_keyword'] . ', ' . $data['code_src'];
            }
            if (empty($data['meta_description'])) {
                $data['meta_description'] = implode(PHP_EOL, array(
                    'Mua ' . $data['name'] . ' chính hãng chất lượng tại ' . $_SERVER['SERVER_NAME'] . ', giao hàng tận nơi, với nhiều chương trình khuyến mãi...',                                        
                    $data['short'],                    
                ));              
            }       
            $data['meta_title'] = array(
                $data['name']                
            );
            /*
            if (!empty($data['colors'])) {
                $data['meta_title'][] = 'Màu: ' . implode(', ', Arr::field($data['colors'], 'name'));
            }
            * 
            */
            $data['meta_title'] = implode(' - ', $data['meta_title']);
            $data['meta_title'] = preg_replace('!\s+!', ' ', $data['meta_title']);
            $data['meta_description'] = preg_replace('!\s+!', ' ', $data['meta_description']);
            
            $data['meta_image'] = !empty($data['url_image']) ? $data['url_image'] : '';
            if (!empty($data['image_facebook'])) {
                $data['meta_image'] = $data['image_facebook'];
            }
            $data['og_description'] = 'Mua ' . $data['name'] . ' chính hãng chất lượng tại ' . $_SERVER['SERVER_NAME'] . ', giao hàng tận nơi, với nhiều chương trình khuyến mãi...';            
            $this->setHead(array(
                'title' => $data['meta_title'],
                'meta_name' => array(
                    'description' => $data['meta_description'],
                    'keywords' => $data['meta_keyword'],
                    'area' => $metaArea,
                    'classification' => !empty($data['categories'][0]['name']) ? $data['categories'][0]['name'] : '',
                ),
                'meta_property' => array(
                    'og:title' => $data['meta_title'],
                    'og:description' => $data['og_description'],
                    'og:image' => $data['meta_image'],
                    'og:image:width' => '200',
                    'og:image:height' => '200',
                    'product:price:amount' => !empty($data['price']) ? number_format($data['price'], 0, ',', '.') : '0',
                    'og:price:currency' => 'VND',
                ),               
            ));
            
            $reviewForm = new ReviewForm();  
            $reviewForm ->setController($this)
                        ->setAttribute('id', 'comment-form')
                        ->setAttribute('role', 'form')
                        ->create('post');

//            $actionForm = new AdminActionForm();  
//            $actionForm ->setController($this)
//                        ->setAttribute('product_id', $data['product_id'])
//                        ->setAttribute('id', 'action-form')
//                        ->setAttribute('role', 'form')
//                        ->create('post');
            
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
            
            $data['content'] = '';
            if (array_intersect([15], $data['category_id'])) { 
                $data['content'] = implode(PHP_EOL, [                     
                        '- Balo lớn kích thước ngang 32 x cao 41.5 x rộng 14.5 (cm).',                        
                        '- Có 2 ngăn để vừa laptop 14", có chổ để bình nước.',               
                        '- Phù hợp đựng tập vở cho học sinh cấp 1, cấp 2, cấp 3, đựng đồ đi chơi.',
                        '',
                        '- Balo nhỏ xinh xắn, kích thước ngang 26 x cao 32 x rộng 9 (cm).',                       
                        '- Có 1 ngăn lớn và ngăn đựng vừa Laptop 12"/Ipad',
                        '- Phù hợp đựng tập vở, tài liệu, giấy A4, Laptop 12"/Ipad đi học thêm, đi làm, đi chơi, đựng đồ cho bé đi nhà trẻ, mẫu giáo',
                        '- Dây đeo tháo rời, thiết kế 2 trong 1 tiện lợi, vừa có thể đeo vai như balo vừa có thể đeo chéo.',
                        '',
                        '- Balo giả da simili hàng Việt Nam xuất khẩu, chất lượng đảm bảo không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',                                       
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng',                                          
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',                        
                    ]
                );
                $data['more_images'] = [                     
                    'http://img.vuongquocbalo.com/model/balo_lon_1.png',
                    'http://img.vuongquocbalo.com/model/balo_lon_2.png',
                    'http://img.vuongquocbalo.com/model/balo_lon_3.png',
                    'http://img.vuongquocbalo.com/model/balo_nho_1.png',
                    'http://img.vuongquocbalo.com/model/balo_nho_2.png',
                    'http://img.vuongquocbalo.com/model/balo_nho_3.png',
                    'http://img.vuongquocbalo.com/model/balo_lon_4.png',
                ];
            } elseif (array_intersect([16], $data['category_id'])) {                        
                $data['content'] = implode(PHP_EOL, [                                               
                        '- Kích thước 29 x 40 (cm).',
                        '- Phù hợp đựng tập vở đi học thêm hoặc đựng đồ đi chơi.',                               
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                        '- Màu dây giao ngẫu nhiên, có 2 màu đen hoặc trắng.',
                        '- Balo có 1 mặt in như hình và 1 mặt trơn màu đen sang trọng.',
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',    
                    ]
                );  
                $data['more_images'] = [                     
                    'http://img.vuongquocbalo.com/model/tui_rut_1.png',
                    'http://img.vuongquocbalo.com/model/tui_rut_2.png',
                    'http://img.vuongquocbalo.com/model/tui_rut_3.png',
                ];                
            } elseif (array_intersect([99], $data['category_id'])) {                
                $data['content'] = implode(PHP_EOL, [                         
                        '- <strong>Túi chéo nữ mini kích thước ngang 24 x 17 (cm)</strong>.',    
                        '- Phù hợp đựng tiền, điện thoại, máy tính bảng nhỏ, sổ tay, đồ trang điểm, các vật dụng cá nhân cho nữ, ...',    
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',    
                    ]
                );  
                $data['more_images'] = [                     
                    'http://img.vuongquocbalo.com/model/tui_cheo_mini_1.png',
                    'http://img.vuongquocbalo.com/model/tui_cheo_mini_2.png',
                    'http://img.vuongquocbalo.com/model/tui_cheo_mini_3.png',
                ];
             } elseif (array_intersect([8], $data['category_id'])) {
                $data['content'] = implode(PHP_EOL, [                         
                        '- <strong>Túi chéo kích thước ngang 34 x cao 25 x rộng 9 (cm)</strong>.',    
                        '- Sử dụng đựng tập vở, tài liệu, giấy A4 hoặc máy tính bảng.',    
                        '- Có 1 ngăn lớn, phù hợp đi học thêm, đi làm, đi chơi.',                                                       
                        '- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.',               
                        '- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.',
                        '- Hàng Việt Nam xuất khẩu, chất lượng đảm bảo.',    
                    ]
                );  
                $data['more_images'] = [                     
                    'http://img.vuongquocbalo.com/model/tui_cheo_1.png',
                    'http://img.vuongquocbalo.com/model/tui_cheo_2.png',
                ];
            }
            $data['content'] = str_replace([PHP_EOL,"'"], ['<br>',''], $data['content']);
            return $this->getViewModel(array(
                    'data' => $data,
                    'reviewForm' => $reviewForm,
                    'actionForm' => isset($actionForm) ? $actionForm : null
                ), 'detail'
            );            
        }
    } 
    
    
    
}