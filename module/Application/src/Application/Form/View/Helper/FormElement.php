<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormElement as OriginalFormElement;
use Zend\Form\ElementInterface;

class FormElement extends OriginalFormElement {
    
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof \Application\Form\Element\DateCalendar) {
            $helper = $renderer->plugin('form_datecalendar');
            return $helper($element);
        }
        
        if ($element instanceof \Application\Form\Element\CKEditor) {
            $helper = $renderer->plugin('form_ckeditor');
            return $helper($element);
        }
        
        if ($element instanceof \Application\Form\Element\NewSelect) {          
            $helper = $renderer->plugin('form_newselect');
            return $helper($element);
        }
        
        if ($element instanceof \Application\Form\Element\Select2) {
            $helper = $renderer->plugin('form_select2');
            return $helper($element);
        }
        
        if ($element instanceof \Application\Form\Element\MultiCheckbox2) {
            $helper = $renderer->plugin('form_multi_checkbox2');
            return $helper($element);
        }
        
        $renderedInstance = $this->renderInstance($element);

        if ($renderedInstance !== null) {
            return $renderedInstance;
        }

        $renderedType = $this->renderType($element);

        if ($renderedType !== null) {
            return $renderedType;
        }

        
        
        return $this->renderHelper($this->defaultHelper, $element);
    }
}
