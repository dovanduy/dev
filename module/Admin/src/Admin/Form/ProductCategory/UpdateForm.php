<?php

namespace Admin\Form\ProductCategory;

use Application\Form\AbstractForm;
use Application\Model\ProductCategories;

/**
 * Category Update Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class UpdateForm extends AbstractForm
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
        $elements = array(
            array(
                'name' => '_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'image_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'parent_id',
                'type' => 'Application\Form\Element\Select2',             
                'attributes' => array(
                    'id' => 'parent_id',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Parent product category',
                    'value_options' => array()
                )
            ),           
            array(
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Main photo',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            ),
            array(            
                'name' => 'url',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'url',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
        );
        $locales = \Application\Module::getConfig('general.locales');
        if (count($locales) == 1) { 
            $elements = array_merge(
                $elements,
                array(
                    array(
                        'name' => 'locale',
                        'attributes' => array(
                            'type' => 'hidden',
                        ),
                    ),                
                    array(
                        'name' => 'name',
                        'attributes' => array(
                            'id' => 'name',
                            'type' => 'text',
                            'required' => true,
                            'class' => 'form-control'
                        ),
                        'options' => array(
                            'label' => 'Category name',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('product_categories.name')
                    ),
                    array(
                        'name' => 'short',
                        'attributes' => array(
                            'id' => 'short',
                            'type' => 'textarea',
                            'required' => false,
                            'class' => 'form-control'
                        ),
                        'options' => array(
                            'label' => 'Short',
                        ),                
                    ),
                    array(
                        'name' => 'content',
                        'attributes' => array(
                            'id' => 'content',
                            'type' => 'textarea',
                            'required' => false,
                            'class' => 'form-control',
                            'rows' => 8
                        ),
                        'options' => array(
                            'label' => 'Content',
                        ),                
                    ),
                    array(
                        'name' => 'meta_keyword',
                        'attributes' => array(
                            'id' => 'meta_keyword',
                            'type' => 'textarea',                    
                            'class' => 'form-control',
                            'maxlength' => '500',
                            'rows' => 4
                        ),
                        'options' => array(
                            'label' => 'SEO keyword',
                        )
                    ),
                    array(
                        'name' => 'meta_description',
                        'attributes' => array(
                            'id' => 'meta_description',
                            'type' => 'textarea',                    
                            'class' => 'form-control',
                            'maxlength' => '500',
                            'rows' => 4
                        ),
                        'options' => array(
                            'label' => 'SEO description',
                        )
                    ),
                )
            );
        }
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'saveAndBack',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Update And Back',
                        'id' => 'saveAndBackButton',
                        'class' => 'btn btn-primary'                    
                    ),
                ),
                array(
                    'name' => 'save',
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
                        'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                    ),
                )
            )
        );
        return $elements;
    }  
    
}