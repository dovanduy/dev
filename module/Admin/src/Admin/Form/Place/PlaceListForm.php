<?php

namespace Admin\Form\Place;

use Admin\Module;
use Application\Form\AbstractForm;

/**
 * Place List Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class PlaceListForm extends AbstractForm
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
    public function elements() {  
        return array( 
            /*
            array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',                  
                    'class' => 'btn btn-primary',
                ),
            ),
            * 
            */
            array(
                'name' => 'addnew',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => 'Add new',                  
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/places', array('action' => 'add')) . "'"
                ),
            )
        );
    }
    
    /**
    * Column array to create table
    *
    * @return array Array to create columns for table
    */
    public function columns() {  
        return array(             
            array(
                'name' => 'name',
                'title' => 'Name',
                'sort' => 'asc',
                'attributes' => array(
                    
                ),                               
            ),
            array(
                'name' => 'country_name',
                'title' => 'Country',               
                'attributes' => array(
                     
                ),                               
            ),
            array(
                'name' => 'state_name',
                'title' => 'State',                
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_managed',
                'title' => 'Managed',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_read',
                'sort' => true,
                'title' => 'Read',
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_like',
                'title' => 'Like',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_comment',
                'title' => 'Comment',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_rate',
                'title' => 'Rate',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_rate_person',
                'title' => 'Rate/person',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_follow',
                'title' => 'Follow',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_favourite',
                'title' => 'Favourite',
                'sort' => true,
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'is_verified',
                'type' => 'toggle',
                'title' => 'Verified',   
                'sort' => true,
                'attributes' => array(
                    'id' => "is_verified",
                    'value' => "{_id}"
                ),                               
            ),
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute('admin/places', array('action' => 'update', 'id' => '{_id}'))
                ),                             
            )
        );
    } 
    
}