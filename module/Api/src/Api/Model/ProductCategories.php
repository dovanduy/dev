<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Log;
use Application\Lib\Util;
use Application\Lib\Arr;

class ProductCategories extends AbstractModel {
    
    protected static $properties = array(
        'category_id',
        '_id',
        'sort',
        'locale',
        'name',
        'short',
        'content',
        'meta_keyword',
        'meta_description',
        'created',
        'updated',
        'featured',
        'active',
        'image_id',
        'parent_id',
        'path_id',
        'website_id'
    );
    
    protected static $primaryKey = 'category_id';
    
    protected static $tableName = 'product_categories';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'product_category_locales', 
                static::$tableName . '.category_id = product_category_locales.category_id',
                array('name', 'short')
            )
            ->join(
                'product_category_images', 
                static::$tableName . '.image_id = product_category_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("product_category_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
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
            $select->where(new Expression("product_category_locales.name LIKE '%{$param['name']}%'"));
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
                        $select->order("product_category_locales.{$match[1]} " . $match[2]);
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
                'category_id', 
                '_id', 
                'sort',
                'image_id',
                'featured',
                'path_id',
                'parent_id'
            ))
            ->join(               
                array(
                    'product_category_locales' => 
                    $sql->select()
                        ->from('product_category_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.category_id = product_category_locales.category_id',
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
                'product_category_images', 
                static::$tableName . '.image_id = product_category_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');   
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (isset($param['parent_id']) && $param['parent_id'] !== '') {            
            $select->where(static::$tableName . '.parent_id = '. $param['parent_id']);  
        }
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
            $id = $detail['category_id'];
            if (isset($param['return_id'])) {
                return $id;
            }
            return $detail['_id'];
        }
        $_id = mongo_id();  // product_categories._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'product_categories',
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
        if ($id = self::insert($values)) {
            $localeValues = array(
                'category_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            }
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            } 
            if (isset($param['content'])) {
                $localeValues['content'] = $param['content'];
            }        
            if (isset($param['meta_keyword'])) {
                $localeValues['meta_keyword'] = mb_strtolower($param['meta_keyword']);
            }        
            if (isset($param['meta_description'])) {
                $localeValues['meta_description'] = $param['meta_description'];
            }        
            self::insert($localeValues, 'product_category_locales');
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'product_categories',
                    'src_id' => $id,
                    'id' => $param['image_id'],
                    'website_id' => $param['website_id'],
                ));
            }
            if (empty(self::error()) && !empty($param['name'])) {
                $urlIds = new UrlIds();
                $urlIds->addUpdateByCategoryId(array(
                    'url' => name_2_url($param['name']),
                    'category_id' => $id,
                    'website_id' => $param['website_id'],
                ));
            }
            if (isset($param['return_id'])) {
                return $id;
            }
            return $_id;
        }        
        return false;
    }

    public function updateInfo($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $self = self::find(
            array(            
                'where' => array(
                    'website_id' => $param['website_id'],
                    '_id' => $param['_id']
                )
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('_id');
            return false;
        }   
        $param['category_id'] = $self['category_id'];
        $set = array();
        if (isset($param['sort'])) {
            $set['sort'] = $param['sort'];
        }
        if (isset($param['parent_id'])) {
            $set['parent_id'] = $param['parent_id'];
        }
        $pathId = Arr::field(self::findParent($param), 'category_id');
        if (!empty($pathId)) {
            $set['path_id'] = \Zend\Json\Encoder::encode($pathId);
        }
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'product_categories',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'product_categories',
                        'src_id' => $self['category_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'product_categories'
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
                    '_id' => $param['_id'],
                    'website_id' => $param['website_id'],
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
            'website_id' => $param['website_id'],
            '_id' => $param['_id'],
            'locale' => $param['locale'],
        ));
        if (empty($detail)) {
            self::errorNotExist('_id');
            return false;
        }
        
        static::$tableName = 'product_category_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['content'])) {
            $values['content'] = $param['content'];
        }
        if (isset($param['meta_keyword'])) {
            $values['meta_keyword'] = mb_strtolower($param['meta_keyword']);
        }
        if (isset($param['meta_description'])) {
            $values['meta_description'] = $param['meta_description'];
        }
        if (empty($detail['locale'])) {
            $values['category_id'] = $detail['category_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        $ok = self::update(
            array(
                'set' => $values,
                'where' => array(
                    'category_id' => $detail['category_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
        if ($ok && !empty($param['name'])) {
            $urlIds = new UrlIds();
            $urlIds->addUpdateByCategoryId(array(
                'url' => name_2_url($param['name']),
                'category_id' => $detail['category_id'],
                'website_id' => $param['website_id'],
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
                'category_id', 
                '_id', 
                'sort',
                'image_id',
                'path_id',
                'parent_id'
            ))
            ->join(               
                array(
                    'product_category_locales' => 
                    $sql->select()
                        ->from('product_category_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.category_id = product_category_locales.category_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                    'meta_keyword',
                    'meta_description',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . ".website_id = ". self::quote($param['website_id']));         
        if (!empty($param['_id'])) {
            $select->where(static::$tableName . "._id = ". self::quote($param['_id'])); 
        }
        if (!empty($param['name'])) {
            $select->where("product_category_locales.name = ". self::quote($param['name'])); 
        }       
        $row = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if (!empty($row)) {
            $productCategoryHasFieldsModel = new ProductCategoryHasFields();
            $row['fields'] = $productCategoryHasFieldsModel->getAll(array(
                'website_id' => $param['website_id'],
                'category_id' => $row['category_id'],
                'locale' => $param['locale'],
            ));
        }
        return $row;
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
    public function findParent($param)
    {         
        $result = array();
        $categories = self::getAll($param); 
        $find = Arr::filter($categories, 'category_id', $param['category_id'], false, false);        
        $find = !empty($find[0]) ? $find[0] : array();
        if (!empty($find)) {
            $result[] = $find;           
            while (!empty($find['parent_id'])) {
                $find = Arr::filter($categories, 'category_id', $find['parent_id'], false, false);
                $find = !empty($find[0]) ? $find[0] : array();
                if (!empty($find)) {
                    $result[] = $find;
                }
            }
        }
        return array_reverse($result); 
    }
    
}
