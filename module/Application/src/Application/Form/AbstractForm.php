<?php

namespace Application\Form;

use Admin\Model\Model;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * AbstractForm
 *
 * @package 	Admin\Form
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
abstract class AbstractForm extends Form {
    
    /**    
     * @var Zend\I18n\Translator\Translator
     */
    public $translator;
    
    public $controller;
    
    /**    
     * @var 
     */
    public $url;
    
    /**    
     * @var Array
     */
    public $dataset;
    
    /**    
     * @var Array
     */
    public $elementOptions = array();
    
    /**    
     * Default filters
     * @var Array
     */
    protected $filters = array(
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
    );
    
    /**
    * Form construct
    *
    * @param string $name Form name
    */
    public function __construct($name = null) {
        parent::__construct($name);
    }
    
    public function setElementOptions($options = null) {
        $this->elementOptions[] = $options;
    }
    
    /**
    * Create a Form
    *
    * @param array $data Form original data
    * @param string $action Form action url
    * @param string $method Form method POST | GET
    * @return self
    */
    public function create($method = 'post', $action = null) { 
        $this->setAttribute('method', $method)
             ->setAttribute('novalidate', 'novalidate');
        // set action attribute
        if ($action) {
            $this->setAttribute('action', $action);
        }
        $validators = array();
        $elements = $this->elements(); 
        if (!empty($elements)) {
            foreach ($elements as $element) {
                if (!empty($this->elementOptions)) {
                    foreach ($this->elementOptions as $elementOptions) {
                        foreach ($elementOptions as $name => $options) {
                            if ($element['name'] == $name && isset($element['options'])) {
                                $element['options'] = array_replace_recursive($element['options'], $options);
                            }
                        }
                    }
                }          
                foreach ($element as $elementKey => $elementValue) {
                    if (isset($element['attributes']['required'])) {
                        if ($element['attributes']['required'] == true) {
                            $element['required'] = true;
                            $element['allow_empty'] = false;
                        } else {
                            $element['required'] = false;
                            $element['allow_empty'] = true;
                        }                    
                    }
                }
                // see Zend\InputFilter\Factory
                if (!isset($element['validators'])) { 
                    foreach ($element as $elementKey => $elementValue) {
                        if (in_array($elementKey, array('required', 'allow_empty', 'continue_if_empty'))) {
                            if (!isset($validator['name'])) {
                                $validator['name'] = $element['name'];
                            }
                            $validator[$elementKey] = $elementValue;                                               
                        }
                    }                 
                    if (isset($element['attributes']['required'])) {
                        $validator['required'] = $element['attributes']['required'];
                        // auto allow_empty if required == false 
                        if ($validator['required'] === false) {
                            if (!isset($validator['name'])) {
                                $validator['name'] = $element['name'];
                            }                        
                            $validator['allow_empty'] = true;
                        }
                    }
                    if (!empty($validator)) {
                        $validators[] = $validator;
                    }
                } else {
                    foreach ($element['validators'] as &$v) {
                        if (isset($v['name']) && $v['name'] == 'StringLength') {
                            $v['options']['encoding'] = 'UTF-8';
                        }
                    }
                    unset($v);
                    $validator = array(                    
                        'name' => $element['name'],
                        'validators' => $element['validators']
                    );                
                    // find attribute required to set to validators
                    if (!empty($element['attributes'])) {
                        foreach ($element['attributes'] as $attribute => $attributeValue) {
                            if ($attribute == 'required') {
                                $validator['required'] = $attributeValue;
                                break;
                            }
                        }
                    }
                    // set filter when not isset no_filters
                    if (!isset($element['no_filters'])) {
                        $filters = $this->filters;
                        if (isset($element['filters'])) {
                            $validator['filters'] = array_merge($filters, $element['filters']);
                            unset($element['filters']);
                        }
                    } else {
                        unset($element['no_filters']);
                    }
                    if (!empty($validator['validators'])) {
                        $validators[] = $validator;                    
                    }
                    unset($element['validators']);                
                }
                if (isset($element['attributes']['required'])) {
                    //unset($element['attributes']['required']);
                }
                $this->add($element);
                $validator = array();
            }
            // add validators to inputFilter
            if (!empty($validators)) {
                $inputFilter = new InputFilter();
                foreach ($validators as $validator) {
                    // translate message
                    $search = array();
                    $replace = array();
                    if (!empty($validator['validators'])) {
                        foreach ($validator['validators'] as $i => $item) {
                            if (isset($item['options'])) {
                                foreach ($item['options'] as $key => $value) {
                                    if (is_scalar($value)) {
                                        $search[] = '{' . $key . '}';
                                        $replace[] = $value;
                                    }
                                }
                                if (isset($item['options']['messages'])) {
                                    foreach ($item['options']['messages'] as $keyMessage => $message) {
                                        $message = str_replace($search, $replace, $this->translate($message));
                                        $validator['validators'][$i]['options']['messages'][$keyMessage] = $message;
                                    }
                                }
                            }
                        }          
                    }           
                    $inputFilter->add($validator); 
                }
                $this->setInputFilter($inputFilter);
            }
        }
        return $this;
    }
    
