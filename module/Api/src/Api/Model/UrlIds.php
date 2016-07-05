<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class UrlIds extends AbstractModel {
    
    protected static $properties = array(
        'id',       
        'url',       
        'created',
        'updated',
        'active',
        'website_id',
        'category_id',
        'product_id',
        'brand_id',
        'page_id',
        'option_id',
    );
    
    protected static $primaryKey = 'id';
    
    protected static $tableName = 'url_ids';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName);
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['url'])) {
            $select->where(new Expression(static::$tableName . ".url LIKE '%{$param['url']}%'"));
        }   
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(url)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("brand_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'url':
                    case 'sort':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.created DESC');
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
            ->where(static::$tableName . '.active = 1')     
            ->order('name');   
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    { 
        $values = array(          
            'url' => empty($param['url']) ? $param['url'] : ''            
        );          
        if (isset($param['category_id'])) {
            $values['category_id'] = $param['category_id'];
        }
        if (isset($param['brand_id'])) {
            $values['brand_id'] = $param['brand_id'];
        }
        if (isset($param['product_id'])) {
            $values['product_id'] = $param['product_id'];
        }
        if (isset($param['page_id'])) {
            $values['page_id'] = $param['page_id'];
        }
        if (isset($param['option_id'])) {
            $values['option_id'] = $param['option_id'];
        }
        if (isset($param['website_id'])) {
            $values['website_id'] = $param['website_id'];
        }
        if ($id = self::insert($values)) {        
            return $_id;
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
        if (isset($param['url'])) {
            $set['url'] = $param['url'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    'id' => $param['id']
                ),
            )
        )) {                                 
            return true;
        } 
        return false;
    }
    
    public function addUpdateByCategoryId($param)
    {          
        if (empty($param['category_id']) || empty($param['url'])) {
            self::errorParamInvalid('category_id_or_url');
            return false;
        }
        $self = self::find(
            array(            
                'where' => array(
                    'category_id' => $param['category_id'],
                    'website_id' => $param['website_id'],
                )
            ),
            self::RETURN_TYPE_ONE
        ); 
        if (!empty($self)) {
            $set = array();        
            if (isset($param['url'])) {
                $set['url'] = $param['url'];
            }
            if (isset($param['website_id'])) {
                $set['website_id'] = $param['website_id'];
            }
            if (self::update(
                array(
                    'set' => $set,
                    'where' => array(
                        'id' => $self['id']
                    ),
                )
            )) {                                 
                return true;
            } 
        } else {
            $values = array(
                'category_id' => $param['category_id'] ,               
                'url' => $param['url'],
                'website_id' => $param['website_id'],
            ); 
            if (self::insert($values)) {        
                return true;
            }
        }
        return false;        
    }
    
    public function addUpdateByProductId($param)
    {
        if (empty($param['product_id']) || empty($param['url'])) {
            self::errorParamInvalid('product_id_or_url');
            return false;
        }
        $self = self::find(
            array(            
                'where' => array(
                    'product_id' => $param['product_id'],
                    'website_id' => $param['website_id'],
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($self)) {
            $set = array();        
            if (isset($param['url'])) {
                $set['url'] = $param['url'];
            }
            if (isset($param['website_id'])) {
                $set['website_id'] = $param['website_id'];
            }
            if (self::update(
                array(
                    'set' => $set,
                    'where' => array(
                        'id' => $self['id']
                    ),
                )
            )) {                                 
                return true;
            } 
        } else {
            $values = array(
                'website_id' => $param['website_id'],
                'product_id' => $param['product_id'] ,               
                'url' => $param['url'],                
            );
            if (self::insert($values)) {        
                return true;
            }
        }
        return false;
    }
    
    public function addUpdateByBrandId($param)
    {
        if (empty($param['brand_id']) || empty($param['url'])) {
            self::errorParamInvalid('brand_id_or_url');
            return false;
        }
        $self = self::find(
            array(            
                'where' => array(
                    'brand_id' => $param['brand_id'],
                    'website_id' => $param['website_id'],
                )
            ),
            self::RETURN_TYPE_ONE
        ); 
        if (!empty($self)) {
            $set = array();        
            if (isset($param['url'])) {
                $set['url'] = $param['url'];
            }
            if (isset($param['website_id'])) {
                $set['website_id'] = $param['website_id'];
            }
            if (self::update(
                array(
                    'set' => $set,
                    'where' => array(
                        'id' => $self['id']
                    ),
                )
            )) {                                 
                return true;
            } 
        } else {
            $values = array(
                'brand_id' => $param['brand_id'] ,               
                'url' => $param['url'],
                'website_id' => $param['website_id'],
            ); 
            if (self::insert($values)) {        
                return true;
            }
        }
        return false;
    }
    
    public function addUpdateByPageId($param)
    {
        if (empty($param['page_id']) || empty($param['url'])) {
            self::errorParamInvalid('page_id_or_url');
            return false;
        }
        $self = self::find(
            array(            
                'where' => array(
                    'page_id' => $param['page_id'],
                    'website_id' => $param['website_id'],
                )
            ),
            self::RETURN_TYPE_ONE
        ); 
        if (!empty($self)) {
            $set = array();        
            if (isset($param['url'])) {
                $set['url'] = $param['url'];
            }
            if (isset($param['website_id'])) {
                $set['website_id'] = $param['website_id'];
            }
            if (self::update(
                array(
                    'set' => $set,
                    'where' => array(
                        'id' => $self['id']
                    ),
                )
            )) {                                 
                return true;
            } 
        } else {
            $values = array(
                'page_id' => $param['page_id'] ,               
                'url' => $param['url'],
                'website_id' => $param['website_id'],
            ); 
            if (self::insert($values)) {        
                return true;
            }
        }
        return false;        
    }
    
    public function addUpdateByOptionId($param)
    {
        if (empty($param['option_id']) || empty($param['url']) || empty($param['website_id'])) {
            self::errorParamInvalid('option_id_or_url');
            return false;
        }
        $self = self::find(
            array(            
                'where' => array(
                    'option_id' => $param['option_id'],
                    'website_id' => $param['website_id'],
                )
            ),
            self::RETURN_TYPE_ONE
        );       
        if (!empty($self)) {           
            if (self::update(
                array(
                    'set' => array(
                        'url' => $param['url']
                    ),
                    'where' => array(
                        'id' => $self['id'],
                        'website_id' => $param['website_id'],
                    ),
                )
            )) {                                 
                return true;
            }
        } else {
            $values = array(
                'option_id' => $param['option_id'] ,               
                'url' => $param['url'],
                'website_id' => $param['website_id'],
            ); 
            if (self::insert($values)) {        
                return true;
            }
        }
        return false;
    }
    
    public function getDetail($param)
    {       
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)
            ->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));        
        if (!empty($param['id'])) {            
            $select->where(static::$tableName . '.id = '. self::quote($param['id']));  
        }
        if (!empty($param['url'])) {            
            $select->where(static::$tableName . '.url = '. self::quote($param['url']));  
        }        
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        ); 
        if (empty($result)) {
            
        }
        return $result;
    }  
    
}
