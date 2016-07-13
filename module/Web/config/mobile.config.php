<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

if (in_array(domain(), ['thoitrang1.net', 'thoitrang1.vn'])) {
    return array(
        'view_manager' => array(        
            'template_map' => array(            
                'web/header' => __DIR__ . '/../view/partial/thoitrang1/mobile/header.phtml',           
                'web/footer' => __DIR__ . '/../view/partial/thoitrang1/mobile/footer.phtml'         
            ),        
        ),
        'display_page' => 5,
    );    
} else {
    return array(
        'view_manager' => array(        
            'template_map' => array(            
                'web/header' => __DIR__ . '/../view/partial/mobile/header.phtml',           
                'web/footer' => __DIR__ . '/../view/partial/mobile/footer.phtml'         
            ),        
        ),
        'display_page' => 5,
    );
}