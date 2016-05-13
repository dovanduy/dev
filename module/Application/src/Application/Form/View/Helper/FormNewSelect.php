<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormSelect as OriginalFormSelect;
use Zend\Form\ElementInterface;
use Zend\Stdlib\ArrayUtils;

class FormNewSelect extends OriginalFormSelect {
    
    public function __invoke(ElementInterface $element = null)
    {     
        // invoke parent and get form select
        $name = $element->getName();        
        $this->getView()->headScript()->offsetSetScript(100, " 
        $(function() {    
            $(\"#{$name}\").change(function(){ 
                var text = $('#{$name} option:selected').text();               
                var re = new RegExp('\\\|----', 'g');
                text = text.replace(re, '');
                text = jQuery.trim(text);        
                $('#{$name} option:selected').text(text); 
            });
        });
        ");
        $originalElement = parent::__invoke($element);
        return $originalElement;
    }
    
    /**
     * Render an array of options
     *
     * Individual options should be of the form:
     *
     * <code>
     * array(
     *     'value'    => 'value',
     *     'label'    => 'label',
     *     'disabled' => $booleanFlag,
     *     'selected' => $booleanFlag,
     * )
     * </code>
     *
     * @param  array $options
     * @param  array $selectedOptions Option values that should be marked as selected
     * @return string
     */
    public function renderOptions(array $options, array $selectedOptions = [])
    {
        $template      = '<option %s>%s</option>';
        $optionStrings = [];
        $escapeHtml    = $this->getEscapeHtmlHelper();

        foreach ($options as $key => $optionSpec) {
            $value    = '';
            $label    = '';
            $selected = false;
            $disabled = false;

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key
                ];
            }

            if (isset($optionSpec['options']) && is_array($optionSpec['options'])) {
                $optionStrings[] = $this->renderOptgroup($optionSpec, $selectedOptions);
                continue;
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }

            if (ArrayUtils::inArray($value, $selectedOptions)) {
                $selected = true;
            }

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            $attributes = compact('value', 'selected', 'disabled');

            if (isset($optionSpec['attributes']) && is_array($optionSpec['attributes'])) {
                $attributes = array_merge($attributes, $optionSpec['attributes']);
            }

            $this->validTagAttributes = $this->validOptionAttributes;
            
            $optionStrings[] = sprintf(
                $template,
                $this->createAttributesString($attributes),
                $label
            );
        }

        return implode("\n", $optionStrings);
    }
    
}
