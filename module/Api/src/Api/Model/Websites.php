<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Websites extends AbstractModel {
    
    protected static $properties = array(
        'website_id',
        '_id',
        'sort',
        'url',        
        'logo_text',        
        'created',
        'updated',
        'active',
        'image_id',
        'phone',
        'email',
        'facebook',
        'twitter',
        'youtube',
        'linkedin',
        'locale',
        'name',
        'company_name',
        'address',
        'short',
        'about',
        'copyright',
        'meta_keyword',
        'meta_description',
    );
    
    protected static $primaryKey = 'website_id';
    
    protected static $tableName = 'websites';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'website_locales', 
                static::$tableName . '.website_id = website_locales.website_id',
                array('name', 'short')
            )
            ->join(
                'website_images', 
                static::$tableName . '.image_id = website_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("website_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("website_locales.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['category_id'])) {
            $select->where(new Expression(
                static::$tableName .  ".website_id IN (
                    SELECT website_id 
                    FROM website_has_categories 
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
                        $select->order("website_locales.{$match[1]} " . $match[2]);
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
                'website_id', 
                '_id', 
                'url',
                'image_id',
                'phone',
                'email',
            ))
            ->join(               
                array(
                    'website_locales' => 
                    $sql->select()
                        ->from('website_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.website_id = website_locales.website_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'address',
                    'copyright',
                    'meta_keyword',
                    'meta_description',
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
        $_id = mongo_id();  // websites._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'websites',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        }        
        $values = array(
            '_id' => $_id,
            'sort' => self::max(array('field' => 'sort')) + 1,
            'is_locale' => \Application\Module::getConfig('general.default_is_locale')
        );          
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['url'])) {
            $values['url'] = $param['url'];
        }
        if (isset($param['email'])) {
            $values['email'] = $param['email'];
        }         
        if (isset($param['phone'])) {
            $values['phone'] = $param['phone'];
        }
        if (isset($param['facebook'])) {
            $values['facebook'] = $param['facebook'];
        }
        if (isset($param['twitter'])) {
            $values['twitter'] = $param['twitter'];
        }
        if (isset($param['youtube'])) {
            $values['youtube'] = $param['youtube'];
        }
        if (isset($param['linkedin'])) {
            $values['linkedin'] = $param['linkedin'];
        }        
        if (isset($param['logo_text'])) {
            $values['logo_text'] = $param['logo_text'];
        }        
        if ($id = self::insert($values)) {
            $localeValues = array(
                'website_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale')
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['company_name'])) {
                $localeValues['company_name'] = $param['company_name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            } 
            if (isset($param['about'])) {
                $localeValues['about'] = $param['about'];
            }
            if (isset($param['address'])) {
                $localeValues['address'] = $param['address'];
            }            
            self::addUpdateLocale($localeValues);
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'websites',
                    'src_id' => $id,
                    'id' => $param['image_id']
                ));
                if (!empty($param['category_id'])) {
                    $websiteHasCategories = new WebsiteHasCategories();
                    $websiteHasCategories->addUpdate(
                        array(
                            'website_id' => $id,
                            'category_id' => $param['category_id']
                        )
                    );
                }
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
        if (isset($param['email'])) {
            $set['email'] = $param['email'];
        }         
        if (isset($param['phone'])) {
            $set['phone'] = $param['phone'];
        }  
        if (isset($param['facebook'])) {
            $set['facebook'] = $param['facebook'];
        }
        if (isset($param['twitter'])) {
            $set['twitter'] = $param['twitter'];
        }
        if (isset($param['youtube'])) {
            $set['youtube'] = $param['youtube'];
        }
        if (isset($param['linkedin'])) {
            $set['linkedin'] = $param['linkedin'];
        } 
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'websites',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'websites',
                        'src_id' => $self['website_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'websites'
                ));
            }
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }
        if (isset($param['logo_text'])) {
            $set['logo_text'] = $param['logo_text'];
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
            if (isset($param['category_id'])) {
                if (empty($param['category_id'])) {
                    $param['category_id'] = array();
                }
                $websiteHasCategories = new WebsiteHasCategories();
                $websiteHasCategories->addUpdate(
                    array(
                        'website_id' => $self['website_id'],
                        'category_id' => $param['category_id']
                    )
                );
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
        
        static::$tableName = 'website_locales';
        $values = array();
        if (isset($param['name'])) {
            $values['name'] = $param['name'];
        } 
        if (isset($param['company_name'])) {
            $values['company_name'] = $param['company_name'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['about'])) {
            $values['about'] = $param['about'];
        }
        if (isset($param['address'])) {
            $values['address'] = $param['address'];
        }
        if (isset($param['copyright'])) {
            $values['copyright'] = $param['copyright'];
        }
        if (isset($param['meta_keyword'])) {
            $values['meta_keyword'] = mb_strtolower($param['meta_keyword']);
        }
        if (isset($param['meta_description'])) {
            $values['meta_description'] = $param['meta_description'];
        }
        if (empty($detail['locale'])) {
            $values['website_id'] = $detail['website_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        return self::update(
            array(
                'set' => $values,
                'where' => array(
                    'website_id' => $detail['website_id'],
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
                'website_id', 
                '_id', 
                'url', 
                'sort',
                'image_id',
                'phone',
                'email',
                'facebook',
                'twitter',
                'youtube',
                'linkedin',
                'logo_text',
            ))
            ->join(               
                array(
                    'website_locales' => 
                    $sql->select()
                        ->from('website_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.website_id = website_locales.website_id',
                array(
                    'locale', 
                    'name', 
                    'company_name', 
                    'short',
                    'about',
                    'address',
                    'copyright',
                    'meta_keyword',
                    'meta_description',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join( 
                'website_images',                    
                static::$tableName . '.image_id = website_images.image_id',
                array(
                    'url_image'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );           
        if (!empty($param['_id'])) {
            $select->where(static::$tableName . "._id = ". self::quote($param['_id'])); 
        } elseif (!empty($param['website_id'])) {
            $select->where(static::$tableName . ".website_id = ". self::quote($param['website_id'])); 
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
                            'website_id' => $result['website_id']
                        )
                    )
                ),
                'category_id'
            );
            if (!empty($param['get_menus'])) {
                $menuModel = new Menus;
                $result['menus'] = $menuModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1,
                ));
            }
            if (!empty($param['get_product_categories'])) {
                $categoryModel = new ProductCategories;
                $result['product_categories'] = $categoryModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1,
                ));
            }
            if (!empty($param['get_banners'])) {
                $bannerModel = new Banners;
                $result['banners'] = $bannerModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1,
                ));
            }
            if (!empty($param['get_brand_featureds'])) {
                $brandModel = new Brands;
                $result['brand_featureds'] = $brandModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1,
                    'featured' => 1,
                ));
            }
            if (!empty($param['get_brands'])) {
                $brandModel = new Brands;
                $result['brands'] = $brandModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1
                ));
            }
            if (!empty($param['get_blocks'])) {
                $blockModel = new Blocks;
                $result['blocks'] = $blockModel->getAll(array(
                    'website_id' => $result['website_id'],
                    'active' => 1,
                ));
            }
        }       
        return $result;
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
}
