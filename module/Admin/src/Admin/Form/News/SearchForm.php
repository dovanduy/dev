<?php

namespace Admin\Form\News;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\NewsCategories;

/**
 * SearchForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class SearchForm extends AbstractForm
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
        return array(  
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
                'name' => 'category',
                'options' => array(
                    'label' => 'Category',
                    'value_options' =>
                        array('0' => '--All--') +
                        $categories
                ),
                'attributes' => array(
                    'id' => 'category',
                    'class' => 'form-control',
                    'value' => '0'
                )
            ),
            array(
                'name' => 'title',
                'attributes' => array(
                    'id' => 'title',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Title',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'active',
                'options' => array(
                    'label' => 'Active',
                    'value_options' => Module::getConfig('search_active_value_options')
                ),
                'attributes' => array(
                    'id' => 'active',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',                        
                        'title-asc' => 'Title ASC',
                        'title-desc' => 'Title DESC',
                    ),
                ),
                'attributes' => array(
                    'id' => 'sort',
                    'class' => 'form-control',
                    'value' => ''
                )
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'limit',
                'options' => array(
                    'label' => 'Record/Page',
                    'value_options' => Module::getConfig('search_limit_value_options')
                ),
                'attributes' => array(
                    'id' => 'limit',
                    'class' => 'form-control',
                    'value' => '10'
                )
            ),            
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Search',
                    'id' => 'searchButton',
                    'class' => 'btn btn-primary'
                ),
            )
        );
    }
}