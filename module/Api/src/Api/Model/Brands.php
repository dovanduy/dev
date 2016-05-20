<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Brands extends AbstractModel {
    
    protected static $properties = array(
        'brand_id',
        '_id',
        'url_id',
        'sort',
        'url',
        'locale',
        'name',
        'short',
        'about',
        'meta_keyword',
        'meta_description',
        'created',
        'updated',
        'active',
        'featured',
        'image_id',
        'website_id',
    );
    
    protected static $primaryKey = 'brand_id';
    
    protected static $tableName = 'brands';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'brand_locales', 
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array('name', 'short')
            )
            ->join(
                'brand_images', 
                static::$tableName . '.image_id = brand_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("brand_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("brand_locales.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (!empty($param['category_id'])) {
            $select->where(new Expression(
                static::$tableName .  ".brand_id IN (
                    SELECT brand_id 
                    FROM brand_has_categories 
                    WHERE category_id IN ({$param['category_id']})
                )"
            ));
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
                        $select->order("brand_locales.{$match[1]} " . $match[2]);
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
                'brand_id', 
                'url_id', 
                '_id', 
                'url',
                'featured',
                'image_id'
            ))
            ->join(               
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'meta_keyword',
                    'meta_description',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'brand_images', 
                static::$tableName . '.image_id = brand_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.website_id = '. $param['website_id'])
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // brands._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'brands',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        }       
        if (empty($param['url_id']) && !empty($param['name'])) {
            $param['url_id'] = name_2_url($param['name']);
        }
        $values = array(
            '_id' => $_id,
            'url_id' => empty($param['url_id']) ? $param['url_id'] : '',
            'sort' => self::max(array('field' => 'sort')) + 1,
            'is_locale' => \Application\Module::getConfig('general.default_is_locale')
        );          
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['url'])) {
            $values['url'] = $param['url'];
        }
        if (isset($param['website_id'])) {
            $values['website_id'] = $param['website_id'];
        }
        if ($id = self::insert($values)) {
            $localeValues = array(
                'brand_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            } 
            if (isset($param['about'])) {
                $localeValues['about'] = $param['about'];
            }
            if (isset($param['meta_keyword'])) {
                $localeValues['meta_keyword'] = mb_strtolower($param['meta_keyword']);
            }        
            if (isset($param['meta_description'])) {
                $localeValues['meta_description'] = $param['meta_description'];
            }
            self::insert($localeValues, 'brand_locales');
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'brands',
                    'src_id' => $id,
                    'id' => $param['image_id']
                ));
                if (!empty($param['category_id'])) {
                    $websiteHasCategories = new WebsiteHasCategories();
                    $websiteHasCategories->addUpdate(
                        array(
                            'brand_id' => $id,
                            'category_id' => $param['category_id']
                        )
                    );
                }
            }
            if (empty(self::error()) && !empty($param['name'])) {
                $urlIds = new UrlIds();
                $urlIds->addUpdateByBrandId(array(
                    'url' => name_2_url($param['name']),
                    'brand_id' => $id,
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
                        'src' => 'brands',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'brands',
                        'src_id' => $self['brand_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'brands'
                ));
            }
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }
        if (empty($param['url_id']) && !empty($param['name'])) {
            $param['url_id'] = name_2_url($param['name']);
        }
        if (isset($param['url_id'])) {
            $set['url_id'] = $param['url_id'];
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
        
        static::$tableName = 'brand_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['about'])) {
            $values['about'] = $param['about'];
        }
        if (isset($param['meta_keyword'])) {
            $values['meta_keyword'] = mb_strtolower($param['meta_keyword']);
        }
        if (isset($param['meta_description'])) {
            $values['meta_description'] = $param['meta_description'];
        }
        if (empty($detail['locale'])) {
            $values['brand_id'] = $detail['brand_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        $ok = self::update(
            array(
                'set' => $values,
                'where' => array(
                    'brand_id' => $detail['brand_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
        if ($ok && !empty($param['name'])) {
            $urlIds = new UrlIds();
            $urlIds->addUpdateByBrandId(array(
                'url' => name_2_url($param['name']),
                'brand_id' => $detail['brand_id'],
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
                'brand_id', 
                '_id', 
                'url_id', 
                'url', 
                'sort',
                'image_id',
            ))
            ->join(               
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'about',
                    'meta_keyword',
                    'meta_description',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'brand_images',                   
                static::$tableName . '.image_id = brand_images.image_id',
                array(
                    'url_image', 
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['brand_id'])) {            
            $select->where(static::$tableName . '.brand_id = '. self::quote($param['brand_id']));  
        }
        if (!empty($param['url_id'])) {            
            $select->where(static::$tableName . '.url_id = '. self::quote($param['url_id']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if ($result) {
            $websiteHasCategories = new WebsiteHasCategories();
            $result['category_id'] = Arr::field(
                $websiteHasCategories->find(
                    array(            
                        'where' => array(
                            'brand_id' => $result['brand_id']
                        )
                    )
                ),
                'category_id'
            );
        }
        return $result;
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
    public function filter($param) {
        if (empty($param['brand_id'])) {
            return array();
        }
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()            
            ->from(static::$tableName)  
            ->columns(array(                
                'brand_id', 
                'product_id'               
            ))            
            ->join(
                'products', 
                static::$tableName . '.product_id = products.product_id',
                array('price')
            )
            ->join(
                'brands', 
                'products.brand_id = brands.brand_id',
                array('brand_id'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )            
            ->where('products.active = 1')     
            ->where('brands.active = 1')
            ->where(new Expression(static::$tableName . '.brand_id IN ('. $param['brand_id'] . ')')); 
        $rows = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        $result = array(
            'brand' => array(),
            'price' => array('min' => 0, 'max' => 0),            
        );
        if (!empty($rows)) {
            $brandId = array();
            foreach ($rows as $row) {
                if (isset($brandId[$row['brand_id']])) {
                    $brandId[$row['brand_id']]++;
                } else {
                    $result['brand'][] = array(
                        'brand_id' => $row['brand_id'],
                        'brand_name' => $row['brand_name'],
                    );
                    $brandId[$row['brand_id']] = 0;
                }   
                if ($result['price']['min'] > $row['price']) {
                    $result['price']['min'] = $row['price'];
                }
                if ($result['price']['max'] < $row['price']) {
                    $result['price']['max'] = $row['price'];
                }
            }      
            foreach ($result['brand'] as &$row) {
                $row['count_product'] = !empty($brandId[$row['brand_id']]) ? $brandId[$row['brand_id']] : 0; 
            }
            unset($row);                
        }
        return $result;
    } 
}
