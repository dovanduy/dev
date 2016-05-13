<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductReviews extends AbstractModel {
    
    protected static $properties = array(
        'review_id',
        '_id',
        'sort',        
        'name',
        'title',
        'content',
        'rating',
        'created',
        'updated',        
        'active',       
        'product_id',
        'parent_id',
        'website_id'
    );
    
    protected static $primaryKey = 'review_id';
    
    protected static $tableName = 'product_reviews';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName);
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (isset($param['parent_id']) && $param['parent_id'] !== '') {            
            $select->where(static::$tableName . '.parent_id = '. $param['parent_id']);  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("product_reviews.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['title'])) {
            $select->where(new Expression("product_reviews.title LIKE '%{$param['title']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|title|created)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);            
            }            
        } else {
            $select->order(static::$tableName . '.created ASC');
        }         
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'review_id', 
                '_id', 
                'name',
                'title',
                'content',
                'rating',
                'product_id',
                'parent_id',
                'created',
            ))            
            ->where(static::$tableName . '.active = 1')     
            ->order('created DESC');   
        if (isset($param['product_id']) && $param['product_id'] !== '') {            
            $select->where(static::$tableName . '.product_id = '. $param['product_id']);  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // product_reviews._id            
        $values = array(
            '_id' => $_id,
            'name' => $param['name'], 
            'content' => $param['content'],            
            'product_id' => $param['product_id'],
            'website_id' => $param['website_id'],
			'title' => !empty($param['title']) ? $param['title'] : '',
			'rating' => !empty($param['rating']) ? $param['rating'] : 0,
            'parent_id' => !empty($param['parent_id']) ? $param['parent_id'] : 0,            
        ); 
        if ($id = self::insert($values)) {            
            return $_id;
        }        
        return false;
    }

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
        if (isset($param['title'])) {
            $set['title'] = $param['title'];
        }
        if (isset($param['content'])) {
            $set['content'] = $param['content'];
        }
		if (isset($param['rating'])) {
            $set['rating'] = $param['rating'];
        }
        if (isset($param['parent_id'])) {
            $set['parent_id'] = $param['parent_id'];
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
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'review_id', 
                '_id', 
                'name',
                'title',
                'content',
                'rating',
                'product_id',
                'parent_id'
            ))            
            ->where("_id = ". self::quote($param['_id']));                      
        $row = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );       
        return $row;
    }
    
}