    /**
     * Bind original data
     *
     * @param array $data Data get from DB or API
     * @return self
     */
    public function bindData($data) {
        $this->bind(new Model($data));
        return $this;
    }
    
    public function setData($data)
    {
        if ($_FILES) {
            $files = array();
            foreach ($_FILES as $name => $file) {
                if (!empty($file['name']) && empty($data['remove'][$name])) {
                   $files[$name] = $file;
                }
            }
            if (!empty($files)) {
                $data = array_merge_recursive( 
                    (array) $data, 
                    $files
                );
            }
        }
        return parent::setData($data);
    }
    
    /**
     * Set dataset
     *
     * @param array $dataSet Data get from DB or API
     * @return self
     */
    public function setDataset($dataset) {        
        $this->dataset = $dataset;
        return $this;
    }
    
    /**
     * Get dataset
     *
     * @return array DataSet
     */
    public function getDataset() {        
        return $this->dataset;
    }
    
    /**
     * Set Controller to form
     *
     * @param object $controller
     * @return self
     */
    public function setController($controller = null) {        
        $this->controller = $controller;
        $this->translator = $controller->getServiceLocator()->get('translator');
        $this->url = $controller->url();
        return $this;
    }
    
    /**
     * Get Controller
     *
     * @return object Controller
     */
    public function getController() {
        return $this->controller;
    }
    
    /**
     * Set Translator to form
     *
     * @param object $translator
     * @return self
     */
    public function setTranslator($translator = null) {
        $this->translator = $translator;
        return $this;
    }
    
    /**
     * Get Translator
     *
     * @return object Translator
     */
    public function getTranslator() {
        return $this->translator;
    }

     /**
     * Translate message
     * @param mixed Unlimit
     * @return string Message after translate
     */
    public function translate() {      
        $args = func_get_args();
        if (count($args) < 1) {
            return '';
        }
        $args[0] = $this->translator->translate($args[0]);
        return call_user_func_array('sprintf', $args);
    }
    
    /**
     * Define elements of form, Will be override by child class
     *
     * @author  thailh   
     * @return  void
     */
    public abstract function elements();  
    
    /**
     * Define content to show on tfoot 
     *
     * @author  thailh   
     * @return  string
     */
    public function tfoot() {
        return '';
    }    
    
    /**
     * Bind original data
     *
     * @param array $data Data get from DB or API
     * @return self
     */
    public function getEditLink($url) { 
        $html = "
            <a class=\"vi\" href=\"{$url}?locale=vi\">vi</a> 
            &nbsp;
            <a class=\"en\" href=\"{$url}?locale=en\">en</a> 
        ";
        return $html;    
    }
    
    public function getEditLinkText($url) { 
        $html = "
            &nbsp; &nbsp;&nbsp;<a class=\"\" href=\"{$url}\">Edit</a>    
        ";
        return $html;    
    }
    
    public function getRequest() { 
        if ($this->getController()) {
            $request = $this->getController()->getServiceLocator()->get('Request');
            return $request;  
        }
        return null;
    }
    
}
