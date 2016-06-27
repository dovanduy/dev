<?php

namespace Web\Form\Checkout;

use Application\Form\AbstractForm;
use Application\Lib\Auth;
use Web\Model\LocaleCountries;

/**
 * RegisterAddressForm
 *
 * @package Web\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class RegisterAddressForm extends AbstractForm
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
        $checkoutInfo = \Application\Lib\Session::get('checkout_step1');
        if (isset($checkoutInfo['address_id'])) {
            $addressId = $checkoutInfo['address_id'];
        } else {
            $auth = new Auth();
            if ($auth->hasIdentity()) {    
                $AppUI = $auth->getIdentity();
                $addressId = !empty($AppUI->address_id) ? $AppUI->address_id : '';
            }
        }
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
            /*
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'address_id',               
                'options' => array(                    
                    'label' => 'Delivery address',
                    'value_options' => array()       
                ),
                'attributes' => array(
                    'id' => 'address_id',
                    'class' => 'form-control',                   
                    'value' => $addressId,
                    'onchange' => '
                        if (this.value == "") {
                            $("#new-address").removeClass("hide");
                            $("#city_code").val(""); 
                            $("#street").val(""); 
                        } else {
                            $("#new-address").addClass("hide");
                        }                        
                    ',
                    'required' => false,
                ), 
            ),
            * 
            */
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
                    'class' => 'form-control',
                    'placeholder' => ''
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
            )
        );         
        return $elements;
    }
}