<?php

namespace Admin\Form\User;

use Application\Form\AbstractForm;

/**
 * List Form
 *
 * @package    Admin\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AddressListForm extends AbstractForm
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
                'name' => 'addnew',
                'attributes' => array(
                    'type' => 'button',
                    'value' => 'Add Address',
                    'class' => 'btn btn-primary show-model',
                    'data-modelid' => '#add-address-modal',
                ),
            )
        );;
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
                'name' => 'name',
                'title' => 'Name',                       
            ),           
            array(
                'name' => 'country_name',
                'title' => 'Country',                         
            ), 
            array(
                'name' => 'state_name',
                'title' => 'State/Province',                             
            ), 
            array(
                'name' => 'city_name',
                'title' => 'City/District',
            ),    
            array(
                'name' => 'street',
                'title' => 'Street',
            ),
            array(
                'name' => 'active',
                'type' => 'toggle',
                'title' => 'Active',   
                'attributes' => array(
                    'id' => "active",
                    'value' => "{_id}"
                ),                              
            ), 
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'data-_id' => '{_id}',
                    'data-name' => '{name}',
                    'data-country_code' => '{country_code}',
                    'data-state_code' => '{state_code}',
                    'data-city_code' => '{city_code}',
                    'data-street' => '{street}',
                    'class' => 'show-model',
                    'data-modelid' => '#add-address-modal',
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/users', 
                        array(
                            'action' => 'update', 
                            'id' => '{_user_id}'
                        ),
                        array(
                            'query' => array(
                                'tab' => 'address',
                                'addressid' => '{_id}'
                            )
                        )
                    )
                ),                             
            )            
        );
    }

}