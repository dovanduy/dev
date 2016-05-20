<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Menus extends AbstractModel {
    
    protected static $properties = array(
        'menu_id',
        '_id',
        'sort',
        'locale',
        'url',
        'name',
        'short',       
        'created',
        'updated',
        'type',
        'active',
        'image_id',
        'parent_id',
        'website_id'
    );
    
    protected static $primaryKey = 'menu_id';
    
    protected static $tableName = 'menus';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'menu_locales', 
                static::$tableName . '.menu_id = menu_locales.menu_id',
                array('name', 'short')
            )
            ->join(
                'menu_images', 
                static::$tableName . '.image_id = menu_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("menu_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (isset($param['parent_id']) && $param['parent_id'] !== '') {            
            $select->where(static::$tableName . '.parent_id = '. $param['parent_id']);  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['type'])) {        
            $select->where(static::$tableName . '.type = '. self::quote($param['type']));  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("menu_locales.name LIKE '%{$param['name']}%'"));
        }       
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(updated|name|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("menu_locales.{$match[1]} " . $match[2]);
                        break;
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
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'menu_id', 
                '_id', 
                'sort',
                'url',
                'image_id',
                'type',
                'parent_id',
                'active',
            ))
            ->join(               
                array(
                    'menu_locales' => 
                    $sql->select()
                        ->from('menu_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.menu_id = menu_locales.menu_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )            
            ->where(static::$tableName . '.website_id = '. $param['website_id'])
            ->where(static::$tableName . '.active = 1')     
            ->order('type')
            ->order('sort');
        if (!empty($param['type'])) {        
            $select->where(static::$tableName . '.type = '. self::quote($param['type']));  
        }
        $selectString = $sql->getSqlStringForSqlObject($select);       
        return self::response(
            static::selectQuery($selectString), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // menus._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'menus',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        }        
        $values = array(
            '_id' => $_id,
            'sort' => self::max(array('field' => 'sort')) + 1,
        );          
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['parent_id'])) {
            $values['parent_id'] = $param['parent_id'];
        }
        if (isset($param['website_id'])) {
            $values['website_id'] = $param['website_id'];
        }
        if (isset($param['url'])) {
            $values['url'] = $param['url'];
        }
        if (isset($param['type'])) {
            $values['type'] = $param['type'];
        }
        if ($id = self::insert($values)) {
            $localeValues = array(
                'menu_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }             
            self::$tableName = 'menu_locales';
            self::insert($localeValues);
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'menus',
                    'src_id' => $id,
                    'id' => $param['image_id']
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
        $set = array();
        if (isset($param['sort'])) {
            $set['sort'] = $param['sort'];
        }
        if (isset($param['url'])) {
            $set['url'] = $param['url'];
        }
        if (isset($param['type'])) {
            $set['type'] = $param['type'];
        }
        if (isset($param['parent_id'])) {
            $set['parent_id'] = $param['parent_id'];
        }
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'menus',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'menus',
                        'src_id' => $self['menu_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'menus'
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
        
        static::$tableName = 'menu_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        }         
        if (empty($detail['locale'])) {
            $values['menu_id'] = $detail['menu_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        return self::update(
            array(
                'set' => $values,
                'where' => array(
                    'menu_id' => $detail['menu_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
    }

    public function getDetail($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'menu_id', 
                '_id', 
                'url',
                'type',
                'sort',
                'image_id',
                'parent_id'
            ))
            ->join(               
                array(
                    'menu_locales' => 
                    $sql->select()
                        ->from('menu_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.menu_id = menu_locales.menu_id',
                array(
                    'locale', 
                    'name', 
                    'short',                    
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("_id = ". self::quote($param['_id']));                  
        $row = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );        
        return $row;
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
}
