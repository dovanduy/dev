<?php

namespace Admin\Form\Festival;

use Admin\Module;
use Application\Form\AbstractForm;

/**
 * Festival List Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      caolp
 * @copyright   YouGo INC
 */
class FestivalListForm extends AbstractForm
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
                'attributes' => array(
                     
                ),                               
            ),
            array(
                'name' => 'city_name',
                'title' => 'Country',
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
                'name' => 'count_image',
                'title' => 'Image',
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_like',
                'title' => 'Like',
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_comment',
                'title' => 'Comment',
                'attributes' => array(
                ),                               
            ),
            array(
                'name' => 'count_share',
                'title' => 'Share',
                'attributes' => array(
                ),
            ),
            array(
                'name' => 'count_rate_person',
                'title' => 'Rate/person',
                'attributes' => array(
                ),                               
            ),
            array(            
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',                                
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute('admin/festivals', array('action' => 'update', 'id' => '{_id}'))
                ),                             
            )
        );
    } 
    
}