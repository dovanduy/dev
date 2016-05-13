<?php
return array(  
    'admin_navigation' => array(            
        array(
            'id' => 'admin_newscategories_index',
            'label' => 'News Category List',
            'module' => 'admin',
            'controller' => 'newscategories',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_newscategories_add',
                    'label' => 'Add News Category',
                    'module' => 'admin',
                    'controller' => 'newscategories',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_newscategories_update',
                    'label' => 'Edit News category',
                    'module' => 'admin',
                    'controller' => 'newscategories',
                    'action' => 'update',
                ),
            ),
        ),
        array(
            'id' => 'admin_news_index',
            'label' => 'News List',
            'module' => 'admin',
            'controller' => 'news',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_news_add',
                    'label' => 'Add News',
                    'module' => 'admin',
                    'controller' => 'news',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_news_update',
                    'label' => 'Edit News',
                    'module' => 'admin',
                    'controller' => 'news',
                    'action' => 'update',
                ),
            ),
        ),
        array(
            'id' => 'admin_websitecategories_index',
            'label' => 'Website Category List',
            'module' => 'admin',
            'controller' => 'websitecategories',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_websitecategories_add',
                    'label' => 'Add Website Category',
                    'module' => 'admin',
                    'controller' => 'websitecategories',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_websitecategories_update',
                    'label' => 'Edit Website Category',
                    'module' => 'admin',
                    'controller' => 'websitecategories',
                    'action' => 'update',
                ),
            ),
        ),

        array(
            'id' => 'admin_websites_index',
            'label' => 'Website List',
            'module' => 'admin',
            'controller' => 'websites',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_websites_add',
                    'label' => 'Add Website',
                    'module' => 'admin',
                    'controller' => 'websites',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_websites_update',
                    'label' => 'Edit Website',
                    'module' => 'admin',
                    'controller' => 'websites',
                    'action' => 'update',
                ),
                array(
                    'id' => 'admin_websites_profile',
                    'label' => 'Website Profile',
                    'module' => 'admin',
                    'controller' => 'websites',
                    'action' => 'profile',
                ),
            ),
        ),

        array(
            'id' => 'admin_admins_index',
            'label' => 'Admin List',
            'module' => 'admin',
            'controller' => 'admins',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_admins_add',
                    'label' => 'Add Admin',
                    'module' => 'admin',
                    'controller' => 'admins',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_admins_update',
                    'label' => 'Update Admin',
                    'module' => 'admin',
                    'controller' => 'admins',
                    'action' => 'update',
                ),
                array(
                    'id' => 'admin_admins_profile',
                    'label' => 'Profile',
                    'module' => 'admin',
                    'controller' => 'admins',
                    'action' => 'profile',
                ),
            ),
        ),

        array(
            'id' => 'admin_inputfields_index',
            'label' => 'Attribute List',
            'module' => 'admin',
            'controller' => 'inputfields',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_inputfields_add',
                    'label' => 'Add Attribute',
                    'module' => 'admin',
                    'controller' => 'inputfields',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_inputfields_update',
                    'label' => 'Edit Attribute',
                    'module' => 'admin',
                    'controller' => 'inputfields',
                    'action' => 'update',
                ),
            ),
        ),

        array(
            'id' => 'admin_productcategories_index',
            'label' => 'Product Category List',
            'module' => 'admin',
            'controller' => 'productcategories',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_productcategories_add',
                    'label' => 'Add Product Category',
                    'module' => 'admin',
                    'controller' => 'productcategories',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_productcategories_update',
                    'label' => 'Edit Product Category',
                    'module' => 'admin',
                    'controller' => 'productcategories',
                    'action' => 'update',
                ),
            ),
        ),

        array(
            'id' => 'admin_products_index',
            'label' => 'Product List',
            'module' => 'admin',
            'controller' => 'products',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_products_add',
                    'label' => 'Add Product',
                    'module' => 'admin',
                    'controller' => 'products',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_products_update',
                    'label' => 'Edit Product',
                    'module' => 'admin',
                    'controller' => 'products',
                    'action' => 'update',
                ),
                array(
                    'id' => 'admin_products_detail',
                    'label' => 'Product Detail',
                    'module' => 'admin',
                    'controller' => 'products',
                    'action' => 'detail',
                ),
                array(
                    'id' => 'admin_products_lists',
                    'label' => 'Product List',
                    'module' => 'admin',
                    'controller' => 'products',
                    'action' => 'lists',
                ),
            ),
        ),

        array(
            'id' => 'admin_brands_index',
            'label' => 'Brand List',
            'module' => 'admin',
            'controller' => 'brands',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_brands_add',
                    'label' => 'Add Brand',
                    'module' => 'admin',
                    'controller' => 'brands',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_brands_update',
                    'label' => 'Update Brand',
                    'module' => 'admin',
                    'controller' => 'brands',
                    'action' => 'update',
                ),
            ),
        ),            

        array(
            'id' => 'admin_banners_index',
            'label' => 'Home Banner List',
            'module' => 'admin',
            'controller' => 'banners',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_banners_add',
                    'label' => 'Add Home Banner',
                    'module' => 'admin',
                    'controller' => 'banners',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_banners_update',
                    'label' => 'Update Home Banner',
                    'module' => 'admin',
                    'controller' => 'banners',
                    'action' => 'update',
                ),
            ),
        ),  

        array(
            'id' => 'admin_blocks_index',
            'label' => 'Product Block List',
            'module' => 'admin',
            'controller' => 'blocks',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_banners_add',
                    'label' => 'Add Product Block',
                    'module' => 'admin',
                    'controller' => 'blocks',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_banners_update',
                    'label' => 'Update Product Block',
                    'module' => 'admin',
                    'controller' => 'blocks',
                    'action' => 'update',
                ),
            ),
        ),

        array(
            'id' => 'admin_users_index',
            'label' => 'Member List',
            'module' => 'admin',
            'controller' => 'users',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_users_add',
                    'label' => 'Add Member',
                    'module' => 'admin',
                    'controller' => 'users',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_users_update',
                    'label' => 'Update Member',
                    'module' => 'admin',
                    'controller' => 'users',
                    'action' => 'update',
                ),                    
            ),
        ),

        array(
            'id' => 'admin_productorders_index',
            'label' => 'Order List',
            'module' => 'admin',
            'controller' => 'productorders',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_productorders_add',
                    'label' => 'Add Order',
                    'module' => 'admin',
                    'controller' => 'productorders',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_productorders_update',
                    'label' => 'Edit Order',
                    'module' => 'admin',
                    'controller' => 'productorders',
                    'action' => 'update',
                ),
                array(
                    'id' => 'admin_productorders_detail',
                    'label' => 'Order Detail',
                    'module' => 'admin',
                    'controller' => 'productorders',
                    'action' => 'detail',
                ),
            ),
        ),

        array(
            'id' => 'admin_menus_index',
            'label' => 'Menus',
            'module' => 'admin',
            'controller' => 'menus',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_menus_add',
                    'label' => 'Add menu',
                    'module' => 'admin',
                    'controller' => 'menus',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_menus_update',
                    'label' => 'Update menu',
                    'module' => 'admin',
                    'controller' => 'menus',
                    'action' => 'update',
                ),
            ),
        ), 
        
        array(
            'id' => 'admin_pages_index',
            'label' => 'Page List',
            'module' => 'admin',
            'controller' => 'pages',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_pages_add',
                    'label' => 'Add Page',
                    'module' => 'admin',
                    'controller' => 'pages',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_pages_update',
                    'label' => 'Update Page',
                    'module' => 'admin',
                    'controller' => 'pages',
                    'action' => 'update',
                ),
            ),
        ), 
        
        array(
            'id' => 'admin_productsizes_index',
            'label' => 'Product Size List',
            'module' => 'admin',
            'controller' => 'productsizes',
            'action' => 'index',
            'pages' => array(
                array(
                    'id' => 'admin_productsizes_add',
                    'label' => 'Add Product Size',
                    'module' => 'admin',
                    'controller' => 'productsizes',
                    'action' => 'add',
                ),
                array(
                    'id' => 'admin_productsizes_update',
                    'label' => 'Update Product Size',
                    'module' => 'admin',
                    'controller' => 'productsizes',
                    'action' => 'update',
                ),
            ),
        ), 
        
    ),     
);