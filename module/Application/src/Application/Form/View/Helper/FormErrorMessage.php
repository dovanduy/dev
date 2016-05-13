<?php

namespace Application\Form\View\Helper;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;
use Zend\Form\ElementInterface;

class FormErrorMessage extends BaseAbstractHelper {
    
    public function __invoke(ElementInterface $element = null)
    {       
		if (!empty($element->getMessages())) {
			$html = '<ul>';
			foreach ($element->getMessages() as $message) {
				$html .= "<li class=\"error\">{$message}</li>";
			}
			$html .= '</ul>';
			return $html;
		}
        return "";
    }
    
}
