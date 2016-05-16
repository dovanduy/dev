<?php
return array (
    'routes' => 
        array (                                  
        // The following is a route to simplify getting started creating
        // new controllers and actions without needing to create a new
        // module. Simply drop new controllers in, and you can access them
        // using the path /admin/:controller/:action
        'admin' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/admin',
                'defaults' => array(
                    '__NAMESPACE__' => 'Admin\Controller',
                    'controller' => 'Index',
                    'action' => 'index',
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route'    => '/[:controller[/:action]]',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                        ),
                    ),
                ),
                'ajax' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/ajax[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'ajax',
                            'action' => 'index',
                        ),
                    ),
                ),
                'albums' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/albums[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'albums',
                            'action' => 'index',
                        ),
                    ),
                ),
                'categories' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/categories[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'categories',
                            'action' => 'index',
                        ),
                    ),
                ),
                'page' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/page[/:action]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'index',
                        ),
                    ),
                ),
                'places' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/places[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'places',
                            'action' => 'index',
                        ),
                    ),
                    'may_terminate' => true,
                    'child_routes' => array(
                        'images' => array(
                            'type' => 'literal',
                            'options' => array(
                                'route' => '/places/images[/:id]',
                                'constraints' => array(
                                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'images',
                                    'action' => 'index',
                                ),
                            ),
                        ),
                    )                        
                ),     

                'inputfields' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/inputfields[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'inputfields',
                            'action' => 'index',
                        ),
                    ),
                ),

                'newscategories' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/newscategories[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'newscategories',
                            'action' => 'index',
                        ),
                    ),
                ),

                'news' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/news[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'news',
                            'action' => 'index',
                        ),
                    ),
                ),

                'websitecategories' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/websitecategories[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'websitecategories',
                            'action' => 'index',
                        ),
                    ),
                ),

                'websites' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/websites[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'websites',
                            'action' => 'index',
                        ),
                    ),
                ),

                'admins' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/admins[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ), 
                        'defaults' => array(          
                            'controller' => 'admins',
                            'action' => 'index',
                        ),
                    ),
                 ), 

                'users' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/users[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ), 
                        'defaults' => array(          
                            'controller' => 'users',
                            'action' => 'index',
                        ),
                    ),
                 ),

                'productcategories' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/productcategories[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'productcategories',
                            'action' => 'index',
                        ),
                    ),
                ),

                'products' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/products[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'products',
                            'action' => 'index',
                        ),
                    ),
                ),

                'brands' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/brands[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'brands',
                            'action' => 'index',
                        ),
                    ),
                ),

                'banners' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/banners[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'banners',
                            'action' => 'index',
                        ),
                    ),
                ),

                'productorders' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/productorders[/:action][/:id]',
                        'constraints' => array(
                            //'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'productorders',
                            'action' => 'index',
                        ),
                    ),
                ),

                'carts' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/carts[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'carts',
                            'action' => 'index',
                        ),
                    ),
                ),

                'menus' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/menus[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'menus',
                            'action' => 'index',
                        ),
                    ),
                ),

                'pages' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/pages[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'pages',
                            'action' => 'index',
                        ),
                    ),
                ),

                'blocks' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/blocks[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'blocks',
                            'action' => 'index',
                        ),
                    ),
                ),

                'productsizes' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/productsizes[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'productsizes',
                            'action' => 'index',
                        ),
                    ),
                ),
                
                'productcolors' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/productcolors[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'productcolors',
                            'action' => 'index',
                        ),
                    ),
                ),
                
                'systems' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/systems[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'systems',
                            'action' => 'index',
                        ),
                    ),
                ),

            ),
        ),            
    ),    
);