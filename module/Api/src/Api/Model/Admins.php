<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;

class Admins extends AbstractModel {

    protected static $properties = array(
        'admin_id',
        '_id',
        'email',
        'password',
        'hash_password',
        'username',
        'name',
        'display_name',
        'birthday',
        'gender',
        'country_code',
        'state_code',
        'city_code',
        'street',
        'passport',
        'identify',
        'device',
        'active',
        'created',
        'updated',
        'last_login',
        'image_id',
        'website_id',
    );
    
    protected static $tableName = 'admins';

    public function duplicateEmail($param) {
        $sql = new Sql(static::$db);
        $select = $sql->select()
                ->from(static::$tableName)
                ->where(array(static::$tableName . '.email' => $param['email']));
        if (!empty($param['notId'])) {
            $select->where(new Expression(static::$tableName . ".admin_id <> '{$param['notId']}'"));
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        return static::count($selectString) > 0 ? 1 : 0;
    }
    
    public function add($param)
    {
        $_id = mongo_id();  // admins._id   
        $crypt = Util::cryptPassword($param['password']);  
        $values = array(
            '_id' =>  $_id,          
            'email' => $param['email'],
            'password' => $crypt['password'],
            'hash_password' => $crypt['salt'],            
            'name' => $param['name'],            
            'website_id' => $param['website_id'],            
        );
        if (isset($param['display_name'])) {
            $values['display_name'] = $param['display_name'];
        } 
        $validator = new \Zend\Validator\Date();
        if (isset($param['birthday']) && $validator->isValid($param['birthday'])) {
            $values['birthday'] = self::str2time($param['birthday']);
        }
        if (isset($param['gender'])) {
            $values['gender'] = $param['gender'];
        }
        if (isset($param['country_code'])) {
            $values['country_code'] = $param['country_code'];
        }
        if (isset($param['state_code'])) {
            $values['state_code'] = $param['state_code'];
        }
        if (isset($param['city_code'])) {
            $values['city_code'] = $param['city_code'];
        }
        if (isset($param['street'])) {
            $values['street'] = $param['street'];
        }        
        if (isset($param['identify'])) {
            $values['identify'] = $param['identify'];
        }
        if (isset($param['device'])) {
            $values['device'] = $param['device'];
        }
        if (isset($param['active'])) {
            $values['active'] = $param['active'];
        }
        if (isset($param['last_login'])) {
            $values['last_login'] = $param['last_login'];
        }
        if (self::insert($values)) {             
            return $_id;
        }        
        return false;
    }

    /*
    * @description register user
    * @param email
    * @param password
    * return array user.id
    */    
    public function register($param) {
        $user = self::spQuery(
            'admins_get_email', 
            self::spParameter(
                array(
                    'email' => null
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        ); 
        if (!empty($user)) {
            self::errorNotExist('email');
            return false;
        }
        $crypt = Util::cryptPassword($param['password']);
        $param['password'] = $crypt['password'];
        $param['hash_password'] = $crypt['salt'];
        $result = self::spQuery(
            'admins_register',
            self::spParameter(
                array(
                    'email' => null,
                    'first_name' => null,
                    'last_name' => null,
                    'password' => null,
                    'hash_password' => null,
                    'timezone' => Util::timezone(),
                    'perm_role_id' => 0,
                ),
                $param
            ),                
            self::RETURN_TYPE_ONE
        );
        return !empty($result['_id']) ? $result['_id'] : false;
    }

    /*
    * Login
    * @param email
    * @param password
    * @return array()
    */  
    public function login($param) {    
        $user = self::find(
            array(
                'where' => array(
                    'email' => $param['email'],
                    'active' => 1,
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($user)) {
            self::errorNotExist('email');
            return false;
        }
        $user['id'] = $user['admin_id'];
        if (Util::verifyPassword($param['password'], $user['password'])) {
            if (!empty($user['image_id'])) {
                $image = new Images();
                $imageDetail = $image->getDetail(array(
                    'id' => $user['image_id'],
                    'src' => 'admins'
                ));
                $user['url_image'] = !empty($imageDetail['url_image']) ? $imageDetail['url_image'] : ''; 
            }
            if (!self::update(array(
                'set' => array('last_login' => time()),
                'where' => array(
                    '_id' => $user['_id']
                ),
            ))) {
                return false;
            }  
            return $user;
        }
        self::errorParamInvalid('password');
        return false;
    }

    /*
    * @desction get List admins
    */
    public function getList($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'admin_id', 
                '_id', 
                'email', 
                'name',
                'display_name',
                'active',
                'website_id',
            ))
            ->join(
                'admin_images', 
                static::$tableName . '.image_id = admin_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );            
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (!empty($param['email'])) {
            $select->where(new Expression(static::$tableName .  ".email LIKE '%{$param['email']}%'"));
        }
        if (!empty($param['name'])) {
            $select->where(new Expression(static::$tableName .  ".name LIKE '%{$param['name']}%'"));
        }        
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(email|name|last_login)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
               $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);               
            }            
        } else {
            $select->order(static::$tableName . '.last_login DESC');
        }  
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    /*
    * @desction update info admins
    */
    public function updatePassword($param)
    {
        $self = self::find(
            array(            
                'where' => array('_id' => $param['_id']),
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('_id');
            return false;
        }  
        $crypt = Util::cryptPassword($param['password']);
        $set = array(
            'password' => $crypt['password'],
            'hash_password' => $crypt['salt'],            
        );              
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {            
            return true;
        }
        return false;
    } 
    
    /*
    * @desction update info admins
    */
    public function updateInfo($param)
    {
        $self = self::find(
            array(            
                'where' => array('_id' => $param['_id'])
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('_id');
            return false;
        }        
        $set = array();  
        if (isset($param['name'])) {
            $set['name'] = $param['name'];
        } 
        if (isset($param['display_name'])) {
            $set['display_name'] = $param['display_name'];
        }
        $validator = new \Zend\Validator\Date();
        if (isset($param['birthday']) && $validator->isValid($param['birthday'])) {
            $set['birthday'] = self::str2time($param['birthday']);
        }
        if (isset($param['gender'])) {
            $set['gender'] = $param['gender'];
        }
        if (isset($param['country_code'])) {
            $set['country_code'] = $param['country_code'];
        }
        if (isset($param['state_code'])) {
            $set['state_code'] = $param['state_code'];
        }
        if (isset($param['city_code'])) {
            $set['city_code'] = $param['city_code'];
        }
        if (isset($param['street'])) {
            $set['street'] = $param['street'];
        }        
        if (isset($param['identify'])) {
            $set['identify'] = $param['identify'];
        }
        if (isset($param['passport'])) {
            $set['passport'] = $param['passport'];
        }
        if (isset($param['device'])) {
            $set['device'] = $param['device'];
        }
        if (isset($param['active'])) {
            $set['active'] = $param['active'];
        }
        if (isset($param['last_login'])) {
            $set['last_login'] = $param['last_login'];
        }   
        if (isset($param['website_id'])) {
            $set['website_id'] = $param['website_id'];
        }
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'admins',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'admins',
                        'src_id' => $self['admin_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'admins'
                ));
            }
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }  
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {            
            return true;
        }
        return false;
    }

    public function getDetail($param)
    {
        $self = self::find(
            array(
                'where' => array(
                    '_id' => $param['_id'],
                ),
            ),
            self::RETURN_TYPE_ONE
        );
        return $self;
    }
    
}
