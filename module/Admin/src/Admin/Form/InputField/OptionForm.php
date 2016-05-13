<?php

namespace Admin\Form\InputField;

use Application\Form\AbstractForm;

/**
 * List Option Form
 *
 * @package    Admin\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class OptionForm extends AbstractForm
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
        return array(
            array(
                'name' => 'saveOption',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }

    /**
     * Column array to create table
     *
     * @return array Array to create columns for table
     */
    public function columns()
    {
        return array( 
            array(
                'name' => 'option_id',
                'title' => 'ID',                
            ),
            array(
                'name' => 'name',
                'type' => 'text',
                'title' => 'Value',
                'attributes' => array(
                    'name' => 'name[{option_id}]',
                    'value' => '{name}'
                ),
            ),            
            array(
                'name' => 'sort',
                'type' => 'text',
                'title' => 'Sort',
                'attributes' => array(
                    'name' => 'sort[{option_id}]',
                    'value' => '{sort}',                    
                    'class' => 'number'
                ),
            ),
            array(
                'name' => 'active',
                'type' => 'toggle',
                'title' => 'Active',   
                'attributes' => array(
                    'id' => "active",
                    'value' => "{_id}"
                ),                              
            )          
        );
    }

}