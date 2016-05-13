<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormLabel as OriginalFormLabel;
use Zend\Form\ElementInterface;

class FormLabel extends OriginalFormLabel {
    
    public function __invoke(ElementInterface $element = null, $labelContent = null, $position = null)
    {
        // invoke parent and get form label
        $originalformLabel = parent::__invoke($element, $labelContent, $position);

        // check if element is required
        if ($element->hasAttribute('required') && $element->getAttribute('required') !== false) {
            return "<div class=\"form-label\">{$originalformLabel}<span class=\"required-mark\">*</span></div>";
        } else {
            return "<div class=\"form-label\">{$originalformLabel}</div>";
        }
    }

}
