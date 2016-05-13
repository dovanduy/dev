<?php

namespace Admin\Form\ProductOrder;

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
class ListForm extends AbstractForm
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
                    'value' => $this->translate('Add Order'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/productorders', 
                            array('action' => 'add'),
                            array(
                                'query' => array(
                                    'backurl' => base64_encode($this->getRequest()->getRequestUri())
                                )
                            )
                        ) . "'"
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
                'name' => 'code',
                'type' => 'link',
                'title' => 'Code',
                'innerHtml' => '{code}',                                
                'attributes' => array(
                    'href' => '#',
                    'class' => 'show-model',
                    'data-modelid' => '#dialog-modal',                    
                    'data-url' => $this->getController()->url()->fromRoute(
                        'admin/productorders', 
                        array(
                            'action' => 'detail', 
                            'id' => '{_id}'
                        )                        
                    )
                ),                             
            ),
            array(            
                'name' => 'state_name',
                'title' => 'State/Province', 
                'sort' => true,
            ),
            array(            
                'name' => 'city_name',
                'title' => 'City/District', 
                'sort' => true,
            ),
            array(            
                'name' => 'street',
                'title' => 'Address', 
                'sort' => true,
            ),
            array(            
                'name' => 'user_name',
                'title' => 'Customer name', 
                'sort' => true,
            ),
            array(            
                'name' => 'user_email',
                'title' => 'Email', 
                'sort' => true,
            ),
            array(            
                'name' => 'user_mobile',
                'title' => 'Mobile', 
                'sort' => true,
            ),
            array(
                'name' => 'total_money',
                'title' => 'Total money', 
                'sort' => true,
                'attributes' => array(
                    'number' => true
                ),
            ),
            array(
                'name' => 'created',
                'title' => 'Created', 
                'sort' => 'desc',
                'attributes' => array(
                    'datetime' => true
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
            ),
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/productorders', 
                        array(
                            'action' => 'update', 
                            'id' => '{_id}'
                        ),
                        array(
                            'query' => array(
                                'backurl' => base64_encode($this->getRequest()->getRequestUri())
                            )
                        )
                    )
                ),                             
            )            
        );
    }

}