<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Addresses extends AbstractModel {

    protected static $properties = array(
        'address_id',
        '_id',        
        'name',
        'country_code',
        'state_code',
        'city_code',
        'street',        
        'active',
        'created',
        'updated',        
        'user_id',
        'website_id',
    );
    
    protected static $tableName = 'addresses';
    
    public function add($param, &$id = 0)
    {
        $_id = mongo_id();  // addresses._id   
        $values = array(
            '_id' =>  $_id, 
            'user_id' => $param['user_id'],            
        );
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
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
        if ($id = self::insert($values)) {  
            $addressList = $this->find(array(
                'where' => array(
                    'user_id' => $param['user_id'],
                    'active' => 1
                )
            ));
            if (count($addressList) == 1) {
                $userModel = new Users;
                $userModel->update(array(
                    'set' => array('address_id' => $id),
                    'where' => array(
                        'user_id' => $param['user_id']                        
                    )
                ));
            }
            return $_id;
        }        
        return false;
    }

    /*
    * @desction get List addresses
    */
    public function getList($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'address_id', 
                '_id',                
                'name',
                'country_code',
                'state_code',
                'city_code',
                'street',
                'active',
                'user_id',
                'website_id',
            ));         
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. self::quote($param['active']));  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['user_id'])) {            
            $select->where(static::$tableName . '.user_id = '. self::quote($param['user_id']));  
        }
        if (!empty($param['country_code'])) {            
            $select->where(static::$tableName . '.country_code = '. self::quote($param['country_code']));  
        }
        if (!empty($param['state_code'])) {            
            $select->where(static::$tableName . '.state_code = '. self::quote($param['state_code']));  
        }
        if (!empty($param['city_code'])) {            
            $select->where(static::$tableName . '.city_code = '. self::quote($param['city_code']));  
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
            preg_match("/(name|country_code|state_code|city_code)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
               $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);               
            }            
        } 
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    /*
    * @desction get List addresses
    */
    public function getAll($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'address_id', 
                '_id',                
                'name',
                'country_code',
                'state_code',
                'city_code',
                'street',
                'active',
                'user_id',
            ))
            ->join(
                'users', 
                static::$tableName . '.user_id = users.user_id',
                array('_user_id' => '_id')  
            )
            ->join(
                'location_countries', 
                static::$tableName . '.country_code = location_countries.iso_a2',
                array('country_name' => 'name'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'location_states', 
                static::$tableName . '.state_code = location_states.iso',
                array('state_name' => 'name'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'location_cities', 
                static::$tableName . '.city_code = location_cities.code',
                array('city_name' => 'name'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.user_id = '. self::quote($param['user_id'])); 
        if (isset($param['active']) && $param['active'] !== '') {
            $select->where(static::$tableName . '.active = '. self::quote($param['active'])); 
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|country_name|state_name|city_name)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
               $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);               
            }            
        } 
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rows = static::toArray(static::selectQuery($selectString));
        if (!empty($rows)) {
            foreach ($rows as &$row) {               
                $item = array();
                if (!empty($row['street'])) {
                    $item[] = $row['street']; 
                }
                if (!empty($row['city_name'])) {
                    $item[] = $row['city_name']; 
                }
                if (!empty($row['state_name'])) {
                    $item[] = $row['state_name']; 
                }
                if (!empty($row['country_name'])) {
                    $item[] = $row['country_name']; 
                }
                $row['address_full'] = implode(', ', $item);                
            } 
            unset($row);
        }
        return $rows;
    }
    
    /*
    * @desction update info addresses
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
