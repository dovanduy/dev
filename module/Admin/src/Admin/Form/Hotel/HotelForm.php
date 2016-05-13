<?php

namespace Admin\Form\Hotel;

use Application\Form\AbstractForm;
use Zend\Validator\StringLength;
use Zend\Validator\File;

/**
 * HotelForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class HotelForm extends AbstractForm
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
        return array(
            array(
                'name' => 'hotel_id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ),
            array(
                'name' => 'category_id',
                'attributes' => array(
                    'id' => 'category_id',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Category Id',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'star',
                'attributes' => array(
                    'id' => 'star',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
            ),
            'options' => array(
                    'label' => 'Star',
            ),
            'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 4,
                            'messages' => array(
                                    StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                    StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'street',
                'attributes' => array(
                        'id' => 'street',
                        'type' => 'text',
                        'required' => true,
                        'class' => 'form-control'
                ),
                'options' => array(
                        'label' => 'Street',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                    StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                    StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'city_code',
                'attributes' => array(
                    'id' => 'city_code',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'City Code',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 24,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'state_code',
                'attributes' => array(
                    'id' => 'state_code',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'State Code',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 24,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'country_code',
                'attributes' => array(
                    'id' => 'country_code',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Country Code',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 2,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'tel',
                'attributes' => array(
                    'id' => 'tel',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tel',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'fax',
                'attributes' => array(
                    'id' => 'fax',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Fax',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'url_website',
                'attributes' => array(
                    'id' => 'url_website',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Url Website',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 255,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'hotline',
                'attributes' => array(
                    'id' => 'hotline',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Hotline',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 30,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'email',
                'attributes' => array(
                    'id' => 'email',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Email',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 255,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'image_id',
                'attributes' => array(
                    'id' => 'image_id',
                    'type' => 'text',
                    'required' => false,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Image Id',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'co_read',
                'attributes' => array(
                    'id' => 'co_read',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Co. Read',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'co_like',
                'attributes' => array(
                    'id' => 'co_like',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Co. Like',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'co_comment',
                'attributes' => array(
                    'id' => 'co_comment',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Co. Comment',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'co_rated',
                'attributes' => array(
                    'id' => 'co_rated',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Co. Rated',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'co_rated_person',
                'attributes' => array(
                    'id' => 'co_rated_person',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Co. Rated Person',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'last_comment_id',
                'attributes' => array(
                    'id' => 'last_comment_id',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Last comment Id',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'is_locale',
                'attributes' => array(
                    'id' => 'is_locale',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Is locale',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'lat',
                'attributes' => array(
                    'id' => 'lat',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Latitude',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'lng',
                'attributes' => array(
                    'id' => 'lng',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Longtitude',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'is_verified',
                'attributes' => array(
                    'id' => 'is_verified',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Is verified',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 4,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'expires_at',
                'attributes' => array(
                    'id' => 'expires_at',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Expired at',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 11,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'name',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 255,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'short',
                'attributes' => array(
                    'id' => 'short',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Short',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 255,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'tag',
                'attributes' => array(
                    'id' => 'tag',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'content',
                'attributes' => array(
                    'id' => 'content',
                    'type' => 'textarea',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Content',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 1000,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'ft_search',
                'attributes' => array(
                    'id' => 'ft_search',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Feature search',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 255,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'name',
                'attributes' => array(
                    'id' => 'type',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Name',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 50,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'tag',
                'attributes' => array(
                    'id' => 'tag',
                    'type' => 'text',
                    'required' => true,
                    'class' => 'form-control'
                ),
                'options' => array(
                    'label' => 'Tag',
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->translate('The input is less than %s characters long', 1),
                                StringLength::TOO_LONG => $this->translate('The input is more than %s characters long', 100)
                            )
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Save',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary'
                ),
            ),
            array(
                'name' => 'cancel',
                'attributes' => array(
                    'type'  => 'button',
                    'value' => $this->translate('Cancel'),
                    'class' => 'btn',
                    'onclick' => "location.href='" . $this->getController()->url()->fromRoute('admin/hotels') . "'"
                ),
            )
        );
    }
    
}