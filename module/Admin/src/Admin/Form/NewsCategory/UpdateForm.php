<?php

namespace Admin\Form\NewsCategory;

use Application\Form\AbstractForm;
use Application\Model\NewsCategories;

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
                    'label' => 'Parent category',
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
                        'validators' => \Admin\Module::getValidatorConfig('news_categories.name')
                    ),
                    array(
                        'name' => 'short',
                        'attributes' => array(
                            'id' => 'short',
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control'
                        ),
                        'options' => array(
                            'label' => 'Short',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('news_categories.short')
                    ),
                    array(
                        'name' => 'content',
                        'attributes' => array(
                            'id' => 'content',
                            'type' => 'textarea',
                            'required' => true,
                            'class' => 'form-control',
                            'rows' => 4
                        ),
                        'options' => array(
                            'label' => 'Content',
                        ),
                        'validators' => \Admin\Module::getValidatorConfig('news_categories.content')
                    )
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