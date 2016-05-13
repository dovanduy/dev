<?php

namespace Admin\Form\News;

use Application\Form\AbstractForm;
use Application\Model\NewsCategories;

/**
 * Category Add Form
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
        $categories = NewsCategories::getForSelect($lastLevel);
        $elements = array( 
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
                'allow_empty' => true,
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
                'type' => 'Zend\Form\Element\Select',
                'name' => 'site',   
                'options' => array(
                    'label' => 'Website',
                    'value_options' => \Admin\Module::getConfig('news.import_site_value_options')
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
                    'value' => $this->translate('Save'),                    
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