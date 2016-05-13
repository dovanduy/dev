<?php

namespace Web\Form\Product;

use Application\Form\AbstractForm;

/**
 * Form
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class ReviewForm extends AbstractForm
{  
    /**
    * Form construct
    *
    * @param string $name Form name
    */
    public function __construct($name = null)
    {
        parent::__construct($name); 
    }
    
    /**
    * Element array to create form
    *
    * @return array Array to create elements for form
    */
    public function elements() {
        $elements = array(      
            
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Full name',
                ),
                'validators' => \Web\Module::getValidatorConfig('product_reviews.name')
            ),
            /*
            array(
                'name' => 'title',
                'allow_empty' => true,
                'attributes' => array(
                    'id' => 'title',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Title',
                ),
                'validators' => \Web\Module::getValidatorConfig('product_reviews.title')
            ),
             * 
             */
                        
            array(
                'name' => 'content',
                'attributes' => array(
                    'id' => 'content',
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control',
                    'rows' => 4
                ),
                'options' => array(
                    'label' => 'Review',                            
                ),
                'validators' => \Web\Module::getValidatorConfig('product_reviews.content')
            ),
            
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'rating',
                'options' => array(
                    'label' => 'Rating',
                    'value_options' => array(
                        '0' => '--Choose one--',
                        '1' => '1 Star',
                        '2' => '2 Stars',
                        '3' => '3 Stars',
                        '4' => '4 Stars',
                        '5' => '5 Stars',
                    )
                ),
                'attributes' => array(
                    'id' => 'rating',
                    'class' => 'form-control',
                    'required' => false,
                )
            ),
        );          
        $elements = array_merge(
            $elements,
            array(
                array(
                    'name' => 'send',
                    'attributes' => array(
                        'type'  => 'button',
                        'value' => 'Send',
                        'id' => 'submitbutton',
                        'class' => 'submit-button btn btn-default ajax-submit',  
                        'data-callback' => "
                            var data = {
                                loadreviews: 1
                            }             
                            $.ajax({
                                type: 'POST',
                                url: window.location.href,
                                data: data,
                                success: function (response) {
                                    if (response) {
                                        $('#review-list').html(response); 
                                        frm.trigger('reset');
                                    }
                                }
                            });                             
                        ", 
                    ),
                ),                
            )
        );
        return $elements;
    }
    
}