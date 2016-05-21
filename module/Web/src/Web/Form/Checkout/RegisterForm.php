<?php

namespace Web\Form\Checkout;

use Application\Form\AbstractForm;
use Web\Model\LocaleCountries;

/**
 * List Form
 *
 * @package Web\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class RegisterForm extends AbstractForm
{
    /**
     * Table construct
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Element array to create form
    *
    * @return array Array to create elements for form
    */
    public function elements() {        
        $elements = array(
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
                'validators' => \Web\Module::getValidatorConfig('general.email')
            ), 
            array(            
                'name' => 'password',
                'attributes' => array(
                    'id' => 'password',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Password',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.password')
            ),
            array(            
                'name' => 'password_confirmation',
                'attributes' => array(
                    'id' => 'password_confirmation',                    
                    'type' => 'password',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Password confirmation',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.password_confirmation')
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
                    'label' => 'Full name',
                ),
                'validators' => \Web\Module::getValidatorConfig('users.name')
            ),              
            array(
                'name' => 'mobile',
                'attributes' => array(
                    'id' => 'mobile',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Mobile',
                ),
                'validators' => \Web\Module::getValidatorConfig('general.mobile')
            ),  
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'country_code',               
                'options' => array(                    
                    'label' => 'Country',
                    'value_options' =>
                        array('' => '--Select one--') + 
                        LocaleCountries::getAll()                 
                ),
                'attributes' => array(
                    'id' => 'country_code',
                    'class' => 'form-control',
                    'onchange' => 'return localeState(this.value);',
                    'required' => true,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'state_code',                
                'options' => array(                    
                    'label' => 'State/Province',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'state_code',
                    'class' => 'form-control', 
                    'onchange' => 'return localeCity(this.value);',
                    'required' => true,
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'city_code',               
                'options' => array(                    
                    'label' => 'City/District',
                    'value_options' => array('' => '--Select one--')
                ),
                'attributes' => array(
                    'id' => 'city_code',
                    'class' => 'form-control', 
                    'required' => true,
                ),
            ),
            array(
                'name' => 'street',
                'attributes' => array(
                    'id' => 'street',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Number, street, ward',
                )
            ),   
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'address_name',
                'options' => array(                    
                    'label' => 'Address type',
                    'value_options' => \Application\Module::getConfig('address_name')             
                ),
                'attributes' => array(
                    'id' => 'address_name',
                    'class' => 'form-control',
                    'required' => true,
                ),         
            ),  
        );   
        
        /*
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'save',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Sign up',
                        'id' => 'saveButton',
                        'class' => 'btn btn-primary ajax-submit'                    
                    ),
                ),               
            )
        );
        * 
        */
        return $elements;
    }
}