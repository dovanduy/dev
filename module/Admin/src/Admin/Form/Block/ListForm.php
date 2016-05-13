<?php

namespace Admin\Form\Block;

use Application\Form\AbstractForm;

/**
 * Category List Form
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
                    'value' => $this->translate('Add Product Block'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/blocks', 
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
                'name' => 'name',
                'title' => 'Name',
                'sort' => true,
                'attributes' => array(
                  
                ),
            ),  
            array(
                'name' => 'url',
                'title' => 'Url',
                'sort' => true,
                'attributes' => array(
                ),
            ),       
            array(
                'name' => 'sort',
                'type' => 'text',
                'title' => 'Sort',
                'sort' => 'asc',
                'attributes' => array(
                    'id' => 'sort_{block_id}',
                    'name' => 'sort[{block_id}]',
                    'value' => '{sort}',                   
                    'class' => 'number'
                ),
            ), 
            array(
                'name' => 'action',
                'title' => '',               
                'type' => 'html',
                'innerHtml' => " 
                    <a  href='{$this->getController()->url()->fromRoute(
                            'admin/blocks', 
                            array('action' => 'update', 'id' => '{_id}')                        
                        )}'
                        title='{$this->getController()->translate('Edit')}'>
                        <i class='fa fa-fw fa-edit'></i>
                    </a>
                    &nbsp;
                    <a  href='{$this->getController()->url()->fromRoute(
                            'admin/blocks', 
                            array('action' => 'product', 'id' => '{_id}')                        
                        )}'
                        title='{$this->getController()->translate('Product list')}'>                       
                        <i class='fa fa-list'></i>
                    </a>                    
                ",                      
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
        );
    }

}