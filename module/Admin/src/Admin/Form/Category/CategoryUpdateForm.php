<?php

namespace Admin\Form\Category;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;
use Zend\Validator\File;
use Application\Lib\Arr;
use Admin\Model\Categories;
use Admin\Module;

/**
 * CategoryForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class CategoryUpdateForm extends AbstractForm
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
                'name' => '_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'parent_id',
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(
                    'id' => 'parent_id',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Parent Category',
                    'value_options' =>
                        array('' => '--Select one--') +
                        Arr::keyValue(
                            Categories::categoris_list($this->getController()->params()->fromRoute('id', 0)),
                            '_id',
                            'name'
                        )

                ),
            ),
            array(
                'name' => 'iseq',
                'attributes' => array(
                    'id' => 'iseq',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Sort',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 11)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'url_image',
                'attributes' => array(
                    'id' => 'url_image',
                    'type' => 'file',
                    'required' => false,
                    'no_filters' => true,
                ),
                'options' => array(
                    'label' => 'Image',
                    'is_image' => true, // custom
                    'allow_empty' => true, // custom
                ),
                'validators' => \Admin\Module::getValidatorConfig('general.image')
            ),
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary'                    
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),                  
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/categories') . "'"
                ),
            )
        );
    }
    
}