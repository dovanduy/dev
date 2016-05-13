<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormInput as OriginalFormInput;
use Zend\Form\ElementInterface;

class FormDateCalendar extends OriginalFormInput {
    
    public function __invoke(ElementInterface $element = null)
    { 
        // invoke parent and get form input
        $name = $element->getName();
        $value = $element->getValue();
        $attributes = $element->getAttributes();
        if (!empty($attributes['datetimepicker']) && $attributes['datetimepicker'] == true) {
            if (!empty($value) && is_numeric($value)) {
                $element->setValue(date('Y-m-d H:i', $value));
            }
            /*
            $this->getView()->headScript()->offsetSetScript(100, " 
            $(function() {    
                $(\".datetimepicker\").datetimepicker({
                    format: 'YYYY-MM-DD HH:mm',
                    showTodayButton:true,
                    showClear:true,
                    showClose:true,
                    locale:'en',
                    stepping:1
                });                
            });
            ");
            * 
            */
            $element->setAttributes(array(
                'class' => $attributes['class'] . ' datetimepicker'
            ));
            $originalElement = parent::__invoke($element);
            return $originalElement;
            /*
            return "   
                <div class=\"input-group date\">
                    {$originalElement}
                    <span class=\"input-group-addon\">
                        <span class=\"glyphicon glyphicon-calendar\"></span>
                    </span>
                </div>
                ";
             * 
             */
        } else {
            if (!empty($value) && is_numeric($value)) {
                $element->setValue(date('Y-m-d', $value));
            }
            $element->setAttributes(array(
                'class' => $attributes['class'] . ' datepicker'
            ));
            /*
            $this->getView()->headScript()->offsetSetScript(100, " 
            $(function() {    
                $(\"#{$name}\").datepicker({
                    format: 'yyyy-mm-dd',                           
                    clearBtn: true,
                    todayHighlight: true,                   
                });                         
            });
            ");
            * 
            */ 
            $originalElement = parent::__invoke($element);
            return $originalElement;
        }        
    }
    
}
