<?php

namespace Admin\Form\Menu;

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
                    'value' => $this->translate('Add Menu'),
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/menus', 
                            array('action' => 'add'),
                            array(
                                'query' => array(
                                    'tab' => $this->getRequest()->getQuery('tab', ''),
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
                'name' => 'menu_id',
                'title' => 'ID',
                'sort' => true,
                'attributes' => array(
                    
                ),
            ),            
            array(
                'name' => 'name',
                'title' => 'Name',
                'sort' => true,
                'attributes' => array(
                    'value' => '{title}',
                    'style' => 'width:100%'
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
                    'name' => 'sort[{menu_id}]',
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
                        'admin/menus', 
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