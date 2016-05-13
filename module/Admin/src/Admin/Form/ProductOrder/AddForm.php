<?php

namespace Admin\Form\ProductOrder;

use Application\Lib\Api;
use Application\Lib\Arr;
use Application\Form\AbstractForm;

/**
 * Add Form
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
        $userList = Api::call('url_users_all', array());
        return array(            
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'user_id',
                'options' => array(
                    'label' => 'Customer name',
                    'value_options' => array('0' => '--Choose one--')
                ),
                'attributes' => array(
                    'ajax_url' => $this->getController()->url()->fromRoute('admin/ajax', array('action' => 'searchuser')),
                    'id' => 'user_id',
                    'class' => 'form-control',
                    'required' => false,
                    'onchange' => 'return localeAddress(this.value);',
                )
            ),          
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'address_id',
                'options' => array(                    
                    'label' => 'Address',
                    'value_options' => array()
                ),
                'attributes' => array(
                    'id' => 'address_id',
                    'class' => 'form-control',   
                    'required' => false,
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
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'class' => 'btn btn-primary',                    
                    //'onclick' => 'return saveNewOrder()',                    
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