<?php

namespace Admin\Form\Album;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;
use Zend\Validator\File;

/**
 * AlbumForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AlbumForm extends AbstractForm
{  
    /**
    * Form construct
    *
    * @param string $name Form name
    */
    public function __construct($name = null)
    {
        parent::__construct($name); 
    }
    
    /**
    * Element array to create form
    *
    * @return array Array to create elements for form
    */
    public function elements() {  
        return array(
            array(
                'name' => 'album_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'artist',              
                'attributes' => array(
                    'id' => 'artist',                    
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Artist',               
                    'label_attributes' => array(
                        //'class' => 'fl'
                    ),                     
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(                            
                            'min' => 1,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1), 
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100) 
                            )
                        ),
                    ),
                ), 
            ),
            array(            
                'name' => 'title',
                'attributes' => array(
                    'id' => 'title',                    
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Title',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(                         
                            'min' => 1,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1), 
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100) 
                            )
                        ),
                    ),
//                    array(
//                        'name' => 'Application\Validator\DuplicateEmail',
//                        'options' => array(                         
//                            'notUserId' => 1
//                        ),
//                    ),
                ),
            ),
            array(
                'name' => 'image_url',                
                'attributes' => array(
                    'id' => 'image_url',    
                    'type' => 'file',   
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Image',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => array(    
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
                )
            ),
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/albums') . "'"
                ),
            )
        );
    }
    
}