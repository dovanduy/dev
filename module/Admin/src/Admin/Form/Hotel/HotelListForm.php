<?php

namespace Admin\Form\Hotel;

use Application\Form\AbstractForm;

/**
 * Hotel List Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class HotelListForm extends AbstractForm
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
             array(
                'name' => 'save',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'class' => 'btn btn-primary',
                ),
            ),
            array(
                'name' => 'addnew',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => 'Add new',
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/hotels', array('action' => 'update')) . "'"
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
                'name' => 'hotel_id',
                'title' => 'ID',
                'attributes' => array(
                    
                ),
            ),
            array(
                'name' => 'category_id',
                'title' => '_Id',
                'attributes' => array(
                     
                ),
            ),
            array(
                'name' => 'is_locale',
                'title' => 'Is_locale',
                'attributes' => array(
                    'value' => '{is_locale}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'email',
                'title' => 'Email',
                'attributes' => array(
                    'value' => '{email}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'url_website',
                'title' => 'Url Website',
                'attributes' => array(
                    'value' => '{url_website}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'street',
                'title' => 'Street',
                'attributes' => array(
                    'value' => '{street}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'name',
                'title' => 'Name',
                'attributes' => array(
                    'value' => '{name}',
                    'style' => 'width:100%'
                ),
            ),
            /*array(
                'name' => 'values',
                'title' => 'Values',
                'attributes' => array(
                    'value' => '{values}',
                    'style' => 'width:100%'
                ),
            ),
           /* array(
                'name' => 'iseq',
                'type' => 'text',
                'title' => 'Sort',
                'attributes' => array(
                    'name' => 'iseq[{hotel_id}]',
                    'value' => '{iseq}',
                    'style' => '
                        width:50px;
                        text-align:center;
                    ',
                    'class' => 'number'
                ),
            ),*/
            array(
                'name' => 'edit',
                'type' => 'link',
                'title' => 'Edit',
                'innerHtml' => '<i class="fa fa-fw fa-edit"></i>',
                'attributes' => array(
                    'href' => $this->getController()->url()->fromRoute('admin/hotels', array('action' => 'update', 'id' => '{hotel_id}'))
                ),
            ),
            array(
                'name' => 'delete',
                'type' => 'link',
                'title' => 'Delete',
                'innerHtml' => '<i class="fa fa-fw fa-remove"></i>',
                'attributes' => array(
                    'class' => 'confirmDelete',
                    'href' => $this->getController()->url()->fromRoute('admin/hotels', array('action' => 'delete', 'id' => '{hotel_id}'))
                ),
            )
        );
    }
    
}