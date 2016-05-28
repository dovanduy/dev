<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Banners extends AbstractModel {
    
    protected static $properties = array(
        'banner_id',
        '_id',
        'sort',
        'url',
        'locale',
        'title',
        'short',       
        'created',
        'updated',
        'active',        
        'image_id',
        'website_id'
    );
    
    protected static $primaryKey = 'banner_id';
    
    protected static $tableName = 'banners';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'banner_locales', 
                static::$tableName . '.banner_id = banner_locales.banner_id',
                array('title', 'short')
            )
            ->join(
                'banner_images', 
                static::$tableName . '.image_id = banner_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where("banner_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['title'])) {
            $select->where(new Expression("banner_locales.name LIKE '%{$param['title']}%'"));
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
                        $select->order("banner_locales.{$match[1]} " . $match[2]);
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
                'banner_id', 
                '_id', 
                'url',               
                'image_id'
            ))
            ->join(               
                array(
                    'banner_locales' => 
                    $sql->select()
                        ->from('banner_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.banner_id = banner_locales.banner_id',
                array(
                    'locale', 
                    'title', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'banner_images', 
                static::$tableName . '.image_id = banner_images.image_id',
                array('url_image'),
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
        $_id = mongo_id();  // banners._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'banners',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        }        
        $values = array(
            '_id' => $_id,
            'sort' => self::max(array('field' => 'sort')) + 1,            
            'website_id' => $param['website_id'],            
        );          
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['url'])) {
            $values['url'] = $param['url'];
        }       
        if ($id = self::insert($values)) {
            $localeValues = array(
                'banner_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['title'])) {
                $localeValues['title'] = $param['title'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }             
            self::$tableName = 'banner_locales';
            self::insert($localeValues);
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'banners',
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
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'banners',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'banners',
                        'src_id' => $self['banner_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'banners'
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
        
        static::$tableName = 'banner_locales';
        $values = array();
        if (isset($param['title'])) {
            $values['title'] = $param['title'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        }         
        if (empty($detail['locale'])) {
            $values['banner_id'] = $detail['banner_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        return self::update(
            array(
                'set' => $values,
                'where' => array(
                    'banner_id' => $detail['banner_id'],
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
                'banner_id', 
                '_id', 
                'url', 
                'sort',
                'image_id',
            ))
            ->join(               
                array(
                    'banner_locales' => 
                    $sql->select()
                        ->from('banner_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.banner_id = banner_locales.banner_id',
                array(
                    'locale', 
                    'title', 
                    'short',                   
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'banner_images',                   
                static::$tableName . '.image_id = banner_images.image_id',
                array(
                    'url_image', 
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['banner_id'])) {            
            $select->where(static::$tableName . '.banner_id = '. self::quote($param['banner_id']));  
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
