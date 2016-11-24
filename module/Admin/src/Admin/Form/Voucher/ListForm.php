<?php

namespace Admin\Form\Voucher;

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
                    'value' => $this->translate('Add new'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/vouchers', 
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
                'name' => 'phone',
                'title' => 'Phone',
                'sort' => true,
                'attributes' => array(
                    'width' => 150
                ),
            ), 
            array(
                'name' => 'code',
                'title' => 'Voucher code',
                'sort' => 'asc',
                'attributes' => array(
                    'width' => 200
                ),
            ),                           
            array(
                'name' => 'expired',
                'title' => 'Expired date', 
                'sort' => 'desc',
                'attributes' => array(
                    'datetime' => true,
                    'width' => 120
                ),
            ),   
            array(
                'name' => 'used',
                'type' => 'toggle',
                'title' => 'Used',   
                'attributes' => array(
                    'id' => "active",
                    'value' => "{_id}"
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
                        'admin/vouchers', 
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