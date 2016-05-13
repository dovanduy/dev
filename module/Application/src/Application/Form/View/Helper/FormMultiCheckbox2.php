<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormMultiCheckbox as OriginalFormMultiCheckbox;
use Zend\Form\ElementInterface;
use Zend\Stdlib\ArrayUtils;

class FormMultiCheckbox2 extends OriginalFormMultiCheckbox {
    
    public function __invoke(ElementInterface $element = null)
    {   
        $originalElement = parent::__invoke($element);
        return $originalElement;
    }
    
    /**
     * Render options
     *
     * @param  MultiCheckboxElement $element
     * @param  array                $options
     * @param  array                $selectedOptions
     * @param  array                $attributes
     * @return string
     */
    protected function renderOptions(\Zend\Form\Element\MultiCheckbox $element, array $options, array $selectedOptions, array $attributes)
    { 
        $options = parent::renderOptions($element, $options, $selectedOptions, $attributes);
        return "<div class=\"checkbox\">{$options}</div>";
    }  

}
