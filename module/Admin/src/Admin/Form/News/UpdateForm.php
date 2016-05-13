<?php

namespace Admin\Form\News;

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
        $categories = NewsCategories::getForSelect($lastLevel);
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
                'type' => 'Zend\Form\Element\Select',
                'name' => 'locale',
                'options' => array(
                    'label' => 'Language',
                    'value_options' => \Application\Module::getConfig('general.locales')
                ),
                'attributes' => array(
                    'id' => 'locale',
                    'class' => 'form-control',
                )
            ),   
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'News Category List',
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
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Photo',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
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
                'validators' => \Admin\Module::getValidatorConfig('news.title')
            ),
            array(
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'Short',
                ),
                'validators' => \Admin\Module::getValidatorConfig('news.short')
            ),
            array(
                'name' => 'content',
                'type' => 'Application\Form\Element\CKEditor',
                'attributes' => array(
                    'id' => 'content',
                    'required' => true,
                    'class' => 'form-control',
                    'height' => 300
                ),
                'options' => array(
                    'label' => 'Content',
                ),
                'validators' => \Admin\Module::getValidatorConfig('news.content')
            ),
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
        );        
        return $elements;
    }
    
}