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
            'web_navigation' => 'Web\Navigation\NavigationFactory',
        ),
    ),
    
    'controllers' => array(
        'initializers' => array(
            'Application\Controller\Initializer'
        ),
        'invokables' => array(
            'Web\Controller\Index' => 'Web\Controller\IndexController',
            'Web\Controller\Ajax' => 'Web\Controller\AjaxController',           
            'Web\Controller\Auth' => 'Web\Controller\AuthController',            
            'Web\Controller\Page' => 'Web\Controller\PageController',           
            'Web\Controller\Checkout' => 'Web\Controller\CheckoutController',
            'Web\Controller\Products' => 'Web\Controller\ProductsController',
            'Web\Controller\Carts' => 'Web\Controller\CartsController',
            'Web\Controller\Pages' => 'Web\Controller\PagesController',
            'Web\Controller\Contact' => 'Web\Controller\ContactController',
            'Web\Controller\My' => 'Web\Controller\MyController',
            'Web\Controller\Search' => 'Web\Controller\SearchController',
        ),
    ),
   
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'web/layout' => __DIR__ . '/../view/layout/layout.phtml', 
            'web/layout/error' => __DIR__ . '/../view/layout/error.phtml',
            'web/ajax' => __DIR__ . '/../view/layout/ajax.phtml',
            'web/page' => __DIR__ . '/../view/layout/page.phtml',
            'web/header' => __DIR__ . '/../view/partial/header.phtml',           
            'web/footer' => __DIR__ . '/../view/partial/footer.phtml',            
            'web/sidebar' => __DIR__ . '/../view/partial/sidebar.phtml',
            'web/breadcrumb' => __DIR__ . '/../view/partial/breadcrumb.phtml',          
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
   
    'view_helpers' => array(
        'invokables'=> array(
            'productItemHelper' => 'Application\View\Helper\ProductItemHelper',
            'paginatorHelper' => 'Application\View\Helper\PaginatorHelper',
            'sliderHelper' => 'Application\View\Helper\SliderHelper',
            'htmlForm' => 'Application\View\Helper\HtmlForm',  
            'htmlListForm' => 'Application\View\Helper\HtmlListForm',
            'formRow' => 'Application\Form\View\Helper\FormRow', 
            'formFile' => 'Application\Form\View\Helper\FormFile', 
            'formLabel' => 'Application\Form\View\Helper\FormLabel',                    
            'formText' => 'Application\Form\View\Helper\FormText',           
            'formFile' => 'Application\Form\View\Helper\FormFile',           
            'formMultiCheckbox2' => 'Application\Form\View\Helper\FormMultiCheckbox2', 
            'formElement' => 'Application\Form\View\Helper\FormElement', 
            'formDateCalendar' => 'Application\Form\View\Helper\FormDateCalendar', 
            'formNewSelect' => 'Application\Form\View\Helper\FormNewSelect', 
            'formSelect2' => 'Application\Form\View\Helper\FormSelect2', 
            'formCKEditor' => 'Application\Form\View\Helper\FormCKEditor',     
            'formErrorMessage' => 'Application\Form\View\Helper\FormErrorMessage',     
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
    
    'site_name' => 'Shop',
    
    'search_limit_value_options' => array(  
        16 => 16,
        32 => 32,
        64 => 64
    ),
    
    'search_locale_value_options' => array(        
        'vi' => 'vi',
        'en' => 'en',
        'ru' => 'ru',
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
    
    'hotels_services_type_value_options' => array(
        1 => 1,
        2 => 2,
    ),
    
    'gender_value_options' => array(        
        1 => 'Male',
        2 => 'Female',
    ),
    
    
    'places_services_type_value_options' => array(
        1 => 1,
        2 => 2,
    ),
    
    'input_fields_element_value_options' => array(
        'text' => 'text',
        'textarea' => 'textarea',
        'select' => 'select',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
    ),
    
    'news_site_value_options' => array(
        'http://vnexpress.vn' => 'http://vnexpress.vn',
        'http://news.zing.vn/' => 'http://news.zing.vn/',
    ),
    
    'limit' => array(
        'products' => 18
    ),   
    
    'st_host' => 'http://vuongquocbalo.com/web',
    'image_unavalable_url' => 'http://img.vuongquocbalo.com/unavailable.png',
    
    'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'name' => 'web',
    ),
    
    'facebook_admins' => '129746714106531',
    'facebook_app_id' => '1679604478968266',
    'facebook_app_secret' => '53bbe4bab920c2dd3bb83855a4e63a94',
    'facebook_tag_ids' => array(
        '10206637393356602', // Thai Lai
        '129881887426347', // Balo Đẹp
        '835521976592060', // Ngoc Nguyen My
        '1723524741251993', // Duc Tin
    ),
    'admin_user_id' => array(1, 4, 11, 13),
    
);
