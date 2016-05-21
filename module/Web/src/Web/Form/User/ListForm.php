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
                    'value' => 'Add new',
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . 
                        $this->getController()->url()->fromRoute(
                            'admin/news', 
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
                    
                ),
            ), 
            array(
                'name' => 'name',
                'title' => 'Name',
                'sort' => 'asc',
                'attributes' => array(
                    
                ),
            ), 
            array(
                'name' => 'display_name',
                'title' => 'Display name',               
                'sort' => true,
            ),  
            array(            
                'name' => 'mobile',
                'title' => 'Mobile', 
                'sort' => true,
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