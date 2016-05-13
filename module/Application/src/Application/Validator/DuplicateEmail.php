<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Validator;

use Application\Lib\Api;
use Zend\Validator\Exception;
use Zend\Validator\AbstractValidator;

/**
 * Check duplicate email in database
 */
class DuplicateEmail extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_DUPLICATE_EMAIL = 'duplicateEmail';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::ERROR_DUPLICATE_EMAIL => "Duplicate email in database"
    ];
    
    protected $notUserId = 0;
    
    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {       
        if (array_key_exists('notUserId', $options)) {
            $this->setNotUserId($options['notUserId']);
        }
        parent::__construct($options);
    }
    
    /**
     * Returns the notUserId option
     *
     * @return int
     */
    public function getNotUserId()
    {
        return $this->notUserId;
    }

    /**
     * Sets the notUserId option
     *
     * @param  int $userId
     * @return self 
     */
    public function setNotUserId($userId)
    {
        $this->notUserId = $userId;
        return $this;
    }
    
    /**
     * Returns true if and only if $value is no duplicate
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $result = Api::call('url_validator_duplicateemail', array(
            'email' => $value,
            'notUserId' => $this->notUserId,
        ));
        if ($result) {
            $this->error(self::ERROR_DUPLICATE_EMAIL);
            return false;
        }
        return true;
    }
    
}
