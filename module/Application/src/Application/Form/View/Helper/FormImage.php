<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormFile as OriginalFormFile;
use Zend\Form\ElementInterface;

class FormImage extends OriginalFormFile {
    
    public function __invoke(ElementInterface $element = null)
    { 
        // invoke parent and get form file
        $deleteLabel = 'Delete';
        if (null !== ($translator = $this->getTranslator())) {
            $deleteLabel = $translator->translate(
                'Delete',
                $this->getTranslatorTextDomain()
            );
            $mainLabel = $translator->translate(
                'Main photo',
                $this->getTranslatorTextDomain()
            );
        }
        $originalElement = parent::__invoke($element);
        
        $value = $element->getValue();       
        if (!is_string($value)) {
            $value = '';
        }
        $img = "<div class=\"img-preview-75\">";
        $img .= "<a href=\"{$value}\" class=\"" . (empty($value) ? 'bg-no-image' : 'js-thumb') . "\">";
        if (!empty($value)) {
            $img .= "<img src=\"{$value}\" />";
        }
        $img .= "</a></div>";       
        $originalElement .= $img;
       
        $html = "<div class=\"col-md-3\">{$originalElement}</div>";
        $html .= "<div class=\"col-md-3\"><input " . ($element->getOption('is_main') ? 'checked' : '') . " name=\"is_main\" type=\"radio\" value=\"{$element->getName()}\">&nbsp;{$mainLabel}</div>";
        if (!empty($element->getOption('colors'))) {         
            $colorElement = "<select style=\"padding:2px;\" name=\"color_{$element->getName()}\">";
            $colorElement .= "<option value=\"\">---</option>";
            foreach ($element->getOption('colors') as $color) {
                $selected = '';
                if (!empty($value) && $color['url_image'] == $value) {
                    $selected = 'selected';
                }
                $colorElement .= "<option {$selected} value=\"{$color['color_id']}\">{$color['name']}</option>";
            }      
            $colorElement .= "</select>";  
            $html .= "<div class=\"col-md-3\">{$colorElement}</div>";
        }        
        if (!empty($value) && is_string($value) && $element->getOption('allow_empty') == true) {
            $html .= "<div class=\"col-md-3\"><input name=\"remove_{$element->getName()}\" type=\"checkbox\" value=\"{$element->getName()}\">&nbsp;{$deleteLabel}</div>";                             
        }        
        return "<div class=\"image\"/><div class=\"row\">{$html}</div></div>";
    }
    
}
