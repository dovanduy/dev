<?php

namespace Admin\Form\Category;

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
class CategoryListForm extends AbstractForm
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
                    'value' => 'Add new',
                    'class' => 'btn btn-primary',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/categories', array('action' => 'add')) . "'"
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
                'name' => 'image_url',
                'type' => 'image',
                'title' => 'Image',
                'attributes' => array(
                    'src' => "{image_url}",
                    'width' => 50
                ),
            ),
            array(
                'name' => 'category_id',
                'title' => 'ID',
                'attributes' => array(),
            ),
            array(
                'name' => 'name',
                'title' => 'Name',
                'attributes' => array(
                    'value' => '{title}',
                    'style' => 'width:100%'
                ),
            ),
            array(
                'name' => 'count_hotel',
                'title' => 'Total hotel',
                'attributes' => array(),
            ),
            array(
                'name' => 'iseq',
                'type' => 'text',
                'title' => 'Sort',
                'attributes' => array(
                    'name' => 'iseq[{category_id}]',
                    'value' => '{iseq}',
                    'style' => '
                        width:50px;
                        text-align:center;
                    ',
                    'class' => 'number'
                ),
            ),
            array(
                'name' => 'edit',
                'type' => 'html',
                'title' => 'Edit',
                'innerHtml' => $this->getEditLink($this->getController()->url()->fromRoute('admin/categories', array('action' => 'update', 'id' => '{_id}')))
            )
        );
    }

}