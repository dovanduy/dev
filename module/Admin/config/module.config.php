<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => include ('router.config.php'), 
    'navigation' => include ('navigation.config.php'),
    
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'navigation' => 'Admin\Navigation\NavigationFactory',
        ),
    ),
    
    'controllers' => array(
        'initializers' => array(
            'Application\Controller\Initializer'
        ),
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\Ajax' => 'Admin\Controller\AjaxController',            
            'Admin\Controller\Images' => 'Admin\Controller\ImagesController',
            'Admin\Controller\Auth' => 'Admin\Controller\AuthController',            
            'Admin\Controller\Page' => 'Admin\Controller\PageController',           
            'Admin\Controller\Inputfields' => 'Admin\Controller\InputfieldsController',
            'Admin\Controller\Newscategories' => 'Admin\Controller\NewscategoriesController',
            'Admin\Controller\Productcategories' => 'Admin\Controller\ProductcategoriesController',
            'Admin\Controller\Productsizes' => 'Admin\Controller\ProductsizesController',
            'Admin\Controller\Productcolors' => 'Admin\Controller\ProductcolorsController',
            'Admin\Controller\Websitecategories' => 'Admin\Controller\WebsitecategoriesController',
            'Admin\Controller\Users' => 'Admin\Controller\UsersController',
            'Admin\Controller\News' => 'Admin\Controller\NewsController',
            'Admin\Controller\Websites' => 'Admin\Controller\WebsitesController',
            'Admin\Controller\Admins' => 'Admin\Controller\AdminsController',
            'Admin\Controller\Products' => 'Admin\Controller\ProductsController',
            'Admin\Controller\Brands' => 'Admin\Controller\BrandsController',
            'Admin\Controller\Productorders' => 'Admin\Controller\ProductordersController',
            'Admin\Controller\Carts' => 'Admin\Controller\CartsController',
            'Admin\Controller\Banners' => 'Admin\Controller\BannersController',
            'Admin\Controller\Menus' => 'Admin\Controller\MenusController',
            'Admin\Controller\Pages' => 'Admin\Controller\PagesController',
            'Admin\Controller\Blocks' => 'Admin\Controller\BlocksController',
            'Admin\Controller\Systems' => 'Admin\Controller\SystemsController',            
            'Admin\Controller\Vouchers' => 'Admin\Controller\VouchersController',            
        ),
    ),
   
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'admin/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'admin/ajax' => __DIR__ . '/../view/layout/ajax.phtml',
            'admin/page' => __DIR__ . '/../view/layout/page.phtml',
            'admin/header' => __DIR__ . '/../view/partial/header.phtml',
            'admin/footer' => __DIR__ . '/../view/partial/footer.phtml',
            'admin/sidebar' => __DIR__ . '/../view/partial/sidebar.phtml',
            'admin/breadcrumb' => __DIR__ . '/../view/partial/breadcrumb.phtml',           
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
   
    'view_helpers' => array(
        'invokables'=> array(
            'paginatorHelper' => 'Application\View\Helper\PaginatorHelper',
            'htmlForm' => 'Application\View\Helper\HtmlForm',  
            'htmlListForm' => 'Application\View\Helper\HtmlListForm',
            'formRow' => 'Application\Form\View\Helper\FormRow', 
            'formFile' => 'Application\Form\View\Helper\FormFile', 
            'formImage' => 'Application\Form\View\Helper\FormImage', 
            'formLabel' => 'Application\Form\View\Helper\FormLabel', 
            'formMultiCheckbox2' => 'Application\Form\View\Helper\FormMultiCheckbox2', 
            'formElement' => 'Application\Form\View\Helper\FormElement', 
            'formDateCalendar' => 'Application\Form\View\Helper\FormDateCalendar', 
            'formNewSelect' => 'Application\Form\View\Helper\FormNewSelect', 
            'formSelect2' => 'Application\Form\View\Helper\FormSelect2', 
            'formCKEditor' => 'Application\Form\View\Helper\FormCKEditor', 
        )
    ),
    
    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format' => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>',
            'message_close_string' => '</span></div>',
            'message_separator_string' => '<br/>'
        ),        
    ),
        
    'translator' => array(
        'locale' => 'vi_VN',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => __NAMESPACE__
            ),
        ),
    ),
    
    'search_limit_value_options' => array(       
        10 => 10,
        20 => 20,
        30 => 30,
        40 => 40,
        50 => 50
    ),
    
    'search_locale_value_options' => array(        
        'vi' => 'vi',
        'en' => 'en',        
    ),
    
    'search_status_value_options' => array(
        '' => '--All--',
        0 => 0,
        1 => 1,
        2 => 2,
    ),
    
    'search_active_value_options' => array(
        '' => '--All--',
        1 => 'Yes', 
        0 => 'No',             
    ),
    
    'search_yesno_value_options' => array(        
        '' => '--All--',
        1 => 'Yes',
        0 => 'No',
    ),
    
    'yesno_value_options' => array(        
        1 => 'Yes',
        0 => 'No',
    ),
    
    'gender_value_options' => array(        
        1 => 'Male',
        2 => 'Female',
    ),
   
    'input_fields' => array(
        'type' => array(
            'text' => 'text',
            'textarea' => 'textarea',
            'select' => 'select',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
        )
    ),
    
    'news' => array(
        'max_images' => 10,
        'import_site_value_options' => array(
            'vnexpress.vn' => 'vnexpress.vn',
            'news.zing.vn' => 'news.zing.vn',
        ),
    ),
    
    'products' => array(
        'max_images' => 5,
        'import_site_value_options' => array(
            'lazada.vn' => 'lazada.vn',
            'bibomart.com.vn' => 'bibomart.com.vn',
            'chothoitrang.com' => 'chothoitrang.com',
        )
    ),
    
);