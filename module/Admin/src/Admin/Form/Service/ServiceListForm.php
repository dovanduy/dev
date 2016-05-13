<?php

namespace Admin\Form\Service;

use Application\Form\AbstractForm;

/**
 * Service List Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ServiceListForm extends AbstractForm
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
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/services', array('action' => 'update')) . "'"
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
                'name' => 'service_id',
                'title' => 'ID',
                'attributes' => array(
                    
                ),
            ),
            array(
                'name' => '_id',
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
                'name' => 'tag',
                'title' => 'Tag',
                'attributes' => array(
                    'value' => '{tag}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'type',
                'title' => 'Type',
                'attributes' => array(
                    'value' => '{type}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'parent_id',
                'title' => 'Parent_id',
                'attributes' => array(
                    'value' => '{parent_id}',
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
                    'name' => 'iseq[{service_id}]',
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
                    'href' => $this->getController()->url()->fromRoute('admin/services', array('action' => 'update', 'id' => '{service_id}'))
                ),
            ),
            array(
                'name' => 'delete',
                'type' => 'link',
                'title' => 'Delete',
                'innerHtml' => '<i class="fa fa-fw fa-remove"></i>',
                'attributes' => array(
                    'class' => 'confirmDelete',
                    'href' => $this->getController()->url()->fromRoute('admin/services', array('action' => 'delete', 'id' => '{service_id}'))
                ),
            )
        );
    }
    
}