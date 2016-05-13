<?php

namespace Admin\Form\Product;

use Application\Form\AbstractForm;
use Application\Model\Brands;
use Application\Model\ProductCategories;

/**
 * Add From Link Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddFromLinkForm extends AbstractForm
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
        return array(           
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'category_id',
                'options' => array(
                    'label' => 'Product Category List',
                    'value_options' => $categories
                ),
                'attributes' => array(
                    'id' => 'category_id',
                    'class' => 'form-control',
                    'multiple' => true,
                    'required' => true,
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
                'type' => 'Zend\Form\Element\Select',
                'name' => 'site',   
                'options' => array(
                    'label' => 'Website',
                    'value_options' => \Admin\Module::getConfig('products.import_site_value_options')
                ),
                'attributes' => array(
                    'id' => 'site',
                    'required' => true,
                    'class' => 'form-control',
                ),
            ),
            array( 
                'type' => 'Zend\Form\Element\Url',
                'name' => 'url',
                'attributes' => array(
                    'id' => 'url',
                    'class' => 'form-control',
                    'required' => true,
                ),
                'options' => array(
                    'label' => 'URL'
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.uri')
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
        );
    }
    
}