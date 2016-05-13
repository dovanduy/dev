<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormInput as OriginalFormInput;
use Zend\Form\ElementInterface;

class FormSlider extends OriginalFormInput {
    
    public function __invoke(ElementInterface $element = null)
    { 	echo 10;
        // invoke parent and get form text
        $originalElement = parent::__invoke($element);
        return "<div class=\"form-input\">{$originalElement}</div>";
    }
    
}
