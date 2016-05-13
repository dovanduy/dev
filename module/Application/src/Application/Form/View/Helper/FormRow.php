<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormRow as OriginalFormRow;
use Zend\Form\ElementInterface;

class FormRow extends OriginalFormRow {
    
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
    {
        // invoke parent and get form row       
        $originalFormRow = parent::__invoke($element, $labelPosition, $renderErrors, $partial);
		//echo $element
        switch ($element->getAttribute('type')) {
            case 'submit':
            case 'button':
                return "<div class=\"form-group button-group form-group-{$element->getAttribute('name')}\">" . $originalFormRow . "</div>";
            default:
                return "<div class=\"form-group form-group-{$element->getAttribute('name')}\">" . $originalFormRow . "</div>";
        }      
    }

}
