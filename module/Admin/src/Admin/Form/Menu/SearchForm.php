<?php

namespace Admin\Form\Menu;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Model\Menus;

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
        $tab = $this->getRequest()->getQuery('tab', 'header');
        $menus = Menus::getForSelect($lastLevel, false, $tab);
        return array(  
            array(
                'type' => 'Application\Form\Element\Select2',
                'name' => 'parent_id',
                'options' => array(
                    'label' => 'Parent menu',
                    'value_options' =>
                        array('0' => '--Select one--') +
                        $menus
                ),
                'attributes' => array(
                    'id' => 'parent_id',
                    'class' => 'form-control',
                    'value' => '0'
                ),    
            ),            
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
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
                        'name-asc' => 'Name ASC',
                        'name-desc' => 'Name DESC',
                        'sort-asc' => 'Sort ASC',
                        'sort-desc' => 'Sort DESC',
                        'updated-desc' => 'Update Date DESC',
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
            ),
            array(
                'name' => 'tab',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
        );
    }
}