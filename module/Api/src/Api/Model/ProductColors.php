<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductColors extends AbstractModel {
    
    protected static $properties = array(
        'color_id',
        'code',
        '_id',
        'sort',      
        'locale',
        'name',       
        'short',       
        'created',
        'updated',
        'active',
        'website_id'
    );
    
    protected static $primaryKey = 'color_id';
    
    protected static $tableName = 'product_colors';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'product_color_locales', 
                static::$tableName . '.color_id = product_color_locales.color_id',
                array('name', 'short')
            )            
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where("product_color_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("product_color_locales.name LIKE '%{$param['name']}%'"));
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
                        $select->order("product_color_locales.{$match[1]} " . $match[2]);
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
                'color_id', 
                'code',                 
                '_id'
            ))
            ->join(               
                array(
                    'product_color_locales' => 
                    $sql->select()
                        ->from('product_color_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.color_id = product_color_locales.color_id',
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
        if (!empty($param['product_id'])) {
            $select->join(               
                array(
                    'product_has_colors' => 
                    $sql->select()
                        ->from('product_has_colors')
                        ->where("product_id = ". self::quote($param['product_id']))
                ),                    
                static::$tableName . '.color_id = product_has_colors.color_id',
                array(
                    'image_id'                   
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(             
                'product_images',                   
                'product_has_colors.image_id = product_images.image_id',
                array(                   
                    'url_image',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );
        }
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        return $data;
    }    
    
    public function add($param, &$id = 0)
    {
        $detail = self::getDetail(array(
            'name' => $param['name'],
            'website_id' => $param['website_id'],
        ));
        if (!empty($detail)) {
            $id = $detail['color_id'];
            return $detail['_id'];
        }
        $_id = mongo_id();  // product_colors._id              
        $values = array(
            '_id' => $_id,
            'website_id' => $param['website_id'],  
            'sort' => 
                self::max(array(
                    'table' => 'product_colors',
                    'field' => 'sort',
                    'where' => array(
                        'website_id' => $param['website_id']
                    )
                )) + 1                          
        ); 
        if (isset($param['code'])) {
            $values['code'] = $param['code'];
        }             
        if ($id = self::insert($values)) {
            $localeValues = array(
                'color_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }
            self::insert($localeValues, 'product_color_locales');
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
        if (isset($param['code'])) {
            $set['code'] = $param['code'];
        }    
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
            $values['color_id'] = $detail['color_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values, 'product_color_locales');
        }
        return self::update(
            array(
                'table' => 'product_color_locales',
                'set' => $values,
                'where' => array(
                    'color_id' => $detail['color_id'],
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
                'color_id', 
                'code', 
                '_id', 
                'sort'
            ))
            ->join(               
                array(
                    'product_color_locales' => 
                    $sql->select()
                        ->from('product_color_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.color_id = product_color_locales.color_id',
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
            $select->where('product_color_locales.name = '. self::quote($param['name']));  
        }
        if (!empty($param['color_id'])) {            
            $select->where(static::$tableName . '.color_id = '. self::quote($param['color_id']));  
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
