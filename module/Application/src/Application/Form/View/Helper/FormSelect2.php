<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormSelect as OriginalFormSelect;
use Zend\Form\ElementInterface;
use Zend\Stdlib\ArrayUtils;

class FormSelect2 extends OriginalFormSelect {
    
    public function __invoke(ElementInterface $element = null)
    {     
        // invoke parent and get form select
        $name = $element->getName();
        $attributes = $element->getAttributes();
        $allow_options = '';
        if (!empty($attributes['allow_options'])) {
            $allow_options = $attributes['allow_options'];
        }
        $multiple = false;
        if (!empty($attributes['multiple'])) {
            $multiple = $attributes['multiple'];
        }
        if (empty($allow_options)) {
            //$allow_options = array();
        }
        $allow_options = json_encode($allow_options);
        $ajax = '';
        if (!empty($attributes['ajax_url'])) {
            $ajax = "
                minimumInputLength: 2,
                ajax: {
                    url: '{$attributes['ajax_url']}',
                    dataType: 'json',                   
                    type: 'GET',                   
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {                    
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,                                    
                                    id: item.id
                                }
                            })
                        };                   
                    },
                    cache: true
                }
            ";
        }        
        $this->getView()->headScript()->offsetSetScript(100 + rand(1, 1000), " 
        $(function() {    
            $(\"#{$name}\").hide();
            var allow_options = JSON.parse('{$allow_options}');
            if (allow_options) {
                $(\"#{$name} > option\").each(function() {
                    if (allow_options.indexOf(this.value) < 0) {
                        $(this).attr('disabled', 'disabled');
                    }         
                });
            }            
            $(\"#{$name}\").select2({
                templateResult: function (result) {
                    return result.text;
                },
                templateSelection: function (selection) {
                    var re = new RegExp('\\\|----', 'g');
                    var text = selection.text.replace(re, '');
                    return jQuery.trim(text);
                },
                {$ajax}
            });
            $(\"#{$name}\").show();
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
