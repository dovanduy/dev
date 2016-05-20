<?php

namespace Admin\Form\Hotel;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Lib\Arr;
use Admin\Lib\Api;

/**
 * HotelForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class HotelSearchForm extends AbstractForm
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
                'name' => 'hotel_id',
                'attributes' => array(
                    'id' => 'hotel_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Hotel ID',
                ),
            ),
            array(
                'name' => 'category_id',
                'attributes' => array(
                    'id' => 'category_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Category id',
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
                'name' => 'street',
                'attributes' => array(
                    'id' => 'street',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Street',
                ),
            ),
            array(
                'name' => 'url_website',
                'attributes' => array(
                    'id' => 'url_website',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url Website',
                ),
            ),
            array(
                'name' => 'hotline',
                'attributes' => array(
                    'id' => 'hotline',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Hotline',
                ),
            ),
            array(
                'name' => 'email',
                'attributes' => array(
                    'id' => 'email',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
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