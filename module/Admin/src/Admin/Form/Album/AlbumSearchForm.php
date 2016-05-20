<?php

namespace Admin\Form\Album;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Lib\Arr;
use Admin\Lib\Api;

/**
 * AlbumForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AlbumSearchForm extends AbstractForm
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
                'name' => 'album_id',
                'attributes' => array(
                    'id' => 'album_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Album ID',
                ),
            ),
            array(
                'name' => 'artist',
                'attributes' => array(
                    'id' => 'artist',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Artist',
                ),
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
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',
                        'tag-asc' => 'Tag ASC',
                        'tag-desc' => 'Tag DESC',
                        'type-asc' => 'Type ASC',
                        'type-desc' => 'Type DESC',
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
                            'iso_a2', 
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