<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductSizes extends AbstractModel {
    
    protected static $properties = array(
        'size_id',
        '_id',
        'sort',      
        'locale',
        'name',       
        'short',       
        'created',
        'updated',
        'active',
        'website_id',
    );
    
    protected static $primaryKey = 'size_id';
    
    protected static $tableName = 'product_sizes';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'product_size_locales', 
                static::$tableName . '.size_id = product_size_locales.size_id',
                array('name', 'short')
            )            
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where("product_size_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("product_size_locales.name LIKE '%{$param['name']}%'"));
        }        
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_size_locales.{$match[1]} " . $match[2]);
                        break;                  
                    case 'sort':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.sort ASC');
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
                'size_id',
                '_id'
            ))
            ->join(               
                array(
                    'product_size_locales' => 
                    $sql->select()
                        ->from('product_size_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.size_id = product_size_locales.size_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )           
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param, &$id = 0)
    {
        $detail = self::getDetail(array(
            'name' => $param['name'],
            'website_id' => $param['website_id'],
        ));
        if (!empty($detail)) {
            $id = $detail['size_id'];
            return $detail['_id'];
        }
        $_id = mongo_id();  // product_sizes._id              
        $values = array(
            '_id' => $_id,
            'website_id' => $param['website_id']
        );
        if ($id = self::insert($values)) {
            $localeValues = array(
                'size_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }            
            self::insert($localeValues, 'product_size_locales');
            if (empty(self::error())) {             
                return $_id;                
            }            
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
        if (isset($param['sort'])) {
            $set['sort'] = $param['sort'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {
            $locales = \Application\Module::getConfig('general.locales');
            if (count($locales) == 1) {
                $param['locale'] = array_keys($locales)[0];
                self::addUpdateLocale($param);
            }                        
            return true;
        } 
        return false;
    }

    public function addUpdateLocale($param)
    {
        $detail = self::getDetail(array(
            '_id' => $param['_id'],
            'locale' => $param['locale'],
        ));
        if (empty($detail)) {
            self::errorNotExist('_id');
            return false;
        }        
        
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        }         
        if (empty($detail['locale'])) {
            $values['size_id'] = $detail['size_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values, 'product_size_locales');
        }
        return self::update(
            array(
                'table' => 'product_size_locales',
                'set' => $values,
                'where' => array(
                    'size_id' => $detail['size_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
    }

    public function getDetail($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'size_id', 
                '_id',
                'sort',
            ))
            ->join(               
                array(
                    'product_size_locales' => 
                    $sql->select()
                        ->from('product_size_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.size_id = product_size_locales.size_id',
                array(
                    'locale', 
                    'name', 
                    'short',                   
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['name'])) {            
            $select->where('product_size_locales.name = '. self::quote($param['name']));  
        }
        if (!empty($param['size_id'])) {            
            $select->where(static::$tableName . '.size_id = '. self::quote($param['size_id']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );        
        return $result;
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
}
