<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Validator;

use Zend\Validator\Exception;
use Zend\Validator\AbstractValidator;

/**
 * Check duplicate email in database
 */
class OldPassword extends AbstractValidator
{
    /**
     * Error constants
     */
     const NOT_SAME = 'notSame';
     const NOT_LOGIN = 'notLogin';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = [
        self::NOT_SAME => "Old password is invalid",
        self::NOT_LOGIN => "User not login"
    ];
   
    
    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }    
    
    /**
     * Returns true if and only if $value is no match
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $result = true;
        $auth = new \Application\Lib\Auth();
        if ($auth->hasIdentity()) {    
            $AppUI = $auth->getIdentity();
            $ok = \Web\Lib\Api::call('url_users_checklogin', array(
                'email' => $AppUI->email,
                'password' => $value
            ));
            if (empty($ok)) {
                $this->error(self::NOT_SAME);
                $result = false;
            }
        } else {
            $this->error(self::NOT_LOGIN);
            $result = false;
        }
        return $result;
    }
    
}
