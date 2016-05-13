<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'general' => array(
        'image' => array(
            array(
                'name' => 'Zend\Validator\File\Size',
                'options' => \Application\Module::getConfig('upload.image.size')
            ),
            array(
                'name' => 'Zend\Validator\File\Extension',
                'options' => array(
                    'extension' => \Application\Module::getConfig('upload.image.extension')
                )
            )
        ),
        'uri' => array(
            array(
                'name' => 'Uri',
            )
        ),
        'number' => array(
            array(
                'name' => 'Digits',
            )
        ),
        'float' => array(
            array(
                'name' => 'Float',
            )
        ),
        'date' => array(
            array(
                'name' => 'Date'
            )
        ),
        'email' => array(
            array(
                'name' => 'EmailAddress'
            )
        ),
        'password' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 4,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                ),
            )
        ),
        'country_code' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'state_code' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 5,
                    'max' => 5,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'city_code' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 8,
                    'max' => 8,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        )  
    ),

    'place_locales' => array(
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 150,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'tag' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 100,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'news_categories' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 150,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 255,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'website_categories' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 150,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 255,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'websites' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'company_name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'about' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'news' => array(       
        'title' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'admins' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 100,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),        
        'display_name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 100,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),        
    ),
    
    'input_fields' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 100,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        )        
    ),
    
    'product_categories' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 150,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 255,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'products' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 1024,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'content' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
        'warranty' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 1,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'brands' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),
        'about' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                    )
                )
            )
        ),
    ),
    
    'users' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ), 
        'phone' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 8,
                    'max' => 20,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'mobile' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 8,
                    'max' => 20,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
    ),
    
    'addresses' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),        
    ),
    
    'productorders' => array(
        'note' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
    ),
    
    'banners' => array(       
        'title' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),        
    ),
    
    'menus' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),
        'short' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 512,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                )
            )
        ),        
    ),
    
    'pages' => array(       
        'title' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        )            
    ),
    
    'blocks' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 256,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        )            
    ),
    
    'product_sizes' => array(       
        'name' => array(
            array(
                'name' => 'StringLength',
                'options' => array(
                    'min' => 2,
                    'max' => 100,
                    'messages' => array(
                        \Zend\Validator\StringLength::TOO_SHORT => 'The input is less than {min} characters long',
                        \Zend\Validator\StringLength::TOO_LONG => 'The input is more than {max} characters long'
                    )
                ),
            )
        ),    
    ),
);
