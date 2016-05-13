<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;
use Application\Model\Brands;
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
        $branbs = Brands::getAll();
        $categories = ProductCategories::getForSelect($lastLevel);
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
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'Categories',
                    'value_options' => $categories
                ),
                'attributes' => array(
                    'id' => 'category_id',
                    'class' => 'form-control',
                    'multiple' => true,
                    'allow_options' => $lastLevel,
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'brand_id',
                'options' => array(
                    'label' => 'Brand',
                    'value_options' => array('0' => '--Choose one--') + $branbs
                ),
                'attributes' => array(
                    'id' => 'brand_id',
                    'class' => 'form-control',
                )
            ),
            array(            
                'name' => 'code',
                'attributes' => array(
                    'id' => 'code',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'SKU',
                ),
            ),
            array(            
                'name' => 'model',
                'attributes' => array(
                    'id' => 'model',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Model',
                ),
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
                'name' => 'price',
                'attributes' => array(
                    'id' => 'price',
                    'type' => 'text',
                    'class' => 'form-control price',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0'
                ),
                'options' => array(
                    'label' => 'Price',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.float')
            ),
            array(            
                'name' => 'original_price',
                'attributes' => array(
                    'required' => false,
                    'id' => 'original_price',
                    'type' => 'text',
                    'class' => 'form-control price',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0'
                ),
                'options' => array(
                    'label' => 'Original price',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.float')
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'vat',
                'options' => array(
                    'label' => 'VAT',
                    'value_options' => \Admin\Module::getConfig('yesno_value_options')
                ),
                'attributes' => array(
                    'id' => 'vat',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(            
                'name' => 'warranty',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'warranty',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Warranty',
                )
            ),
            array(            
                'name' => 'weight',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'weight',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Weight (kg)',
                )
            ),
            array(            
                'name' => 'size',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'szie',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Size (depth x width x height cm)',
                )
            ),
            array(     
                'type' => 'Zend\Form\Element\Url',
                'name' => 'url_video',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'url_video',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Video URL',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
            array(  
                'type' => 'Zend\Form\Element\Url',
                'name' => 'url_other',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'url_other',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Other URL',
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
                            'label' => 'Name',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('products.name')
                    ),
                    array(
                        'name' => 'short',
                        'attributes' => array(
                            'id' => 'short',
                            'type' => 'textarea',
                            'required' => false,
                            'rows' => 4,
                            'class' => 'form-control'
                        ),
                        'options' => array(
                            'label' => 'Short',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('products.short')
                    ),
                    array(
                        'name' => 'content',
                        'type' => 'Application\Form\Element\CKEditor',
                        'allow_empty' => true,
                        'attributes' => array(
                            'id' => 'content',
                            'class' => 'form-control',
                            'required' => false,
                            'height' => 300
                        ),
                        'options' => array(
                            'label' => 'Content',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('products.content')
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
                        'value' => 'Save And Back',
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
                        'value' => 'Cancel',                  
                        'class' => 'btn',
                        'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                    ),
                )
            )
        );
        return $elements;
    }  
    
}