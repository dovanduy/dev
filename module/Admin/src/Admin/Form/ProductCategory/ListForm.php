<?php

namespace Admin\Form\ProductCategory;

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
                    'value' => $this->translate('Add Product Category'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/productcategories', 
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
                'name' => 'url_image',
                'type' => 'image',
                'title' => 'Photo',                                        
                'attributes' => array(
                    'src' => "{url_image}",
                    'width' => 50
                ),                     
            ),
            array(
                'name' => 'category_id',
                'title' => 'ID',
                'sort' => true,
                'attributes' => array(
                ),
            ),
            array(
                'name' => 'name',
                'title' => 'Category name',
                'sort' => true,
                'attributes' => array(
                    'value' => '{title}',
                    'style' => 'width:100%'
                ),
            ),            
            array(
                'name' => 'sort',
                'type' => 'text',
                'title' => 'Sort',
                'sort' => 'asc',
                'attributes' => array(
                    'name' => 'sort[{category_id}]',
                    'value' => '{sort}',                    
                    'class' => 'number'
                ),
            ),
            array(
                'name' => 'featured',
                'type' => 'toggle',               
                'title' => 'Featured',   
                'attributes' => array(
                    'id' => "featured",
                    'data-field' => 'featured',
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
                        'admin/productcategories', 
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