<?php
return array(
    'routes' => array(                                  
        // The following is a route to simplify getting started creating
        // new controllers and actions without needing to create a new
        // module. Simply drop new controllers in, and you can access them
        // using the path /web/:controller/:action
        'web' => array(
            'type' => 'Literal',
            'options' => array(
                'route'    => '/web',
                'defaults' => array(
                    '__NAMESPACE__' => 'Web\Controller',
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


                'products' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '[/:name][/:name2][/:name3]',
                        'constraints' => array(/*
                            'name'  => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                            'name2' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                            'name3' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',*/
                        ),
                        'defaults' => array(
                            'module' => 'web',
                            'controller' => 'products',
                            'action' => 'index',
                        ),
                    ),
                ),                    

                'search' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/search[/:q]',
                        'constraints' => array(

                        ),                            
                        'defaults' => array(
                            'module' => 'web',
                            'controller' => 'search',
                            'action' => 'index',
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

                'checkout' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/checkout[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'checkout',
                            'action' => 'index',
                        ),
                    ),
                ),

                'pages' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '[/:name]',
                        'constraints' => array(
                            'name' => '[a-zA-Z][a-zA-Z0-9_-]*.html',                               
                        ),
                        'defaults' => array(
                            'module' => 'web',
                            'controller' => 'pages',
                            'action' => 'index',
                        ),
                    ),                        
                ),

                'contact' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/contact[/:action]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'contact',
                            'action' => 'index',
                        ),
                    ),
                ),

                'my' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/my[/:action][/:id]',
                        'constraints' => array(
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ),
                        'defaults' => array(
                            'controller' => 'my',
                            'action' => 'index',
                        ),
                    ),
                ),

                'login' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/login',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'login',
                        ),
                    ),
                ),

                'login2' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/login2',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'login2',
                        ),
                    ),
                ),
                
                'fblogin' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/fblogin',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'fblogin',
                        ),
                    ),
                ),

                'glogin' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/glogin',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'glogin',
                        ),
                    ),
                ),
                
                'signup' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/signup',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'signup',
                        ),
                    ),
                ),
                
                'forgetpassword' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/quen-mat-khau',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'forgetpassword',
                        ),
                    ),
                ),
                
                'newpassword' => array(
                    'type'    => 'Segment',
                    'options' => array(
                        'route' => '/mat-khau-moi[/:token]',                           
                        'defaults' => array(
                            'controller' => 'page',
                            'action' => 'newpassword',
                        ),
                    ),
                ),

            ),

        ),            
    ),
);