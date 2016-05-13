<?php

namespace Admin\Form\Service;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;
use Zend\Validator\File;

/**
 * ServiceForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ServiceForm extends AbstractForm
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
                    'type' => 'hidden',
                ),
            ),
            /*array(
                'name' => 'is_locale',
                'attributes' => array(
                    'id' => 'is_locale',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Is_locale',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),*/
            array(
                'name' => 'tag',
                'attributes' => array(
                    'id' => 'tag',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 20,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'type',
                'attributes' => array(
                    'id' => 'type',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
            ),
            'options' => array(
                    'label' => 'Type',
            ),
            'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 20,
                            'messages' => array(
                                    StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                    StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'iseq',
                'attributes' => array(
                        'id' => 'Iseq',
                        'type' => 'text',
                        'required' => true,
                        'class' => 'form-control'
                ),
                'options' => array(
                        'label' => 'Iseq',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                    StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                    StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'parent_id',
                'attributes' => array(
                        'id' => 'type',
                        'type' => 'text',
                        'required' => false,
                        'class' => 'form-control'
                ),
                'options' => array(
                        'label' => 'Parent_id',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                    StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                    StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'type',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'values',
                'attributes' => array(
                    'id' => 'type',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Values',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 2000,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
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
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/services') . "'"
                ),
            )
        );
    }
    
}