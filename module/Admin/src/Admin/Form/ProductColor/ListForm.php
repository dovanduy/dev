<?php

namespace Admin\Form\ProductColor;

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
                    'value' => $this->translate('Add Product Color'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/productcolors', 
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
                'name' => 'color_id',
                'title' => 'ID',
                'sort' => true,
                'attributes' => array(
                ),
            ),
            array(
                'name' => 'code',                
                'title' => 'Color code',                
                'sort' => true,
                'attributes' => array(
                  
                ),
            ), 
            array(
                'name' => 'name',                
                'title' => 'Color name',                
                'sort' => true,
                'attributes' => array(
                  
                ),
            ),             
            array(
                'name' => 'short',                
                'title' => 'Short',                
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
                    'id' => 'sort_{size_id}',
                    'name' => 'sort[{size_id}]',
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
            ),
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute(
                        'admin/productcolors', 
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