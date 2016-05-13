<?php

namespace Admin\Form\ProductOrder;

use Application\Form\AbstractForm;
use Application\Model\LocaleCountries;

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
                'name' => 'code',
                'attributes' => array(
                    'required' => false,
                    'id' => 'code',
                    'type' => 'text',
                    'class' => 'form-control',
                    'disabled' => 'disabled'
                ),
                'options' => array(
                    'label' => 'Code',
                ),
            ),                       
            array(            
                'name' => 'user_name',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'user_name',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Customer name',
                ),
                'validators' => \Admin\Module::getValidatorConfig('user.name')
            ),
            array(            
                'name' => 'user_email',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'user_email',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.email')
            ),
            array(            
                'name' => 'user_phone',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'user_phone',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Home phone',
                ),
                'validators' => \Admin\Module::getValidatorConfig('users.phone')
            ),
            array(            
                'name' => 'user_mobile',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'user_mobile',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Mobile',
                ),
                'validators' => \Admin\Module::getValidatorConfig('users.mobile')
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'country_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'Country',
                    'value_options' =>
                        array('' => '--Select one--') + 
                        LocaleCountries::getAll()                 
                ),
                'attributes' => array(
                    'id' => 'country',
                    'class' => 'form-control',
                    'onchange' => 'return localeState(this.value);',
                    'required' => false,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'state_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'State/Province',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'state_code',
                    'class' => 'form-control', 
                    'onchange' => 'return localeCity(this.value);',
                    'required' => false,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'city_code',
                'allow_empty' => true,
                'options' => array(                    
                    'label' => 'City/Distrcit',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'city_code',
                    'class' => 'form-control', 
                    'required' => false,
                ),
            ),
            array(
                'name' => 'street',
                'attributes' => array(
                    'id' => 'street',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Address (number, street, ward)',
                )
            ),    
            array(
                'name' => 'note',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'note',
                    'type' => 'textarea',
                    'required' => false,
                    'class' => 'form-control',
                    'rows' => 4,
                ),
                'options' => array(
                    'label' => 'Note',
                ),
                'validators' => \Admin\Module::getValidatorConfig('productorders.note')
            ),
            array(            
                'name' => 'tax',
                'attributes' => array(
                    'required' => false,
                    'id' => 'tax',
                    'type' => 'text',
                    'class' => 'form-control price',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0',
                ),
                'options' => array(
                    'label' => 'Tax',
                )
            ),
            array(            
                'name' => 'shipping',
                'attributes' => array(
                    'required' => false,
                    'id' => 'shipping',
                    'type' => 'text',
                    'class' => 'form-control price',
                    'data-a-sep' => ',',
                    'data-a-dec' => '.',
                    'data-v-max' => '99999999999',
                    'data-v-min' => '0',
                ),
                'options' => array(
                    'label' => 'Shipping',
                )
            ),
        );        
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
            )
        );
        return $elements;
    }  
    
}