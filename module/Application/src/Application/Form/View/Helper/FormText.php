<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormText as OriginalFormText;
use Zend\Form\ElementInterface;

class FormText extends OriginalFormText {
    
    public function __invoke(ElementInterface $element = null)
    { 
        // invoke parent and get form text
        $originalElement = parent::__invoke($element);
        return "<div class=\"form-input\">{$originalElement}</div>";
    }
    
}
