<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Util;

class Contacts extends AbstractModel {

    protected static $properties = array(
        'contact_id',
        '_id',
        'email',   
        'phone',    
        'name',
        'subject',
        'content',        
        'created',
        'updated',        
        'website_id',         
        'active',         
    );
    
    protected static $tableName = 'contacts';
   
    
    public function add($param)
    {
        $_id = mongo_id();  // contacts._id           
        $values = array(
            '_id' =>  $_id,          
            'email' => $param['email'],                     
            'name' => $param['name'],            
            'subject' => $param['subject'],            
            'content' => $param['content'],            
            'website_id' => $param['website_id'],            
        );
        if (isset($param['phone'])) {
            $values['phone'] = $param['phone'];
        }         
        if (self::insert($values)) {             
            return $_id;
        }        
        return false;
    }    

    /*
    * @desction get List contacts
    */
    public function getList($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'contact_id', 
                '_id', 
                'email', 
                'phone',      
                'name',                
                'active',
                'website_id',                               
                'created',   
                'subject',
                'content',   
            ));
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
        if (!empty($param['phone'])) {
            $select->where(new Expression(static::$tableName .  ".phone LIKE '%{$param['phone']}%'"));
        } 
        if (!empty($param['subject'])) {
            $select->where(new Expression(static::$tableName .  ".subject LIKE '%{$param['subject']}%'"));
        } 
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(email|name|phone)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {                      
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                } 
            }            
        } else {
            $select->order(static::$tableName . '.created DESC');
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));        
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => $data, 
        );
    }
    
    /*
    * @desction get List contacts
    */
    public function getAll($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'contact_id', 
                '_id', 
                'email', 
                'phone',      
                'name',                
                'active',
                'website_id',                               
                'created',   
                'subject',
                'content',     
            ));           
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. static::quote($param['active']));  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. static::quote($param['website_id']));  
        }
        if (!empty($param['contact_id'])) {            
            $select->where(static::$tableName . '.contact_id = '. static::quote($param['contact_id']));  
        }
        if (!empty($param['email'])) {
            $select->where(new Expression(static::$tableName .  ".email LIKE '%{$param['email']}%'"));
        }
        if (!empty($param['name'])) {
            $select->where(new Expression(static::$tableName .  ".name LIKE '%{$param['name']}%'"));
        } 
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            $select->offset(0);
        }
        if (!empty($param['sort'])) {
            preg_match("/(email|name|created)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
               $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);               
            }            
        } else {
            $select->order(static::$tableName . '.created DESC');
        }  
        $selectString = $sql->getSqlStringForSqlObject($select);
        return static::toArray(static::selectQuery($selectString));
    }
    
    /*
    * @desction get List contacts
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
                'id' => 'contact_id',              
                'text' => 'name'                
            ))
            ->where(static::$tableName . '.active = 1'); 
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (!empty($param['keyword'])) {
            $select->where(new Expression("
                contacts.email LIKE '%{$param['keyword']}%'
                OR
                contacts.name LIKE '%{$param['keyword']}%'
                OR
                contacts.subject LIKE '%{$param['keyword']}%'
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
    * @desction update info contacts
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
        if (isset($param['email'])) {
            $set['email'] = $param['email'];
        }                
        if (isset($param['phone'])) {
            $set['phone'] = $param['phone'];
        }       
        if (isset($param['active'])) {
            $set['active'] = $param['active'];
        }
        if (isset($param['subject'])) {
            $set['subject'] = $param['subject'];
        } 
        if (isset($param['content'])) {
            $set['content'] = $param['content'];
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
        $where = array();
        if (!empty($param['_id'])) {
            $where['_id'] = $param['_id'];
        }
        if (!empty($param['contact_id'])) {
            $where['contact_id'] = $param['contact_id'];           
        }
        if (empty($where)) {
            self::errorParamInvalid('contact_id');
            return false;
        }
        $self = self::find(
            array(
                'where' => $where,
            ),
            self::RETURN_TYPE_ONE
        );        
        return $self;
    }
    
}
