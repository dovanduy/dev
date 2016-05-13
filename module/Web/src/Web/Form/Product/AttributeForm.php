<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;

/**
 * List Field Form
 *
 * @package    Admin\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AttributeForm extends AbstractForm
{
    
    /**
     * Table construct
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Element array to create form
     *
     * @return array Array to create elements for form
     */
    public function elements()
    {
        $elements = array();
        $dataset = $this->getDataset();
        foreach ($dataset as $row) {
            $fieldId = $row['field_id'];
            $type = 'text';            
            $options = array(
                'label' => $row['name']
            );
            $class = 'form-control';
            switch ($row['type']) {
                case 'select':
                    $type = 'Zend\Form\Element\Select';                    
                    $options = $row['options'];
                    $options['value_options'] = array('' => '--Select one--') + $options['value_options'];
                    break;
                case 'checkbox':
                    $type = 'Zend\Form\Element\MultiCheckbox'; 
                    $options = $row['options'];
                    $class = '';
                    break;
                case 'radio':
                    $type = 'Zend\Form\Element\Radio'; 
                    $options = $row['options'];
                    $class = '';
                    break;
            }
            $elements[] = array(
                'type' => $type,
                'name' => "field[{$fieldId}]",
                'options' => $options,
                'attributes' => array(
                    'id' => "field_{$fieldId}",
                    'class' => $class,
                )
            );
        }
        $elements = array_merge(
            $elements,
            array(                
                array(
                    'name' => 'saveAndBack',
                    'attributes' => array(
                        'type'  => 'submit',
                        'value' => 'Save And Back',
                        'id' => 'saveAndBackButton',
                        'class' => 'btn btn-primary'                    
                    ),
                ),
                array(
                    'name' => 'save',
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
                        'value' => 'Cancel',                  
                        'class' => 'btn',
                        'onclick' => "location.href='" . base64_decode($this->getController()->params()->fromQuery('backurl')) . "'"
                    ),
                )
            )
        );
        return $elements;
    }
    
}