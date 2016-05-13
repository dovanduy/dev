<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Blocks extends AbstractModel {
    
    protected static $properties = array(
        'block_id',
        '_id',       
        'sort',
        'url',
        'locale',
        'name',
        'short',       
        'created',
        'updated',
        'active',
        'website_id',
    );
    
    protected static $primaryKey = 'block_id';
    
    protected static $tableName = 'blocks';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'block_locales', 
                static::$tableName . '.block_id = block_locales.block_id',
                array('name', 'short')
            ) 
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where("block_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("block_locales.name LIKE '%{$param['name']}%'"));
        }        
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|url|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("block_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'url':
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
                'block_id',                 
                '_id', 
                'url'
            ))
            ->join(               
                array(
                    'block_locales' => 
                    $sql->select()
                        ->from('block_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.block_id = block_locales.block_id',
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
    
    public function add($param)
    {
        $_id = mongo_id();  // blocks._id        
        $values = array(
            '_id' => $_id,           
            'sort' => self::max(array('field' => 'sort')) + 1,
            'website_id' => $param['website_id']
        );  
        if (isset($param['url'])) {
            $values['url'] = $param['url'];
        }       
        if ($id = self::insert($values)) {
            $localeValues = array(
                'block_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }            
            self::$tableName = 'block_locales';
            self::insert($localeValues);            
            if (empty(self::error()) && !empty($param['name'])) {
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
        if (isset($param['url'])) {
            $set['url'] = $param['url'];
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
        
        static::$tableName = 'block_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        }        
        if (empty($detail['locale'])) {
            $values['block_id'] = $detail['block_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        $ok = self::update(
            array(
                'set' => $values,
                'where' => array(
                    'block_id' => $detail['block_id'],
                    'locale' => $param['locale'],
                ),
            )
        );        
        return $ok;
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
                'block_id', 
                '_id',                  
                'url', 
                'sort',                
            ))
            ->join(               
                array(
                    'block_locales' => 
                    $sql->select()
                        ->from('block_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.block_id = block_locales.block_id',
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
        if (!empty($param['block_id'])) {            
            $select->where(static::$tableName . '.block_id = '. self::quote($param['block_id']));  
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
