<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;

class UserActivations extends AbstractModel {

    protected static $properties = array(
        'id',
        'user_id',
        'admin_id',
        'email',
        'password',
        'token',
        'expired',
        'active',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'user_activations';
    
    public function add($param, &$token = '')
    {
        $self = self::getDetail(array(
            'email' => $param['email'],
            'active' => 1
        ));
        if (!empty($self)) {
            self::errorOther(self::ERROR_CODE_OTHER_1, 'email', 'Email for change new password that sent to your inbox before, please check your inbox');
            return false;
        }
        $token = generate_token();
        $userModel = new Users;
        $user = $userModel->getDetail($param);
        if (!empty($user)) {
            $values = array(
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'token' => $token,
                'expired' => strtotime('+ 1 week'),
            );
            if ($id = self::insert($values)) {
                return array(
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'token' => $token,
                    'id' => $id,
                );
            }
        } else {
            self::errorNotExist('email');
        }
        return false;
    }
    
    public function getDetail($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName);                
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. self::quote($param['active']));  
        }
        if (isset($param['email']) && $param['email'] !== '') {            
            $select->where(static::$tableName . '.email = '. self::quote($param['email']));  
        }
        if (isset($param['token']) && $param['token'] !== '') {            
            $select->where(static::$tableName . '.token = '. self::quote($param['token']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        return $result;
    }
}
