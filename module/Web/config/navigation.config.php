<?php
return array(  
    'web_navigation' => array(             
        array(
            'id' => 'web_index_index',
            'label' => 'Homepage',
            'module' => 'web',
            'controller' => 'index',
            'action' => 'index',                
        ),

        array(          
            'id' => 'web_products_index',
            'label' => 'Products',
            'module' => 'web',
            'controller' => 'products',
            'action' => 'index',
            'pages' => array(                     
                array(
                    'id' => 'web_products_detail',
                    'label' => '',
                    'module' => 'web',
                    'controller' => 'products',
                    'action' => 'detail',
                ),                    
            ),
        ),

        array(
            'id' => 'web_pages_index',
            'label' => 'Pages',
            'module' => 'web',
            'controller' => 'pages',
            'action' => 'index',                
        ),

        array(                
            'label' => 'Contact',
            'module' => 'web',
            'controller' => 'contact',
            'action' => 'index',                
        ),

        array(               
            'label' => 'Shopping cart',
            'module' => 'web',
            'controller' => 'carts',
            'action' => 'view',                
        ),

        array(             
            'id' => 'web_my_index',
            'label' => 'My',
            'module' => 'web',
            'controller' => 'my',
            'action' => 'index',                
        ),

        array(         
            'id' => 'web_checkout_index',
            'label' => 'Checkout',
            'module' => 'web',
            'controller' => 'checkout',
            'action' => 'index', 
            'pages' => array(                    
                array(                      
                    'label' => 'Checkout payment',
                    'module' => 'web',
                    'controller' => 'checkout',
                    'action' => 'payment',
                ),   
                array(                      
                    'label' => 'Checkout review',
                    'module' => 'web',
                    'controller' => 'checkout',
                    'action' => 'review',
                ),  
                array(                      
                    'label' => 'Thank you',
                    'module' => 'web',
                    'controller' => 'checkout',
                    'action' => 'completed',
                ),  
            ),
        ),
    ),
);