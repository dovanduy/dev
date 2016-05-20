<?php

namespace Admin\Form\Category;

use Admin\Module;
use Application\Form\AbstractForm;
use Application\Lib\Arr;
use Admin\Lib\Api;
use Admin\Model\Categories;
/**
 * CategoryForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class CategorySearchForm extends AbstractForm
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
                'name' => 'category_id',
                'attributes' => array(
                    'id' => 'category_id',
                    'type' => 'text',
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Category ID',
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
                    'label' => 'Category name',
                ),
            ),
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'sort',
                'options' => array(
                    'label' => 'Sort',
                    'value_options' => array(
                        '' => '--Select one--',
                        'count_hotel-asc' => 'Total Hotels ASC',
                        'count_hotel-desc' => 'Total Hotels DESC',
                        'name-asc' => 'Name ASC',
                        'name-desc' => 'Name DESC',
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
                'name' => 'parent_id',
                'options' => array(
                    'label' => 'Parent Category',
                    'value_options' =>
                        array('' => '--Select one--') +
                        Arr::keyValue(
                            Categories::categoris_list(),
                            'category_id',
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