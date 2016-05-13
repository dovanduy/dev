<?php

namespace Admin\Form;
use Admin\Form\AbstractForm;
 
class AlbumForm extends AbstractForm
{
    public function __construct($name = null)
    {
        parent::__construct($name);         
        $this->setAttribute('method', 'post');
        $this->setAttribute('novalidate', 'novalidate');
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
         
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'gender',
            'options' => array(
                'label' => 'Gender',
                'value_options' => array(
                    '1' => 'Select your gender',
                    '2' => 'Female',
                    '3' => 'Male'
                ),
            ),
            'attributes' => array(
                'value' => '1' //set selected to '1'
            )
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'hobby',
            'options' => array(
                'label' => 'Please choose one/more of the hobbies',
                'value_options' => array(
                    '1' =>'Cooking',
                    '2'=>'Writing',
                    '3'=>'Others'
                ),
            ),
            'attributes' => array(
                'value' => '1' //set checked to '1'
            )
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'placeholder' => 'you@domain.com'
            )
        ));
  
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'birth',
            'options' => array(
                'label' => 'Birth'
            )
        ));
         
        $this->add(array(
            'name' => 'address',
            'attributes'=>array(
                'type'=>'textarea' 
            ),
            'options' => array(
                'label' => 'Address',
            ),
        ));
         
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'direction',
            'options' => array(
                'label' => 'Please choose one of the directions',
                'value_options' => array(
                    '1' => 'Programming',
                    '2' => 'Design',
                ),
            ),
            'attributes' => array(
                'value' => '1' //set checked to '1'
            )
        ));
         
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }   
}
