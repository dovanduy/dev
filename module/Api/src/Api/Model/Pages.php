<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Pages extends AbstractModel {
    
    protected static $properties = array(
        'page_id',
        '_id',
        'sort',      
        'locale',
        'title',
        'short',
        'content',
        'created',
        'updated',
        'active',       
        'website_id',       
    );
    
    protected static $primaryKey = 'page_id';
    
    protected static $tableName = 'pages';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'page_locales', 
                static::$tableName . '.page_id = page_locales.page_id',
                array('title', 'short')
            )            
            ->where("page_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['title'])) {
            $select->where(new Expression("page_locales.name LIKE '%{$param['title']}%'"));
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
            preg_match("/(title|url|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'title':
                        $select->order("page_locales.{$match[1]} " . $match[2]);
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
                'page_id', 
                '_id',                                 
            ))
            ->join(               
                array(
                    'page_locales' => 
                    $sql->select()
                        ->from('page_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.page_id = page_locales.page_id',
                array(
                    'locale', 
                    'title', 
                    'short',                   
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )            
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // pages._id              
        $values = array(
            '_id' => $_id,
            'sort' => self::max(array('field' => 'sort')) + 1,
            'website_id' => $param['website_id']
        );  
        $param['locale'] = \Application\Module::getConfig('general.default_locale');
        if ($id = self::insert($values)) {
            $localeValues = array(
                'page_id' => $id,
                'locale' => $param['locale'],                                
            );
            if (isset($param['title'])) {
                $localeValues['title'] = $param['title'];
            }
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            } 
            if (isset($param['content'])) {
                $localeValues['content'] = $param['content'];
            }
            self::$tableName = 'page_locales';
            self::insert($localeValues);
            if (!empty($param['title'])) {                
                $urlIds = new UrlIds();
                $urlIds->addUpdateByPageId(array(
                    'url' => name_2_url($param['title'], '.html'),
                    'page_id' => $id,
                    'website_id' => $param['website_id']
                ));                
            }
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
        $locales = \Application\Module::getConfig('general.locales');
        if (count($locales) == 1) {
            $param['locale'] = array_keys($locales)[0];
            self::addUpdateLocale($param);
        }                        
        return true;       
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
        
        static::$tableName = 'page_locales';
        $values = array();
        if (isset($param['title'])) {
            $values['title'] = $param['title'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['content'])) {
            $values['content'] = $param['content'];
        }
        if (empty($detail['locale'])) {
            $values['page_id'] = $detail['page_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        $ok = self::update(
            array(
                'set' => $values,
                'where' => array(
                    'page_id' => $detail['page_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
        if ($ok && !empty($param['title'])) {
            $urlIds = new UrlIds();
            $urlIds->addUpdateByPageId(array(
                'url' => name_2_url($param['title'], '.html'),
                'page_id' => $detail['page_id'],
                'website_id' => $param['website_id']
            ));
        }
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
                'page_id', 
                '_id',                
                'sort',               
            ))
            ->join(               
                array(
                    'page_locales' => 
                    $sql->select()
                        ->from('page_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.page_id = page_locales.page_id',
                array(
                    'locale', 
                    'title', 
                    'short',
                    'content',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );            
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['page_id'])) {            
            $select->where(static::$tableName . '.page_id = '. self::quote($param['page_id']));  
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
