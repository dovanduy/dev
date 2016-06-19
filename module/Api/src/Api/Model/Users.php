<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;
use Application\Lib\Log;

class Users extends AbstractModel {

    protected static $properties = array(
        'user_id',
        '_id',
        'email',
        'password',
        'hash_password',
        'username',
        'name',
        'display_name',
        'birthday',
        'gender',        
        'passport',
        'identify',
        'device',
        'active',
        'created',
        'updated',       
        'image_id',
        'address_id',
        'website_id',        
        'phone',        
        'mobile',        
    );
    
    protected static $tableName = 'users';

    public function duplicateEmail($param) {
        $sql = new Sql(static::getDb());
        $select = $sql->select()
                ->from(static::$tableName)
                ->where(array(static::$tableName . '.email' => $param['email']));
        if (!empty($param['notId'])) {
            $select->where(new Expression(static::$tableName . ".user_id <> '{$param['notId']}'"));
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = static::getDb()->query($selectString, Adapter::QUERY_MODE_EXECUTE); 
        if ($result->count() > 0) {
            return 1;            
        }
        return 0;
    }
    
    public function add($param, &$id = 0)
    {
        if (self::duplicateEmail($param)) {
            self::errorDuplicate('email');
            return false;
        }  
        $param['name'] = !empty($param['name']) ? $param['name'] : '';
        $_id = mongo_id();  // users._id 
        $values = array(
            '_id' =>  $_id,          
            'name' => $param['name'],
            'email' => $param['email'],
            'website_id' => $param['website_id'],
        );        
        if (!empty($param['password'])) {
            $crypt = Util::cryptPassword($param['password']);  
            $values['password'] = $crypt['password'];
            $values['hash_password'] = $crypt['salt'];
        }
        if (isset($param['display_name'])) {
            $values['display_name'] = $param['display_name'];
        }
        if (empty($param['display_name'])) {
            $values['display_name'] = $param['name'];
        }
        $validator = new \Zend\Validator\Date();
        if (isset($param['birthday']) && $validator->isValid($param['birthday'])) {
            $values['birthday'] = self::str2time($param['birthday']);
        }
        if (isset($param['gender'])) {
            $values['gender'] = $param['gender'];
        } 
        if (isset($param['phone'])) {
            $values['phone'] = $param['phone'];
        }
        if (isset($param['mobile'])) {
            $values['mobile'] = $param['mobile'];
        }
        if (isset($param['address_id'])) {
            $values['address_id'] = $param['address_id'];
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
        if ($id = self::insert($values)) {              
            return $_id;
        }        
        return false;
    }
    
    /*
    * Check Login
    * @param email
    * @param password
    * @return array()
    */  
    public function checkLogin($param) {    
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
        if (Util::verifyPassword($param['password'], $user['password'])) {            
            return true;
        }
        self::errorParamInvalid('password');
        return false;
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
        $user['id'] = $user['user_id'];
        if (Util::verifyPassword($param['password'], $user['password'])) {
            $websiteHasUserModel = new WebsiteHasUsers;
            $websiteHasUserModel->addUpdate(array(
                'website_id' => $param['website_id'],
                'user_id' => $user['user_id'],
            ));            
            $addressModel = new Addresses;
            $addressList = $addressModel->getAll(array(
                    'user_id' => $user['user_id'],
                    'active' => 1,
                )
            );
            if (!empty($addressList[0])) {
                $user['address'] = $addressList[0];
            }
            if (!empty($user['image_id'])) {
                $image = new Images();
                $imageDetail = $image->getDetail(array(
                    'id' => $user['image_id'],
                    'src' => 'users'
                ));
                $user['url_image'] = !empty($imageDetail['url_image']) ? $imageDetail['url_image'] : ''; 
            }           
            return $user;
        }
        self::errorParamInvalid('password');
        return false;
    }

    /*
    * Login
    * @param email
    * @param password
    * @return array()
    */  
    public function getLogin($param) {    
        $user = self::find(
            array(
                'where' => array(
                    '_id' => $param['_id'],
                    'active' => 1,
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($user)) {
            self::errorNotExist('_id');
            return false;
        }
        $user['id'] = $user['user_id'];       
        if (!empty($user['image_id'])) {
            $image = new Images();
            $imageDetail = $image->getDetail(array(
                'id' => $user['image_id'],
                'src' => 'users'
            ));            
            $user['url_image'] = !empty($imageDetail['url_image']) ? $imageDetail['url_image'] : ''; 
        }
        $addressModel = new Addresses;
        $addressList = $addressModel->getAll(array(
                'user_id' => $user['user_id'],
                'active' => 1,
            )
        );
        if (!empty($addressList[0])) {
            $user['address'] = $addressList[0];
        }
        return $user;
    }
    
    /*
    * @desction get List users
    */
    public function getList($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'user_id', 
                '_id', 
                'email', 
                'name',
                'display_name',
                'active',                
                'phone',        
                'mobile',   
                'created', 
                'updated', 
            ))
            ->join(
                'user_images', 
                static::$tableName . '.image_id = user_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'addresses',                    
                static::$tableName . '.address_id = addresses.address_id',
                array(
                    'address_name' => 'name',
                    'street'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_countries',                    
                'addresses.country_code = location_countries.iso_a2',
                array(
                    'country_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_states',                    
                'addresses.state_code = location_states.iso',
                array(
                    'state_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_cities',                    
                'addresses.city_code = location_cities.code',
                array(
                    'city_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'website_has_users',                    
                static::$tableName . '.user_id = website_has_users.user_id',
                array(
                    'last_login'
                )
            )
            ->where('website_has_users.website_id = '. $param['website_id']); 
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }        
        if (!empty($param['email'])) {
            $select->where(new Expression(static::$tableName .  ".email LIKE '%{$param['email']}%'"));
        }
        if (!empty($param['name'])) {
            $select->where(new Expression(static::$tableName .  ".name LIKE '%{$param['name']}%'"));
        }        
        if (!empty($param['mobile'])) {
            $select->where(new Expression(static::$tableName .  ".mobile LIKE '%{$param['mobile']}%'"));
        }    
        if (!empty($param['phone'])) {
            $select->where(new Expression(static::$tableName .  ".phone LIKE '%{$param['phone']}%'"));
        }    
        if (!empty($param['country_code'])) {            
            $select->where('addresses.country_code = '. self::quote($param['country_code']));  
        } 
        if (!empty($param['state_code'])) {            
            $select->where('addresses.state_code = '. self::quote($param['state_code']));  
        } 
        if (!empty($param['city_code'])) {            
            $select->where('addresses.city_code = '. self::quote($param['city_code']));  
        }
        if (!empty($param['street'])) {          
            $select->where(new Expression("addresses.street LIKE '%{$param['street']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(email|name|mobile|display_name|state_name|city_name|street|updated|last_login)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {  
                    case 'state_name':
                        $select->order('location_states.name ' . $match[2]);
                        break;
                    case 'city_name':
                        $select->order('location_cities.name ' . $match[2]);
                        break;
                    case 'street':
                        $select->order('addresses.name ' . $match[2]);  
                        break;
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                } 
            }            
        } else {
            $select->order(static::$tableName . '.last_login DESC');
        }  
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));
        foreach ($data as &$row) {            
            $address = array(); 
            if (!empty($row['street'])) {
                $address[] = $row['street']; 
            }
            if (!empty($row['city_name'])) {
                $address[] = $row['city_name']; 
            }
            if (!empty($row['state_name'])) {
                $address[] = $row['state_name']; 
            }
            if (!empty($row['country_name'])) {
                $address[] = $row['country_name']; 
            }
            if (!empty($row)) {
                $row['address'] = implode(', ', $address);
            }
        }
        unset($row);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => $data, 
        );
    }
    
    /*
    * @desction get List users
    */
    public function getAll($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'user_id', 
                '_id', 
                'email', 
                'name',
                'display_name',
                'active',
                'website_id',
                'phone',        
                'mobile',   
            ))
            ->join(
                'user_images', 
                static::$tableName . '.image_id = user_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );            
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. static::quote($param['active']));  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. static::quote($param['website_id']));  
        }
        if (!empty($param['user_id'])) {            
            $select->where(static::$tableName . '.user_id = '. static::quote($param['user_id']));  
        }
        if (!empty($param['email'])) {
            $select->where(new Expression(static::$tableName .  ".email LIKE '%{$param['email']}%'"));
        }
        if (!empty($param['name'])) {
            $select->where(new Expression(static::$tableName .  ".name LIKE '%{$param['name']}%'"));
        }      
        if (!empty($param['mobile'])) {
            $select->where(new Expression(static::$tableName .  ".mobile LIKE '%{$param['mobile']}%'"));
        } 
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            $select->offset(0);
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
        return static::toArray(static::selectQuery($selectString));
    }
    
    /*
    * @desction get List users
    */
    public function search($param)
    {        
        if (empty($param['limit'])) {
            $param['limit'] = 10;
        }
        if (empty($param['keyword'])) {
            $param['keyword'] = '';
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'id' => 'user_id',              
                'text' => 'name'                
            ))
            ->where(static::$tableName . '.active = 1'); 
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (!empty($param['keyword'])) {
            $select->where(new Expression("
                users.email LIKE '%{$param['keyword']}%'
                OR
                users.name LIKE '%{$param['keyword']}%'
                OR
                users.display_name LIKE '%{$param['keyword']}%'
            "));
        }               
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            $select->offset(0);
        }        
        $select->order(static::$tableName . '.name ASC');         
        $selectString = $sql->getSqlStringForSqlObject($select);
        return static::toArray(static::selectQuery($selectString));
    }
    
    /*
    * @desction update info users
    */
    public function updatePassword($param)
    {
        $where = array();
        if (isset($param['email'])) {
            $where['email'] = $param['email'];
        }
        if (isset($param['_id'])) {
            $where['_id'] = $param['_id'];
        }
        $self = self::find(
            array(            
                'where' => $where,
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($self)) {
            self::errorNotExist('email_or_id');
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
                'where' => $where,
            )
        )) {            
            return true;
        }
        return false;
    } 
    
    public function updateNewPassword($param)
    {
        $activationModel = new UserActivations();
        $activation = $activationModel->getDetail(array(
            'token' => $param['token'],
            'active' => 1
        ));
        if (!empty($activation)) {
            $ok = self::updatePassword(array(
                'email' => $activation['email'],
                'password' => $param['password'],
            ));
            if ($ok) {
                $ok = $activationModel->update(array(
                    'set' => array('active' => 0),
                    'where' => array('id' => $activation['id']),
                ));                
            }
            return $ok;
        } else {
            self::errorOther(self::ERROR_CODE_FIELD_NOT_EXIST, 'token');
        }       
        return false;
    } 
    
    /*
    * @desction update info users
    */
    public function updateInfo($param)
    {
        if (empty($param['_id']) && empty($param['user_id'])) {
            self::errorParamInvalid('user_id_or_id');
            return false;
        }
        $where = array();
        if (!empty($param['_id'])) {
            $where['_id'] = $param['_id'];
        }
        if (!empty($param['user_id'])) {
            $where['user_id'] = $param['user_id'];
        }
        $self = self::find(
            array(            
                'where' => $where
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
        if (isset($param['address_id'])) {
            $set['address_id'] = $param['address_id'];
        }              
        if (isset($param['identify'])) {
            $set['identify'] = $param['identify'];
        }
        if (isset($param['passport'])) {
            $set['passport'] = $param['passport'];
        }
        if (isset($param['phone'])) {
            $set['phone'] = $param['phone'];
        }
        if (isset($param['mobile'])) {
            $set['mobile'] = $param['mobile'];
        }
        if (isset($param['device'])) {
            $set['device'] = $param['device'];
        }
        if (isset($param['active'])) {
            $set['active'] = $param['active'];
        }           
     
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'users',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'users',
                        'src_id' => $self['user_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'users'
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
                    'user_id' => $self['user_id']
                ),
            )
        )) {  
            if (isset($param['get_login'])) {
                return $this->getLogin($param);
            }
            return true;
        }
        return false;
    }

    public function getDetail($param)
    {
        $where = array();
        if (!empty($param['_id'])) {
            $where['_id'] = $param['_id'];
        }
        if (!empty($param['user_id'])) {
            $where['user_id'] = $param['user_id'];           
        }
        if (!empty($param['email'])) {
            $where['email'] = $param['email'];           
        }
        if (isset($param['active']) && $param['active'] !== '') {
            $where['active'] = $param['active'];           
        }
        if (empty($where)) {
            self::errorParamInvalid('user_id');
            return false;
        }
        $self = self::find(
            array(
                'where' => $where,
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($self)) {
            $addresses = new Addresses;
            $self['addresses'] = $addresses->getAll(array(
                    'user_id' => $self['user_id']
                )
            );
            if (!empty($param['get_product_orders'])) {
                $productOrders = new ProductOrders;
                $self['product_orders'] = $productOrders->getList(array(
                        'user_id' => $self['user_id'],
                        'limit' => !empty($param['product_order_limit']) ? $param['product_order_limit'] : 10,
                        'page' => !empty($param['product_order_page']) ? $param['product_order_page'] : 1,
                    )
                );           
            }
        }
        return $self;
    }
    
    public function register($param, &$userId = 0)
    {
        $_id = self::add($param, $userId);
        if (!empty($_id) && !empty($userId)) {
            $websiteHasUserModel = new WebsiteHasUsers;
            $websiteHasUserModel->addUpdate(array(
                'website_id' => $param['website_id'],
                'user_id' => $userId,
            ));            
            $addresses = new Addresses;
            $addresses->add(
                array(
                    'user_id' => $userId,
                    'country_code' => $param['country_code'],
                    'state_code' => $param['state_code'],
                    'city_code' => $param['city_code'],
                    'street' => $param['street'],
                    'name' => $param['address_name'],
                ),
                $addressId
            );
            if (!empty($addressId)) {
                self::update(
                    array(
                        'set' => array('address_id' => $addressId),
                        'where' => array(
                            '_id' => $_id
                        ),
                    )
                );
            }            
        }          
        return $_id;
    }
    
    /*
    * Facbook Login
    * @param email
    * @param password
    * @return array()
    */  
    public function fbLogin($param) { 
        $isFirstLogin = 0;
        $param['facebook_image'] = "http://graph.facebook.com/{$param['facebook_id']}/picture?type=large";
        $param['facebook_username'] = !empty($param['facebook_username']) ? $param['facebook_username'] : $param['facebook_id'];
        if (empty($param['access_token'])) {
            $param['access_token'] = '';
        }
        if (empty($param['facebook_email'])) {
            $param['facebook_email'] = $param['facebook_username'] . '-fb-vuongquocbalo@gmail.com'; 
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'user_id', 
                '_id', 
                'email', 
                'name',
                'display_name',
                'active',
                'website_id',
                'phone',        
                'mobile',   
            ))
            ->join(
                'user_facebooks', 
                static::$tableName . '.user_id = user_facebooks.user_id',
                array(
                    'facebook_id',
                    'facebook_name',
                    'facebook_username',
                    'facebook_email',
                    'facebook_first_name',
                    'facebook_last_name',
                    'facebook_link',
                    'facebook_image',
                    'facebook_gender',
                    'access_token',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.email = '. static::quote($param['facebook_email']))
            ->limit(1)
            ->offset(0);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = static::getDb()->query($selectString, Adapter::QUERY_MODE_EXECUTE); 
        $user = self::response($result, self::RETURN_TYPE_ONE);   
        $userFacebookModel = new UserFacebooks;    
        $image = new Images;
        if (!empty($user['user_id'])) {
            if (empty($user['facebook_email'])) {
                $userFacebookModel->insert(array(
                    'user_id' => $user['user_id'],
                    'facebook_id' => $param['facebook_id'],
                    'facebook_email' => $param['facebook_email'],
                    'facebook_name' => $param['facebook_name'],
                    'facebook_username' => $param['facebook_username'],                    
                    'facebook_first_name' => $param['facebook_first_name'],
                    'facebook_last_name' => $param['facebook_last_name'],
                    'facebook_link' => $param['facebook_link'],
                    'facebook_image' => $param['facebook_image'],
                    'facebook_gender' => $param['facebook_gender'],
                    'access_token' => $param['access_token'],
                ));
            } else {                
                $userFacebookModel->update(array(                        
                    'set' => array(
                        'facebook_id' => $param['facebook_id'],                            
                        'facebook_email' => $param['facebook_email'],
                        'facebook_name' => $param['facebook_name'],
                        'facebook_username' => $param['facebook_username'],                    
                        'facebook_first_name' => $param['facebook_first_name'],
                        'facebook_last_name' => $param['facebook_last_name'],
                        'facebook_link' => $param['facebook_link'],
                        'facebook_image' => $param['facebook_image'],
                        'facebook_gender' => $param['facebook_gender'],
                        'access_token' => $param['access_token'],
                    ),
                    'where' => array('user_id' => $user['user_id'])
                ));
            }
        } else {
            if ($param['facebook_gender'] == 'male') {
                $param['gender'] = 1;
            } elseif ($param['facebook_gender'] == 'female') {
                $param['gender'] = 2;
            } else {
                $param['gender'] = 0;
            }
            $param['name'] = array();
            if (!empty($param['facebook_last_name'])) {
                $param['name'][] = $param['facebook_last_name'];
            }
            if (!empty($param['facebook_first_name'])) {
                $param['name'][] = $param['facebook_first_name'];
            }
            $param['name'] = !empty($param['name']) ? implode(' ', $param['name']) : '';
            $user['_id'] = self::add(array(              
                'email' => $param['facebook_email'],
                'username' => $param['facebook_username'],
                'display_name' => $param['facebook_name'],                                    
                'name' => $param['name'], 
                'gender' => $param['gender'],
                'website_id' => $param['website_id'],
            ), $user['user_id']);
            if (!empty($user['user_id'])) {
                $isFirstLogin = 1;
                $param['image_id'] = $image->add(array(
                    'src' => 'users',
                    'src_id' => $user['user_id'],
                    'url_image' => $param['facebook_image'],
                    'is_main' => 1,
                ));    
                self::update(array(
                    'set' => array(
                        'image_id' => $param['image_id']
                    ),
                    'where' => array('user_id' => $user['user_id'])
                ));
                $userFacebookModel->insert(array(
                    'user_id' => $user['user_id'],
                    'facebook_id' => $param['facebook_id'],
                    'facebook_email' => $param['facebook_email'],
                    'facebook_name' => $param['facebook_name'],
                    'facebook_username' => $param['facebook_username'],                    
                    'facebook_first_name' => $param['facebook_first_name'],
                    'facebook_last_name' => $param['facebook_last_name'],
                    'facebook_link' => $param['facebook_link'],
                    'facebook_image' => $param['facebook_image'],
                    'facebook_gender' => $param['facebook_gender'],
                    'access_token' => $param['access_token'],
                ));
            }
        }       
        if (!empty($user['_id'])) {            
            $websiteHasUserModel = new WebsiteHasUsers;
            $websiteHasUserModel->addUpdate(array(
                'website_id' => $param['website_id'],
                'user_id' => $user['user_id'],
            ));
            $user = self::getLogin(array('_id' => $user['_id']));
            $user['is_first_login'] = $isFirstLogin;
            $user['facebook_id'] = $param['facebook_id'];
            return $user;
        }
        return false;
    }
    
    /*
    * Google Login
    * @param email
    * @param password
    * @return array()
    */  
    public function gLogin($param) { 
        $isFirstLogin = 0;        
        $param['google_username'] = !empty($param['google_username']) ? $param['google_username'] : '';
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'user_id', 
                '_id', 
                'email', 
                'name',
                'display_name',
                'active',
                'website_id',
                'phone',        
                'mobile',   
            ))
            ->join(
                'user_googles', 
                static::$tableName . '.user_id = user_googles.user_id',
                array(
                    'google_id',
                    'google_name',
                    'google_username',
                    'google_email',
                    'google_first_name',
                    'google_last_name',
                    'google_link',
                    'google_image',
                    'google_gender',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.email = '. static::quote($param['google_email']))
            ->limit(1)
            ->offset(0);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = static::getDb()->query($selectString, Adapter::QUERY_MODE_EXECUTE); 
        $user = self::response($result, self::RETURN_TYPE_ONE);   
        $userGoogleModel = new UserGoogles;    
        $image = new Images;
        if (!empty($user['user_id'])) {
            if (empty($user['google_email'])) {
                $userGoogleModel->insert(array(
                    'user_id' => $user['user_id'],
                    'google_id' => $param['google_id'],
                    'google_email' => $param['google_email'],
                    'google_name' => $param['google_name'],
                    'google_username' => $param['google_username'],                    
                    'google_first_name' => $param['google_first_name'],
                    'google_last_name' => $param['google_last_name'],
                    'google_link' => $param['google_link'],
                    'google_image' => $param['google_image'],
                    'google_gender' => $param['google_gender'],
                ));
            } else {                
                $userGoogleModel->update(array(                        
                    'set' => array(
                        'google_id' => $param['google_id'],                            
                        'google_email' => $param['google_email'],
                        'google_name' => $param['google_name'],
                        'google_username' => $param['google_username'],                    
                        'google_first_name' => $param['google_first_name'],
                        'google_last_name' => $param['google_last_name'],
                        'google_link' => $param['google_link'],
                        'google_image' => $param['google_image'],
                        'google_gender' => $param['google_gender'],
                    ),
                    'where' => array('user_id' => $user['user_id'])
                ));
            }
        } else {
            if ($param['google_gender'] == 'male') {
                $param['gender'] = 1;
            } elseif ($param['google_gender'] == 'female') {
                $param['gender'] = 2;
            } else {
                $param['gender'] = 0;
            }
            $param['name'] = array();
            if (!empty($param['google_last_name'])) {
                $param['name'][] = $param['google_last_name'];
            }
            if (!empty($param['google_first_name'])) {
                $param['name'][] = $param['google_first_name'];
            }
            $param['name'] = !empty($param['name']) ? implode(' ', $param['name']) : '';
            $user['_id'] = self::add(array(              
                'email' => $param['google_email'],
                'username' => $param['google_username'],
                'display_name' => $param['google_name'],                                    
                'name' => $param['name'], 
                'gender' => $param['gender'],
                'website_id' => $param['website_id'],
            ), $user['user_id']);
            if (!empty($user['user_id'])) {
                $isFirstLogin = 1;
                $param['image_id'] = $image->add(array(
                    'src' => 'users',
                    'src_id' => $user['user_id'],
                    'url_image' => $param['google_image'],
                    'is_main' => 1,
                ));   
                self::update(array(
                    'set' => array(
                        'image_id' => $param['image_id']
                    ),
                    'where' => array('user_id' => $user['user_id'])
                ));
                $userGoogleModel->insert(array(
                    'user_id' => $user['user_id'],
                    'google_id' => $param['google_id'],
                    'google_email' => $param['google_email'],
                    'google_name' => $param['google_name'],
                    'google_username' => $param['google_username'],                    
                    'google_first_name' => $param['google_first_name'],
                    'google_last_name' => $param['google_last_name'],
                    'google_link' => $param['google_link'],
                    'google_image' => $param['google_image'],
                    'google_gender' => $param['google_gender'],
                ));
            }
        }       
        if (!empty($user['_id'])) {            
            $websiteHasUserModel = new WebsiteHasUsers;
            $websiteHasUserModel->addUpdate(array(
                'website_id' => $param['website_id'],
                'user_id' => $user['user_id'],
            ));
            $user = self::getLogin(array('_id' => $user['_id']));
            $user['is_first_login'] = $isFirstLogin;
            return $user;
        }
        return false;
    }
    
     /*
    * @desction get List users
    */
    public function getFbAdmin($param)
    {
        $fbModel = new UserFacebooks;
        return $fbModel->getAdmin($param);
    }
    
}
