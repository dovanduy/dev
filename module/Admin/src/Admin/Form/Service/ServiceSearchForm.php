<?php

namespace Admin\Form\Service;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Lib\Arr;
use Admin\Lib\Api;

/**
 * ServiceForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ServiceSearchForm extends AbstractForm
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
        return array(
            array(
                'name' => 'service_id',
                'attributes' => array(
                    'id' => 'service_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Service ID',
                ),
            ),
            array(
                'name' => '_id',
                'attributes' => array(
                    'id' => '_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => '_Id',
                ),
            ),
            array(
                'name' => 'is_locale',
                'attributes' => array(
                    'id' => 'is_locale',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Is_locale',
                ),
            ),
            array(
                'name' => 'tag',
                'attributes' => array(
                    'id' => 'tag',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),
            ),
            array(
                'name' => 'type',
                'attributes' => array(
                    'id' => 'type',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Type',
                ),
            ),
            array(
                'name' => 'iseq',
                'attributes' => array(
                    'id' => 'iseq',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Iseq',
                ),
            ),
            array(
                'name' => 'parent_id',
                'attributes' => array(
                    'id' => 'parent_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Parent_id',
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
                'name' => 'values',
                'attributes' => array(
                    'id' => 'values',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Values',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',
                        'artist-asc' => 'Artist ASC',
                        'artist-desc' => 'Artist DESC',
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
                'type' => 'Zend\Form\Element\Select',
                'name' => 'country',
                'options' => array(
                    'label' => 'Country',
                    'value_options' =>
                        array('' => '--Select one--') +
                        Arr::keyValue(
                            Api::call('url_locationscountries_lists', array()),
                            'iso_num',
                            'name'
                        )
                    
                ),
                'attributes' => array(
                    'id' => 'country',
                    'class' => 'form-control',
                    'value' => ''
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