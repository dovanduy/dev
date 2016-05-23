<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class EmailLogs extends AbstractModel {
    
    protected static $properties = array(
        'id',
        'subject',       
        'content',
        'from_email',
        'from_name',
        'to_email',
        'to_name',     
        'created',
        'updated',
        'is_sent',
        'website_id',
    );
    
    protected static $primaryKey = 'id';
    
    protected static $tableName = 'email_logs';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)
            ->where(static::$tableName . '.website_id = ' . $param['website_id']); 
        if (!empty($param['subject'])) {            
            $select->where(static::$tableName . '.subject = '. $param['subject']);  
        }
        if (isset($param['is_sent']) && $param['is_sent'] !== '') {            
            $select->where(static::$tableName . '.is_sent = '. $param['is_sent']);  
        }
        if (!empty($param['from_email'])) {
            $select->where(new Expression("from_email LIKE '%{$param['from_email']}%'"));
        }
        if (!empty($param['from_name'])) {
            $select->where(new Expression("from_name LIKE '%{$param['from_name']}%'"));
        }
        if (!empty($param['to_email'])) {
            $select->where(new Expression("to_email LIKE '%{$param['to_email']}%'"));
        }
        if (!empty($param['to_name'])) {
            $select->where(new Expression("to_name LIKE '%{$param['to_name']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(to_email|to_name|is_sent|updated)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.updated DESC');
        }         
        $selectString = $sql->getSqlStringForSqlObject($select);      
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)           
            ->where(static::$tableName . '.website_id = ' . $param['website_id'])
            ->order('updated DESC');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $values = array(           
            'website_id' => $param['website_id']
        );  
        if (isset($param['subject'])) {
            $values['subject'] = $param['subject'];
        }       
        if (isset($param['content'])) {
            $values['content'] = $param['content'];
        }       
        if (isset($param['from_email'])) {
            $values['from_email'] = $param['from_email'];
        }       
        if (isset($param['from_name'])) {
            $values['from_name'] = $param['from_name'];
        }       
        if (isset($param['to_email'])) {
            $values['to_email'] = $param['to_email'];
        }       
        if (isset($param['to_name'])) {
            $values['to_name'] = $param['to_name'];
        }  
        if (isset($param['is_sent']) && $param['is_sent'] !== '') {            
            $values['is_sent'] = $param['is_sent'];
        }
        if ($id = self::insert($values)) {
            return $id;        
        }        
        return false;
    }

    public function updateInfo($param)
    {
        $self = self::find(
            array(            
                'where' => array('id' => $param['id'])
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('id');
            return false;
        }        
        $set = array();
        if (isset($param['subject'])) {
            $set['subject'] = $param['subject'];
        } 
        if (isset($param['content'])) {
            $set['content'] = $param['content'];
        }
        if (isset($param['from_email'])) {
            $set['from_email'] = $param['from_email'];
        }       
        if (isset($param['from_name'])) {
            $set['from_name'] = $param['from_name'];
        }       
        if (isset($param['to_email'])) {
            $set['to_email'] = $param['to_email'];
        }       
        if (isset($param['to_name'])) {
            $set['to_name'] = $param['to_name'];
        }  
        if (isset($param['is_sent']) && $param['is_sent'] !== '') {            
            $set['is_sent'] = $param['is_sent'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['id']
                ),
            )
        )) {                                  
            return true;
        } 
        return false;
    }   

    public function getDetail($param)
    {       
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)
            ->where(static::$tableName . '.id = '. self::quote($param['id']));            
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }                
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );        
        return $result;
    }     
    
}
