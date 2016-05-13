<?php

namespace Admin\Form\Website;

use Application\Form\AbstractForm;
use Application\Model\WebsiteCategories;

/**
 * Category Add Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddForm extends AbstractForm
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
        $categories = WebsiteCategories::getForSelect($lastLevel);
        return array(
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'Website Category List',
                    'value_options' =>
                        array('' => '--Choose--') +
                        $categories
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
                    'label' => 'Logo',
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
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
            array(            
                'name' => 'email',
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'id' => 'email',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.email')
            ), 
            array(
                'name' => 'phone',
                'attributes' => array(
                    'id' => 'phone',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Hotline',
                )
            ),
            array(            
                'name' => 'facebook',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'facebook',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Facebook',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
            array(            
                'name' => 'twitter',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'twitter',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Twitter',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
            array(            
                'name' => 'youtube',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'youtube',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Youtube',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
            ),
            array(            
                'name' => 'linkedin',
                'type' => 'Zend\Form\Element\Url',
                'attributes' => array(
                    'id' => 'linkedin',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'LinkedIn',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
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
                    'label' => 'Shop/company name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('websites.name')
            ),
            array(
                'name' => 'company_name',
                'attributes' => array(
                    'id' => 'company_name',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Company name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('websites.company_name')
            ),
            array(            
                'name' => 'address',
                'attributes' => array(
                    'id' => 'address',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Shop/company address',
                )
            ),
            array(
                'name' => 'about',
                'attributes' => array(
                    'id' => 'about',
                    'type' => 'textarea',                    
                    'class' => 'form-control',
                    'maxlength' => '500',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'About Shop/company',
                )
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
            
            /*
            array(
                'name' => 'about',
                'type' => 'Application\Form\Element\CKEditor',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'about',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'height' => 300
                ),
                'options' => array(
                    'label' => 'About',
                ),
                'validators' => \Admin\Module::getValidatorConfig('websites.about')
            ),
             * 
             */
            array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'saveButton',
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
    }
    
}