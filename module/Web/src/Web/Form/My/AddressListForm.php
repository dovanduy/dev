<?php

namespace Web\Form\My;

use Application\Form\AbstractForm;

/**
 * List Form
 *
 * @package Web\Form
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
                    'value' => $this->getController()->translate('Add new address'),
                    'class' => 'btn btn-default show-model',
                    'data-modelid' => '#add-address-modal',
                    'data-modal_title' => $this->getController()->translate('Add new address'),
                    'onclick' => "
                        $('#street').val('');
                    ",
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
            /*
            array(
                'name' => 'name',
                'title' => $this->getController()->translate('Address type'),                       
            ),     
            * 
            */      
            array(
                'name' => 'address',
                'title' => $this->getController()->translate('Address'),                       
            ),          
            array(
                'name' => 'action',
                'title' => '',               
                'type' => 'html',
                'innerHtml' => " 
                    <a  href='#'
                        title='{$this->getController()->translate('Edit address')}',
                        data-_id='{_id}'
                        data-name='{name}'
                        data-country_code='{country_code}',
                        data-state_code='{state_code}',
                        data-city_code='{city_code}',
                        data-street='{street}',
                        data-modal_title='{$this->getController()->translate('Edit address')}',
                        class='show-model',
                        data-modelid='#add-address-modal'><i class='fa fa-fw fa-edit'></i></a>
                    &nbsp;
                    <a  href='#'
                        title='{$this->getController()->translate('Delete address')}',
                        class='ajax-submit'
                        data-_id='{_id}'       
                        data-confirmmessage='{$this->getController()->translate('Are you sure delete this address?')}'
                        data-url='{$this->getController()->url()->fromRoute(
                            'web/checkout', 
                            array('action' => 'removeaddress', 'id' => '{_id}')                        
                        )}'><i class='fa fa-fw fa-remove'></i></a>
                ",                      
            ),   
            /*
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => $this->getController()->translate('Edit'),
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
            ),
            
            array(            
                'name' => 'delete',
                'type' => 'link',
                'title' => $this->getController()->translate('Delete'),
                'innerHtml' => '<i class="fa fa-fw fa-remove"></i>',                                
                'attributes' => array(
                    'class' => 'ajax-submit',
                    'href' => '#',
                    'data-_id' => '{_id}', 
                    'data-url' => $this->getController()->url()->fromRoute(
                        'web/checkout', 
                        array(
                            'action' => 'removeaddress',
                            'id' => '{_id}', 
                        )                        
                    ),
                    'data-confirmmessage' => $this->getController()->translate( 'Are you sure?'),
                    'data-callback' => "                        
                        var row = btn.closest(\"tr\"); 
                        row.remove();
                    "
                ),                             
            ),
            * 
            */
        );
    }

}