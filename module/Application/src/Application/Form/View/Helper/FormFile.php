<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormFile as OriginalFormFile;
use Zend\Form\ElementInterface;

class FormFile extends OriginalFormFile {
    
    public function __invoke(ElementInterface $element = null)
    { 
        // invoke parent and get form file
        $deleteLabel = 'Delete';
        if (null !== ($translator = $this->getTranslator())) {
            $deleteLabel = $translator->translate(
                'Delete',
                $this->getTranslatorTextDomain()
            );
        }
        $originalElement = parent::__invoke($element);
        $value = $element->getValue();
        if ($element->getOption('is_image') == true) {
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
        }
        if (!empty($value) && is_string($value) && $element->getOption('allow_empty') == true) {
            $remove = "<input name=\"remove[{$element->getName()}]\" type=\"checkbox\" value=\"{$value}\">&nbsp;{$deleteLabel}";                    
            $originalElement .= $remove;
        }
        $class = $element->getOption('is_image') ? 'image' : $element->getName();
        return "<div class=\"{$class}\"/>" . $originalElement . "</div>";
    }
    
}
