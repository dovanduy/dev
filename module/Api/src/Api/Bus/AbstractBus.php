<?php

namespace Api\Bus;

use Exception;
use Zend\Http\Response;
use Application\Lib\Log;

/**
 * AbstractBus
 *
 * @package 	Bus
 * @created 	2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
abstract class AbstractBus {

    /** @var int STATUS_OK */
    const STATUS_OK = 200;

    /** @var int ERROR_CODE_INVALID_JSON */
    const ERROR_CODE_INVALID_JSON = 100;

    /** @var int ERROR_VALIDATION */
    const ERROR_VALIDATION = 400;

    /** @var int ERROR_SYSTEM */
    const ERROR_SYSTEM = 500;

    /** @var int ERROR_CODE_AUTH_ERROR */
    const ERROR_CODE_AUTH_ERROR = 401;

    /** @var int ERROR_CODE_LOGIN_ERROR */
    const ERROR_CODE_LOGIN_ERROR = 403;

    /** @var int ERROR_CODE_FIELD_REQUIRED */
    const ERROR_CODE_FIELD_REQUIRED = 1000;

    /** @var int ERROR_CODE_FIELD_LENGTH_MIN */
    const ERROR_CODE_FIELD_LENGTH_MIN = 1001;

    /** @var int ERROR_CODE_FIELD_LENGTH_MAX */
    const ERROR_CODE_FIELD_LENGTH_MAX = 1002;

    /** @var int ERROR_CODE_FIELD_LENGTH_EXACT */
    const ERROR_CODE_FIELD_LENGTH_EXACT = 1003;

    /** @var int ERROR_CODE_FIELD_FORMAT_DATE */
    const ERROR_CODE_FIELD_FORMAT_DATE = 1004;

    /** @var int ERROR_CODE_FIELD_FORMAT_EMAIL */
    const ERROR_CODE_FIELD_FORMAT_EMAIL = 1005;

    /** @var int ERROR_CODE_FIELD_FORMAT_URL */
    const ERROR_CODE_FIELD_FORMAT_URL = 1006;

    /** @var int ERROR_CODE_FIELD_NUMERIC_MIN */
    const ERROR_CODE_FIELD_NUMERIC_MIN = 1007;

    /** @var int ERROR_CODE_FIELD_NUMERIC_MAX */
    const ERROR_CODE_FIELD_NUMERIC_MAX = 1008;

    /** @var int ERROR_CODE_FIELD_NUMERIC_BETWEEN */
    const ERROR_CODE_FIELD_NUMERIC_BETWEEN = 1009;

    /** @var int ERROR_CODE_FIELD_NOT_EXIST */
    const ERROR_CODE_FIELD_NOT_EXIST = 1010;

    /** @var int ERROR_CODE_FIELD_DUPLICATE */
    const ERROR_CODE_FIELD_DUPLICATE = 1011;

    /** @var int ERROR_CODE_FIELD_FORMAT_NUMBER */
    const ERROR_CODE_FIELD_FORMAT_NUMBER = 1012;

    /** @var int ERROR_CODE_DENIED_ERROR */
    const ERROR_CODE_DENIED_ERROR = 1100;

    /** @var int ERROR_CODE_FIELD_FORMAT_KATAKANA */
    const ERROR_CODE_FIELD_FORMAT_KATAKANA = 1200;

    /** @var int ERROR_CODE_FIELD_FORMAT_PASSWORD */
    const ERROR_CODE_FIELD_FORMAT_PASSWORD = 1201;

    /** @var int ERROR_CODE_DENIED_ERROR */
    const ERROR_CODE_ONLY_N_PARAMETER = 1202;

    /** @var array Array of error code */
    protected $_error_code = array();

    /** @var array Array of error message */
    protected $_error_message = array(
        self::ERROR_CODE_INVALID_JSON => 'Invalid json format',
        self::ERROR_VALIDATION => 'Invalid parameter',
        self::ERROR_SYSTEM => 'Db exception',
        self::ERROR_CODE_AUTH_ERROR => 'Access token is invalid',
        self::ERROR_CODE_LOGIN_ERROR => 'Invalid email or password',
    );

    /** @var array Array of validation's error code */
    protected $_error_code_validation = array();

    /** @var array Array of validation's error message */
    protected $_error_message_validation = array(
        self::ERROR_VALIDATION => 'Invalid parameters',
        self::ERROR_CODE_AUTH_ERROR => 'Access token is invalid',
        self::ERROR_CODE_LOGIN_ERROR => 'Invalid email or password',
        self::ERROR_SYSTEM => 'Db exception',
        self::ERROR_CODE_FIELD_REQUIRED => 'The %s is required and must contain a value',
        self::ERROR_CODE_FIELD_LENGTH_MIN => 'The %s has to contain at least %s characters',
        self::ERROR_CODE_FIELD_LENGTH_MAX => 'The %s may not contain more than %s characters',
        self::ERROR_CODE_FIELD_LENGTH_EXACT => 'The field %s must contain exactly %s characters',
        self::ERROR_CODE_FIELD_FORMAT_DATE => 'The %s must contain a valid formatted date',
        self::ERROR_CODE_FIELD_FORMAT_EMAIL => 'The %s must contain a valid email address',
        self::ERROR_CODE_FIELD_FORMAT_URL => 'The %s must contain a valid URL',
        self::ERROR_CODE_FIELD_FORMAT_NUMBER => 'The %s must contain a valid number',
        self::ERROR_CODE_FIELD_NUMERIC_MIN => 'The minimum numeric value of :label must be %s',
        self::ERROR_CODE_FIELD_NUMERIC_MAX => 'The maximum numeric value of %s must be %s',
        self::ERROR_CODE_FIELD_NUMERIC_BETWEEN => 'The %s may not contain more than %s characters',
        self::ERROR_CODE_FIELD_NOT_EXIST => 'The %s does not exist',
        self::ERROR_CODE_FIELD_DUPLICATE => 'The %s is duplicate data',
        self::ERROR_CODE_DENIED_ERROR => 'The action have been denied by system',
        self::ERROR_CODE_FIELD_FORMAT_KATAKANA => 'The %s must be a katakana string',
        self::ERROR_CODE_FIELD_FORMAT_PASSWORD => 'The %s must contain only alphabet or numeric',
        self::ERROR_CODE_ONLY_N_PARAMETER => 'Input only %s parameters',
    );

    /** @var array Array of output format */
    protected $_formats = array('json', 'php', 'html', 'xml', 'serialize');

    /** @var string Input format method */
    protected $_input_format = 'post';

    /** @var string Output format */
    protected $_output_format = 'json';

    /** @var mixed Success status */
    protected $_success = null;

    /** @var string Invalid parameter */
    protected $_invalid_parameter;

    /** @var mixed Exception */
    protected $_exception = null;

    /** @var array Array default value */
    protected $_default_value = array();

    /** @var array Array of required parameters */
    protected $_required = array();

    /** @var array Array of parameter's length */
    protected $_length = array();

    /** @var array Array of parameter's url format */
    protected $_url_format = array();

    /** @var array Array of parameter's email format */
    protected $_email_format = array();

    /** @var array Array of parameter's date format */
    protected $_date_format = array();

    /** @var array Array of parameter's number format */
    protected $_number_format = array();

    /** @var array Array of parameter's kana format */
    protected $_kana_format = array();

    /** @var array Array of parameter's japanese format */
    protected $_japanese_format = array();

    /** @var array Array of response */
    protected $_response = array();

    /** @var bool Check if having parameter or not */
    protected $_has_parameter = true;

    /** @var Object Instance of BusAbstract */
    protected static $_instance = null;

    /**
     * Get instance of bus object
     *
     * @author thailh
     * @return object Instance of bus object
     */
    public final static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * Add error array
     *
     * @return void
     * @author thailh
     */
    protected function _addErrors($error = array()) {
        if (empty($error))
            return false;
        foreach ($error as $err) {
            $this->_addError($err['code'], $err['field'], $err['value']);
        }
    }

    /**
     * Add a error
     *
     * @return void
     * @author thailh
     */
    protected function _addError($code, $field, $value = '') {
        if (isset($this->_error_code_validation[$field])) {
            return true;
        }
        if (isset($this->_error_message_validation[$code])) {
            $message = sprintf($this->_error_message_validation[$code], $field, $value);
        } else {
            $message = $value;
        }
        $this->_error_code_validation[] = array(
            'field' => $field,
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * Get validation error
     *
     * @return array Array of validation's error code
     * @author thailh
     */
    protected function _getError() {
        return $this->_error_code_validation;
    }

    /**
     * Get default value setting
     *
     * @return array Array of default value
     * @author thailh
     */
    public function getDefaultValue() {
        return $this->_default_value;
    }

    /**
     * Implements function get required files setting
     *
     * @author thailh
     * @returns array Array of required parameter
     */
    public function getRequired() {
        return $this->_required;
    }

    /**
     * Implements function get length of fields setting
     *
     * @author thailh
     * @returns array Array of parameter's length
     */
    public function getLength() {
        return $this->_length;
    }

    /**
     * Implements function setDefaultValue if empty
     *
     * @param  array $param
     * @return array Array with default value
     * @author thailh
     */
    public function setDefaultValue($param) {
        $defaultValue = $this->getDefaultValue();
        if (empty($defaultValue)) {
            return $param;
        }
        foreach ($defaultValue as $field => $value) {
            if (empty($param[$field])) {
                $param[$field] = $value;
            }
        }
        foreach ($param as $field => $value) {
            if (empty($param[$field]) && isset($defaultValue[$field])) {
                $param[$field] = $defaultValue[$field];
            }
        }
        return $param;
    }

    /**
     * Check required parameters
     *
     * @author  thailh
     * @param   array $param Input data    
     * @return boolean True if data is valid required, false if invalid
     */
    public function checkRequired($param) { 
        $ok = true;
        foreach ($this->_required as $key => $field) { 
            if (!isset($param[$field])) { 
                $this->_addError(self::ERROR_CODE_FIELD_REQUIRED, $field);
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Check parameter's length
     *
     * @author  thailh
     * @param   array $param Input data to check
     * @param   array $length Length config for check length
     * @return  bool True if all field are valid, false if have one of fields invalid
     */
    public function checkLength($param, $lengthOfField = null) {
        $ok = true;
        foreach ($this->_length as $field => $length) {                          
            if (!is_array($length)) {
                $length = array($length, $length);
            }
            if (!isset($param[$field]) || count($length) !== 2) {
                continue;
            }
            if (mb_strlen($param[$field]) < intval($length[0])) {
                $this->_addError(self::ERROR_CODE_FIELD_LENGTH_MIN, $field, $length[0]);
                $ok = false;
            } elseif (mb_strlen($param[$field]) > intval($length[1])) {
                $this->_addError(self::ERROR_CODE_FIELD_LENGTH_MAX, $field, $length[1]);
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Set error
     *
     * @param array $errorCode Array of error code
     * @return string Response format of api
     * @author thailh
     */
    private function _error($errorCode) {
        return $this->getResponse($errorCode);
    }

    /**
     * Do all check, and then call operateDB if data is good
     *
     * @param array $json Input format
     * @param array $moreParam More parameter if having
     * @return string Response of api
     * @author thailh
     */
    public final function execute($model, $param = array()) {
        
        $this->_output_format = 'json';
        
        $param = $this->setDefaultValue($param);

        // check required
        $checkRequired = $this->checkRequired($param);
        if (!$checkRequired) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check length
        $checkLength = $this->checkLength($param);
        if (!$checkLength) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check url
        $checkUrl = $this->checkUrlFormat($param);
        if (!$checkUrl) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check email
        $checkEmail = $this->checkEmailFormat($param);
        if (!$checkEmail) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check date
        $checkDate = $this->checkDateFormat($param);
        if (!$checkDate) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check number
        $checkNumber = $this->checkNumberFormat($param);
        if (!$checkNumber) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }

        // check data format
        $checkFormat = $this->checkDataFormat($param);
        if (!$checkFormat) {
            return $this->getResponse(self::ERROR_VALIDATION);
        }
        Log::info('Request: ' . $_SERVER['REQUEST_URI']);
        $operateDB = $this->operateDB($model, $param);
        if ($operateDB === false) {
            if (isset($this->_exception) && $this->_exception != null) {
                Log::error(sprintf("Exception\n"
                            . " - Message : %s\n"
                            . " - Code : %s\n"
                            . " - File : %s\n"
                            . " - Line : %d\n"
                            . " - Stack trace : \n"
                            . "%s",
                            $this->_exception->getMessage(),
                            $this->_exception->getCode(),
                            $this->_exception->getFile(),
                            $this->_exception->getLine(),
                            $this->_exception->getTraceAsString()),
                        $param);
            }
            if (self::_getError()) {
                return $this->getResponse(self::ERROR_VALIDATION);
            } else {
                return $this->getResponse(self::ERROR_SYSTEM);
            }
        }
        return $this->getResponse(self::STATUS_OK);
    }

    /**
     * Return format as XML
     *
     * @author  thailh
     * @return  string Xml format
     */
    public function getXMLResponse() {
        //return \Lib\Format::forge($this->_response)->to_xml();
    }

    /**
     * Return format as PHP variable
     *
     * @author  thailh
     * @return  string PHP format
     */
    public function getPhpResponse() {
        //return \Lib\Format::forge($this->_response)->to_php();
    }

    /**
     * Return format as HTML variable
     *
     * @author  thailh
     * @return  string Html format
     */
    public function getHtmlResponse() {
        //return \Lib\Format::forge($this->_response)->to_html();
    }

    /**
     * Return format as serialize variable
     *
     * @author  thailh
     * @return  string Serialized format
     */
    public function getSerializeResponse() {
        //return \Lib\Format::forge($this->_response)->to_serialized();
    }

    /**
     * Return format as json variable
     *
     * @author  thailh
     * @return  string Json format
     */
    public function getJsonResponse() {
        return \Zend\Json\Encoder::encode($this->_response);        
    }

    /**
     * Get response of api
     *
     * @author  thailh
     * @return  String Response of api
     */
    public function getResponse($httpStatus = null) {
        $response = new Response();         
        switch ($httpStatus) {
            case self::ERROR_SYSTEM:
                $response->setStatusCode(self::STATUS_OK);
                if ($this->_exception !== null) {
                    $message = $this->_exception->getMessage();
                } else {
                    $message = 'System error';
                }
                $this->_response = array(
                    'status' => 'ERROR',
                    'results' => $message
                );
                Log::error('ERROR', $message);
                break;
            case self::STATUS_OK:
                $response->setStatusCode(self::STATUS_OK);
                $this->_response = array(
                    'status' => 'OK',
                    'results' => $this->_response
                );
                break;
            case self::ERROR_VALIDATION:
                $response->setStatusCode(self::STATUS_OK);  
                $this->_response = array(
                    'status' => 'ERROR_VALIDATION',
                    'results' => $this->_getError()
                );
                Log::warning('ERROR_VALIDATION', $this->_getError());
                break;            
        }        
        switch ($this->_output_format) {
            case 'xml':
                $response->getHeaders()->addHeaders(array('Content-Type', 'text/xml'));
                $function = 'getXmlResponse';
                break;
            case 'php':
                $response->getHeaders()->addHeaders(array('Content-Type', 'text/plain'));
                $function = 'getPhpResponse';
                break;
            case 'html':
                $response->getHeaders()->addHeaders(array('Content-Type' => 'text/html'));
                $function = 'getHtmlResponse';
                break;
            case 'serialize':
                $response->getHeaders()->addHeaders(array('Content-Type' => 'text/html'));
                $function = 'getSerializeResponse';
                break;
            default:
                $response->getHeaders()->addHeaders(array('Content-Type' => 'application/json'));
                $function = 'getJsonResponse';
        }
        $body = call_user_func(array($this, $function));
        $response->setContent($body);
        return $response;
    }

    /**
     * Get response result
     *
     * @author  thailh
     * @param   $error Array of errors
     * @return  bool True if successful, otherwise false
     */
    public function result($error = array()) {
        if (!empty($error)) {
            $this->_addErrors($error);
            return false;
        }
        if ($this->_response !== false) {
            return true;
        }
        return false;
    }

    /**
     * Check data format, will be override at child class if need
     *
     * @author  thailh
     * @param   $param Input data
     * @return  bool True if successful, otherwise false
     */
    public function checkDataFormat($param) {
        return true;
    }

    /**
     * Check url format, will be override at child class if need
     *
     * @author thailh
     * @param $param Input data
     * @return bool True if successful, otherwise false
     */
    public function checkUrlFormat($param) {
        foreach ($this->_url_format as $field) {
            if (!empty($param[$field]) && !filter_var($param[$field], FILTER_VALIDATE_URL)) {
                $this->_addError(self::ERROR_CODE_FIELD_FORMAT_URL, $field, $param[$field]);
                $this->_invalid_parameter = $field;
                return false;
            }
        }
        return true;
    }

    /**
     * Check email format, will be override at child class if need
     *
     * @author thailh
     * @param $param Input data
     * @return bool True if successful, otherwise false
     */
    public function checkEmailFormat($param) {
        foreach ($this->_email_format as $field) {
            if (!empty($param[$field])) {
                $pattern = "/^[A-Za-z0-9._%+-]+@([A-Za-z0-9-]+\.)+([A-Za-z0-9]{2,4}|museum)$/";
                if (!preg_match($pattern, $param[$field])) {
                    $this->_addError(self::ERROR_CODE_FIELD_FORMAT_EMAIL, $field, $param[$field]);
                    $this->_invalid_parameter = $field;
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check date format, will be override at child class if need
     *
     * @author thailh
     * @param $param Input data
     * @return bool True if successful, otherwise false
     */
    public function checkDateFormat($param) {
        foreach ($this->_date_format as $field => $value) {
            if (!empty($param[$field])) {
                $dt = \DateTime::createFromFormat($value, $param[$field]);
                if (!($dt !== false && !array_sum($dt->getLastErrors()))) {
                    $this->_addError(self::ERROR_CODE_FIELD_FORMAT_DATE, $field, $param[$field]);
                    $this->_invalid_parameter = $field;
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check number format, will be override at child class if need
     *
     * @author thailh
     * @param $param Input data
     * @return bool True if successful, otherwise false
     */
    public function checkNumberFormat($param) {
        foreach ($this->_number_format as $field) {
            if (!empty($param[$field]) && !is_numeric($param[$field])) {
                $this->_addError(self::ERROR_CODE_FIELD_FORMAT_NUMBER, $field, $param[$field]);
                $this->_invalid_parameter = $field;
                return false;
            }
        }
        return true;
    }

    /**
     * Will be override by child class
     *
     * @author  thailh
     * @param   $param Input data
     * @return  void
     */
    public abstract function operateDB($model, $param);
}
