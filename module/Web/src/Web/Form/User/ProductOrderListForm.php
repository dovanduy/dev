<?php

namespace Web\Form\User;

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
class ProductOrderListForm extends AbstractForm
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
                'name' => 'save',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                    'class' => 'btn btn-primary',
                ),
            ),
            array(
                'name' => 'addnew',
                'attributes' => array(
                    'type' => 'button',
                    'value' => 'Add new',
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
                'title' => 'code',
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
                'name' => 'address',
                'title' => 'Address', 
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
            )                       
        );
    }

}