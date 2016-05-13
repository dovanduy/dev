<?php

namespace Application\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;
use Zend\Form\ElementInterface;

class FormElementErrors extends OriginalFormElementErrors  
{
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul%s><li class="error">';
    protected $messageSeparatorString = '</li><li class="error">';
    
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()} if an element is passed.
     *
     * @param  ElementInterface $element
     * @param  array            $attributes
     * @return string|FormElementErrors
     */
    public function __invoke(ElementInterface $element = null, array $attributes = [])
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element, $attributes);
    }
    
}
