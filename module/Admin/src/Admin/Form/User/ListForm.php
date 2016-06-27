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
                            'admin/users', 
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
                'name' => 'email',
                'title' => 'Email',
                'sort' => true,
                'attributes' => array(
                    'width' => 150
                ),
            ), 
            array(
                'name' => 'name',
                'title' => 'Full name',
                'sort' => 'asc',
                'attributes' => array(
                    'width' => 150
                ),
            ), 
//            array(
//                'name' => 'display_name',
//                'title' => 'Display name',               
//                'sort' => true,
//            ),  
            array(            
                'name' => 'mobile',
                'title' => 'Mobile', 
                'sort' => true,
            ),
            /*
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
             * 
             */    
            array(
                'name' => 'address',
                'title' => 'Address',               
                'attributes' => array(
                    
                ),
            ),
            array(
                'name' => 'updated',
                'title' => 'Updated', 
                'sort' => 'desc',
                'attributes' => array(
                    'datetime' => true,
                    'width' => 120
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
                        'admin/users', 
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