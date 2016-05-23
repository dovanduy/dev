<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Products extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        '_id',
        'brand_id',
        'code',
        'model',
        'price',
        'original_price',
        'vat',
        'quantity',
        'url_video',
        'url_other',
        'website_id',
        'provider_id',
        'warranty',
        'weight',
        'size',
        'made_in',
        'locale',
        'name',
        'short',
        'content',
        'meta_keyword',
        'meta_description',
        'created',
        'updated',
        'active',
        'image_id',
        'sort',
        'priority',
        'featured',
        'latest_arrival',
        'top_seller',
    );
    
    protected static $primaryKey = 'product_id';
    
    protected static $tableName = 'products';
    
    public function getForHomepage($param)
    {
        $blockModel = new Blocks;
        $blockList = $blockModel->getAll(
            array(
                'website_id' => $param['website_id']
            )
        );
        if (empty($blockList)) {
            return array();
        }
        $param['block_id'] = implode(',', Arr::field($blockList, 'block_id'));
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
        );
        $select = $sql->select()
            ->from(static::$tableName) 
            ->join(
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name', 
                    'short',
                    'meta_keyword',
                    'meta_description',                    
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT 
            )
            ->join(  
                array(
                    'product_has_sizes' => 
                    $sql->select()
                        ->columns(array(
                            'product_id',
                            'size_id' => new Expression('GROUP_CONCAT(product_has_sizes.size_id SEPARATOR  \',\')')
                        ))
                        ->from('product_has_sizes')                        
                        ->group('product_id')
                ),             
                static::$tableName . '.product_id = product_has_sizes.product_id',
                array(
                    'size_id'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'block_products', 
                static::$tableName . '.product_id = block_products.product_id',
                array(
                    'block_id', 
                    'sort', 
                    'active'
                )
            )           
            ->where(static::$tableName . ".website_id = ". $param['website_id'])
            ->where(static::$tableName . '.active = 1')
            ->where(new Expression("block_products.block_id IN ({$param['block_id']})"))
            ->where('block_products.active = 1')
            ->order(static::$tableName . '.priority DESC')
            ->order('block_products.sort ASC')  
            ->order('block_products.sort ASC') 
            ->group('product_id');  
        $productList = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        ); 
        if (!empty($productList)) {
            foreach ($blockList as &$block) {
                $block['products'] = Arr::filter($productList, 'block_id', $block['block_id']);  
            }
            unset($block);
        }
        return $blockList;        
    }
    
    public function search($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
        );
        $select = $sql->select()
            ->from(static::$tableName) 
            ->columns($columns)
            ->join(
                'product_has_categories', 
                static::$tableName . '.product_id = product_has_categories.product_id',
                array('category_id'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                array(
                    'product_category_locales' => 
                    $sql->select()
                        ->from('product_category_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'product_category_locales.category_id = product_has_categories.category_id',
                array(
                    'category_name' => new Expression('GROUP_CONCAT(product_category_locales.name SEPARATOR  \', \')')
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )           
            ->join(
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array(
                    'brand_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name', 
                    'short',
                    'meta_keyword',
                    'meta_description',                    
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT 
            )
            ->join(
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )            
            ->where(static::$tableName . '.website_id = '. self::quote($param['website_id']))
            ->where(static::$tableName . '.active = 1');
        if (!empty($param['keyword'])) {
            $param['keyword'] = strtolower($param['keyword']);
            $select->where(new Expression("(
                LOWER(product_locales.name) LIKE '%{$param['keyword']}%'
                OR LOWER(brand_locales.name) LIKE '%{$param['keyword']}%'               
            )"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|price|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':  
                        if (isset($sortTable)) {
                            $select->order($sortTable . '.' . $match[1] . ' ' . $match[2]);
                            break;
                        }                        
                    case 'price':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order('product_locales.name ASC');
        }
        $select->group('product_id');
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getList($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
        );
        $select = $sql->select()
            ->from(static::$tableName) 
            ->join(
                'product_has_categories', 
                static::$tableName . '.product_id = product_has_categories.product_id',
                array(
                    'category_id'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                array(
                    'product_category_locales' => 
                    $sql->select()
                        ->columns(array(
                            'category_id',
                            'category_name' => new Expression('GROUP_CONCAT(product_category_locales.name SEPARATOR  \',\')')
                        ))
                        ->from('product_category_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                        ->group('category_id')
                ),                 
                'product_category_locales.category_id = product_has_categories.category_id',
                array(
                    'category_name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )             
            ->join(  
                array(
                    'product_has_sizes' => 
                    $sql->select()
                        ->columns(array(
                            'product_id',
                            'size_id' => new Expression('GROUP_CONCAT(product_has_sizes.size_id SEPARATOR  \',\')')
                        ))
                        ->from('product_has_sizes')                        
                        ->group('product_id')
                ),             
                static::$tableName . '.product_id = product_has_sizes.product_id',
                array(
                    'size_id'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array(
                    'brand_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'product_locales', 
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name', 
                    'short',
                    'meta_keyword',
                    'meta_description',
                )
            )
            ->join(
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . ".website_id = ". self::quote($param['website_id']))
            ->where("product_locales.locale = ". self::quote($param['locale']));        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. self::quote($param['active']));  
        }        
        if (!empty($param['brand_id'])) {            
            $select->where(static::$tableName . '.brand_id = '. self::quote($param['brand_id']));  
        }      
        if (!empty($param['option_id'])) {   
            if (is_array($param['option_id'])) {
                $param['option_id'] = implode(',', $param['option_id']);
            }
            $select->join(
                'product_has_fields', 
                static::$tableName . '.product_id = product_has_fields.product_id',
                array(
                    'field_id'                   
                )
            );
            $select->where(new Expression("LOCATE('[{$param['option_id']}]', product_has_fields.value_id) > 0"));
            $select->where('product_has_fields.active = 1');
        }
        if (isset($param['block_id'])) {    
            if (is_array($param['block_id'])) {
                $param['block_id'] = implode(',', $param['block_id']);
            }
            $select->join(
                'block_products', 
                static::$tableName . '.product_id = block_products.product_id',
                array(
                    'block_id', 
                    'sort', 
                    'active'
                )
            );
            $select->where(new Expression("block_products.block_id IN ({$param['block_id']})"));
            $select->where('block_products.active = 1');
            $sortTable = 'block_products';                       
        } else {
            $columns[] = 'active';
            $columns[] = 'sort';
        }
        $select->columns($columns);       
        if (!empty($param['name'])) {
            $select->where(new Expression("product_locales.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['price_from'])) {
            $param['price_from'] = db_float($param['price_from']);
            $select->where(new Expression(static::$tableName . ".price >= {$param['price_from']}"));
        }
        if (!empty($param['price_to'])) {
            $param['price_to'] = db_float($param['price_to']);
            $select->where(new Expression(static::$tableName . ".price <= {$param['price_to']}"));
        }
        if (!empty($param['category_id'])) {
            if (is_numeric($param['category_id'])) {
                $categoryModel = new ProductCategories;
                $categories = $categoryModel->getAll(array(
                    'website_id' => $param['website_id'],
                    'locale' => $param['locale'],
                    'parent_id' => $param['category_id'],
                ));
                if (!empty($categories)) {
                    $param['category_id'] = implode(',', Arr::field($categories, 'category_id'));
                }
            }
            $select->where(new Expression(
                "product_has_categories.category_id IN ({$param['category_id']})"
            ));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(product_id|name|price|sort|updated)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':  
                        if (isset($sortTable)) {
                            $select->order($sortTable . '.' . $match[1] . ' ' . $match[2]);                           
                        }     
                        break;
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.updated DESC');
        } 
        $select->group('product_id');        
        $selectString = $sql->getSqlStringForSqlObject($select);        
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getFeList($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
            'priority',
        );
        $select = $sql->select()
            ->from(static::$tableName)                         
            ->join(  
                array(
                    'product_has_sizes' => 
                    $sql->select()
                        ->columns(array(
                            'product_id',
                            'size_id' => new Expression('GROUP_CONCAT(product_has_sizes.size_id SEPARATOR  \',\')')
                        ))
                        ->from('product_has_sizes')                        
                        ->group('product_id')
                ),             
                static::$tableName . '.product_id = product_has_sizes.product_id',
                array(
                    'size_id'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),
                static::$tableName . '.brand_id = brand_locales.brand_id',
                array(
                    'brand_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'product_locales', 
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name', 
                    'short',
                    'meta_keyword',
                    'meta_description',
                )
            )
            ->join(
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . ".website_id = ". self::quote($param['website_id']))            
            ->where(static::$tableName . '.active = 1')
            ->where("product_locales.locale = ". self::quote($param['locale']));                       
        if (!empty($param['brand_id'])) {            
            $select->where(static::$tableName . '.brand_id = '. self::quote($param['brand_id']));  
        }      
        if (!empty($param['option_id'])) {   
            if (is_array($param['option_id'])) {
                $param['option_id'] = implode(',', $param['option_id']);
            }
            $select->join(
                'product_has_fields', 
                static::$tableName . '.product_id = product_has_fields.product_id',
                array(
                    'field_id'                   
                )
            );
            $select->where(new Expression("LOCATE('[{$param['option_id']}]', product_has_fields.value_id) > 0"));
            $select->where('product_has_fields.active = 1');
        }        
        $select->columns($columns);  
        if (!empty($param['price_from'])) {
            $param['price_from'] = db_float($param['price_from']);
            $select->where(new Expression(static::$tableName . ".price >= {$param['price_from']}"));
        }
        if (!empty($param['price_to'])) {
            $param['price_to'] = db_float($param['price_to']);
            $select->where(new Expression(static::$tableName . ".price <= {$param['price_to']}"));
        }
        if (!empty($param['category_id'])) {
            if (is_numeric($param['category_id'])) {
                $categoryModel = new ProductCategories;
                $categories = $categoryModel->getAll(array(
                    'website_id' => $param['website_id'],
                    'locale' => $param['locale'],
                    'parent_id' => $param['category_id'],
                ));
                if (!empty($categories)) {
                    $param['category_id'] = implode(',', Arr::field($categories, 'category_id'));
                }
            }
            $select->join(
                'product_has_categories', 
                static::$tableName . '.product_id = product_has_categories.product_id',
                array(
                    'category_id'
                )
            )
            ->where(new Expression(
                "product_has_categories.category_id IN ({$param['category_id']})"
            ));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(product_id|name|price|sort|updated|priority)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':  
                        if (isset($sortTable)) {
                            $select->order($sortTable . '.' . $match[1] . ' ' . $match[2]);                           
                        }     
                        break;
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.priority DESC');
            $select->order(static::$tableName . '.updated DESC');
        }
        $select->group('product_id');        
        $selectString = $sql->getSqlStringForSqlObject($select);        
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getRelated($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'product_id', 
                '_id', 
                'price',
                'original_price',
                'sort',
                'image_id'
            ))
            ->join(               
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(                   
                    'name',                     
                )
            )
            ->join(
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.active = 1')
            ->where(static::$tableName . '.website_id = '. self::quote($param['website_id']))
            ->where(new Expression(
                static::$tableName . ".product_id NOT IN ({$param['product_id']})"
            ));        
        if (!empty($param['brand_id'])) {            
            $select->where(static::$tableName . '.brand_id = '. self::quote($param['brand_id']));           
        }  
        if (!empty($param['category_id'])) {
            if (is_array($param['category_id'])) {
                $param['category_id'] = implode(',', $param['category_id']);
            }
            $select->join(
                'product_has_categories', 
                static::$tableName . '.product_id = product_has_categories.product_id',
                array(
                    'category_id'
                )    
            )
            ->where(new Expression(
                "product_has_categories.category_id IN ({$param['category_id']})"
            ));            
        }   
        if (!empty($param['option_id'])) {   
            if (is_array($param['option_id'])) {
                $param['option_id'] = implode(',', $param['option_id']);
            }
            $select->join(
                'product_has_fields', 
                static::$tableName . '.product_id = product_has_fields.product_id',
                array(
                    'field_id'                   
                )
            );
            $select->where(new Expression("LOCATE('[{$param['option_id']}]', product_has_fields.value_id) > 0"));
            $select->where('product_has_fields.active = 1');
        }
        $select->order(new Expression('RAND()'));  
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);            
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
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
                'product_id', 
                '_id', 
                'price',
                'original_price',
                'sort',
                'image_id'
            ))
            ->join(               
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.product_id = product_locales.product_id',
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
                'product_images', 
                static::$tableName . '.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.active = 1')
            ->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));     
        if (!empty($param['category_id'])) {
            if (is_numeric($param['category_id'])) {
                $categoryModel = new ProductCategories;
                $categories = $categoryModel->getAll(array(
                    'website_id' => $param['website_id'],
                    'locale' => $param['locale'],
                    'parent_id' => $param['category_id'],
                ));
                if (!empty($categories)) {
                    $param['category_id'] = implode(',', Arr::field($categories, 'category_id'));
                }
            }
            $select->join(
                    'product_has_categories', 
                    static::$tableName . '.product_id = product_has_categories.product_id',
                    array(
                        'category_id'
                    )    
                )
                ->where(new Expression(
                    "product_has_categories.category_id IN ({$param['category_id']})"
                ));            
        }
        if (!empty($param['not_in_product_id'])) {
            $select->where(new Expression(
                static::$tableName .  ".product_id NOT IN ({$param['not_in_product_id']})"
            ));
        }
        if (!empty($param['_id'])) {    
            if (is_array($param['_id'])) {
                $param['_id'] = implode(',', self::quote($param['_id']));
            } else {
                $param['_id'] = self::quote($param['_id']);
            }
            $select->where(static::$tableName . '._id IN ('. $param['_id'] . ')');  
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|price|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':  
                        if (isset($sortTable)) {
                            $select->order($sortTable . '.' . $match[1] . ' ' . $match[2]);
                            break;
                        }                        
                    case 'price':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(new Expression('RAND()')); 
        } 
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);            
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    } 
    
    public function add($param, &$id = 0)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $detail = self::getByName(
            array(
                'website_id' => $param['website_id'],
                'name' => $param['name'],
                'locale' => $param['locale'],
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($detail)) {
            $id = $detail['product_id'];
            return $detail['_id'];
        }
        $_id = mongo_id();  // products._id        
        $values = array(
            '_id' => $_id,            
            'priority' => 
                self::max(array(
                    'table' => 'products',
                    'field' => 'priority',
                    'where' => array(
                        'website_id' => $param['website_id']
                    )
                )) + 1,            
            'website_id' => $param['website_id'],
        );
        if (isset($param['sort'])) {
            $values['sort'] = $param['sort'];
        }  
        if (isset($param['price'])) {
            $values['price'] = Util::toPrice($param['price']);
        }  
        if (isset($param['original_price'])) {
            $values['original_price'] = Util::toPrice($param['original_price']);
        }  
        if (isset($param['code'])) {
            $values['code'] = $param['code'];
        }  
        if (isset($param['model'])) {
            $values['model'] = $param['model'];
        }  
        if (isset($param['vat'])) {
            $values['vat'] = $param['vat'];
        }  
        if (isset($param['url_video'])) {
            $values['url_video'] = $param['url_video'];
        }  
        if (isset($param['url_other'])) {
            $values['url_other'] = $param['url_other'];
        }  
        if (isset($param['made_in'])) {
            $values['made_in'] = $param['made_in'];
        }  
        if (isset($param['warranty'])) {
            $values['warranty'] = $param['warranty'];
        }  
        if (isset($param['weight'])) {
            $values['weight'] = $param['weight'];
        }  
        if (isset($param['size'])) {
            $values['size'] = $param['size'];
        }  
        if (isset($param['brand_id'])) {
            $values['brand_id'] = $param['brand_id'];
        }  
        if (isset($param['provider_id'])) {
            $values['provider_id'] = $param['provider_id'];
        }
        
        $imagesModel = new Images();        
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $mainImageUrl = $uploadResult['url_image'];                
            }          
        } elseif (!empty($param['url_image'])) {   
            $mainImageUrl = Util::uploadImageFromUrl($param['url_image']);            
        }
        if (!empty($mainImageUrl)) {
            $values['image_id'] = $imagesModel->add(array(
                'src' => 'products',
                'src_id' => 0,
                'url_image' => $mainImageUrl,
                'is_main' => 1,
            )); 
            if (isset($param['add_image_to_content'])) {
                $param['content'] .= "<center><p><img style=\"width:80%\" src=\"{$mainImageUrl}\"/></p></center>";
            }
        }
        if ($id = self::insert($values)) { 
            
            if (!empty($values['image_id'])) {                
                $imagesModel->updateInfo(array(
                    'src' => 'products',
                    'src_id' => $id,
                    'id' => $values['image_id']
                ));
            } 
            if (isset($param['images'])) {
                foreach ($param['images'] as $imageUrl) {
                    if ($param['url_image'] != $imageUrl) {
                        $imageUrl = Util::uploadImageFromUrl($imageUrl);
                        $imagesModel->add(array(
                            'src' => 'products',
                            'src_id' => $id,
                            'url_image' => $imageUrl,
                            'is_main' => 0,
                        )); 
                        if (isset($param['add_image_to_content'])) {
                            $param['content'] .= "<center><p><img style=\"width:80%\" src=\"{$imageUrl}\"/></p></center>";
                        }
                    }                    
                }
            }  
            
            $localeValues = array(
                'product_id' => $id,
                'locale' => \Application\Module::getConfig('general.default_locale'),         
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
            self::insert($localeValues, 'product_locales');            
            
            if (empty(self::error()) && !empty($param['name'])) {
                $urlIds = new UrlIds();
                $urlIds->addUpdateByProductId(array(
                    'url' => name_2_url($param['name']),
                    'product_id' => $id,
                    'website_id' => $param['website_id']
                ));
            }
            
            $productHascategoriesModel = new ProductHasCategories();
            $productHascategoriesModel->addUpdate(
                array(
                    'product_id' => $id,
                    'category_id' => $param['category_id']
                )
            );
            
            if (isset($param['size_id'])) {
                $productHasSizesModel = new ProductHasSizes();
                $productHasSizesModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'size_id' => $param['size_id']
                    )
                );
            }   
            
            if (isset($param['color_id'])) {
                $productHasColorsModel = new ProductHasColors();
                $productHasColorsModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'color_id' => $param['color_id']
                    )
                );
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
        if (isset($param['price'])) {
            $set['price'] = Util::toPrice($param['price']);
        }  
        if (isset($param['original_price'])) {
            $set['original_price'] = Util::toPrice($param['original_price']);
        }        
        if (isset($param['code'])) {
            $set['code'] = $param['code'];
        }  
        if (isset($param['model'])) {
            $set['model'] = $param['model'];
        }  
        if (isset($param['vat'])) {
            $set['vat'] = $param['vat'];
        }  
        if (isset($param['url_video'])) {
            $set['url_video'] = $param['url_video'];
        }  
        if (isset($param['url_other'])) {
            $set['url_other'] = $param['url_other'];
        }  
        if (isset($param['made_in'])) {
            $set['made_in'] = $param['made_in'];
        }  
        if (isset($param['warranty'])) {
            $set['warranty'] = $param['warranty'];
        }  
        if (isset($param['weight'])) {
            $set['weight'] = $param['weight'];
        }  
        if (isset($param['size'])) {
            $set['size'] = $param['size'];
        }        
        if (isset($param['brand_id'])) {
            $set['brand_id'] = $param['brand_id'];
        }
        if (isset($param['provider_id'])) {
            $set['provider_id'] = $param['provider_id'];
        } 
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'products',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'products',
                        'src_id' => $self['product_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'products'
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
            if (empty($param['category_id'])) {
                $param['category_id'] = array();
            }
            $productHasCategoriesModel = new ProductHasCategories();
            $productHasCategoriesModel->addUpdate(
                array(
                    'product_id' => $self['product_id'],
                    'category_id' => $param['category_id']
                )
            );
            $productHasSizesModel = new ProductHasSizes();
            $productHasSizesModel->addUpdate(
                array(
                    'product_id' => $self['product_id'],
                    'size_id' => $param['size_id']
                )
            );
            $productHasColorModel = new ProductHasColors();
            $productHasColorModel->addUpdate(
                array(
                    'product_id' => $self['product_id'],
                    'color_id' => $param['color_id']
                )
            );
            return true;
        } 
        return false;
    }

    public function addUpdateLocale($param)
    {
        $detail = self::getDetail(array(
            '_id' => $param['_id'],
            'locale' => $param['locale'], 
            'website_id' => $param['website_id']
        ));        
        if (empty($detail)) {
            self::errorNotExist('_id');
            return false;
        }
        
        static::$tableName = 'product_locales';
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
            $values['product_id'] = $detail['product_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        $ok = self::update(
            array(
                'set' => $values,
                'where' => array(
                    'product_id' => $detail['product_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
        if ($ok && !empty($param['name'])) {
            $urlIds = new UrlIds();
            $urlIds->addUpdateByProductId(array(
                'url' => name_2_url($param['name']),
                'product_id' => $detail['product_id'],
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
                'product_id', 
                '_id', 
                'brand_id',
                'code',
                'model',
                'price',
                'original_price',
                'vat',
                'quantity',
                'url_video',
                'url_other',
                'website_id',
                'provider_id',
                'warranty',
                'weight',
                'size',
                'made_in',                
                'active',
                'image_id'
            ))
            ->join(               
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                    'meta_keyword',
                    'meta_description',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );                      
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['product_id'])) {            
            $select->where(static::$tableName . '.product_id = '. self::quote($param['product_id']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if ($result) {
            if (!empty($result['brand_id'])) {
                $brands = new Brands();
                $result['brand'] = $brands->getDetail(array(
                    'brand_id' => $result['brand_id']
                ));
            }
            $productHascategoriesModel = new ProductHasCategories();
            $result['categories'] = $productHascategoriesModel->getAll(
                array( 
                    'product_id' => $result['product_id']
                )
            );           
            $result['category_id'] = Arr::field(
                $result['categories'],
                'category_id'
            );  
            
            $productHasSizesModel = new ProductHasSizes();
            $result['sizes'] = $productHasSizesModel->getAll(array(
                'product_id' => $result['product_id']
            ));
            $result['size_id'] = Arr::field(
                $result['sizes'],
                'size_id'
            ); 
            
            $productHasColorsModel = new ProductHasColors();
            $result['colors'] = $productHasColorsModel->getAll(array(
                'product_id' => $result['product_id']
            ));
            $result['color_id'] = Arr::field(
                $result['colors'],
                'color_id'
            );
            
            $productCategoryHasFields = new ProductCategoryHasFields();
            $result['attributes'] = $productCategoryHasFields->getAll(array(
                'category_id' => $result['category_id'],
                'locale' => $param['locale'],
            ));
            
            $productHasFieldsModel = new ProductHasFields;
            $values = $productHasFieldsModel->getAll(array(
                'product_id' => $result['product_id']                
            ));
            
            $optionId = array();
            foreach ($result['attributes'] as &$attribute) {
                foreach ($values as $value) {
                    if ($attribute['field_id'] == $value['field_id']) {
                        if (!empty($value['value']) || empty($value['value_id'])) {
                            $attribute['value'] = $value['value'];
                        } else {
                            $attribute['value'] = is_numeric($value['value_id']) ? $value['value_id'] : explode(',', $value['value_id']);
                        }
                        if (!empty($attribute['value'])) {
                            $optionId[] = $attribute['value'];
                        }
                    }
                }
            }
            unset($attribute);   
            
            if (isset($param['get_images'])) {
                $image = new Images();
                $result['images'] = $image->getAll(array(
                    'src_id' => $result['product_id'],
                    'src' => 'products'
                ));                
                if (!empty($result['images'])) {
                    foreach ($result['images'] as $image) {
                        if ($image['image_id'] == $result['image_id']) {
                            $result['url_image'] = $image['url_image'];
                            break;
                        }
                    }
                    if (empty($result['url_image'])) {
                        $result['url_image'] = $result['images'][0]['url_image'];
                    }
                }
            }
            
            $urlIds = new UrlIds();
            $detailUrlId = $urlIds->getDetail(array(
                'url' => name_2_url($result['name']),
                'website_id' => $param['website_id'],
            ));
            if (empty($detailUrlId)) {
                $param = array(
                    'url' => name_2_url($result['name']),
                    'product_id' => $result['product_id'],
                    'website_id' => $param['website_id'],
                );
                $urlIds->addUpdateByProductId($param);
                $result['url_id'] = $param;
            } else {
                $result['url_id'] = $detailUrlId; 
            }
            
            if (isset($param['get_product_reviews'])) {
                $reviewModel = new ProductReviews;
                $result['product_reviews'] = $reviewModel->getAll(array(
                    'product_id' => $result['product_id'],
                    'website_id' => $result['website_id']
                ));
            }
            
            if (isset($param['get_product_related'])) {                              
                $result['product_related'] = $this->getRelated(array(
                    'product_id' => $result['product_id'],
                    'website_id' => $result['website_id'],
                    'brand_id' => $result['brand_id'],
                    'category_id' => $result['category_id'],                    
                    'option_id' => $optionId,
                    'limit' => 10,
                ));
            }
            
            if (isset($param['replace_lazy_image'])) {  
                $dom = new \DOMDocument();            
                @$dom->loadHTML(mb_convert_encoding($result['content'], 'HTML-ENTITIES', 'UTF-8'));
                foreach ($dom->getElementsByTagName('img') as $img) {
                    $img->setAttribute('data-original', $img->getAttribute('src'));
                    $img->setAttribute('src', '');                
                    $img->setAttribute('class', 'lazy lazy-hidden');               
                    $img->setAttribute('alt', $result['name']);
                }
                $result['content'] = $dom->saveHTML();
            }
        }
        return $result;
    }
    
    public function getByName($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('products')  
            ->columns(array(                
                'product_id', 
                '_id', 
                'brand_id',
                'code',
                'model',
                'price',
                'original_price',
                'vat',
                'quantity',
                'url_video',
                'url_other',
                'website_id',
                'provider_id',
                'warranty',
                'weight',
                'size',
                'made_in',                
                'active',
                'image_id'
            ))
            ->join(               
                array(
                    'product_locales' => 
                    $sql->select()
                        ->from('product_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                    'meta_keyword',
                    'meta_description',
                )   
            );                      
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['product_id'])) {            
            $select->where(static::$tableName . '.product_id = '. self::quote($param['product_id']));  
        }
        if (!empty($param['name'])) {            
            $select->where('product_locales.name = '. self::quote($param['name']));  
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
    
    public function saveAttribute($param) {        
        $productHasField = new ProductHasFields;
        return $productHasField->addUpdate($param);
    }
    
    
    public function addFeatured($param)
    {            
        $product = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($product)) {
            self::errorNotExist('product_id');
            return false;
        }
        $values = array(
            'product_id' => $param['product_id'],
            'website_id' => $product['website_id'],
            'sort' => 
                self::max(
                    array(
                        'table' => 'product_featureds',
                        'field' => 'sort'
                    ),
                    array('where' => array(
                            'website_id' => $product['website_id']
                        )
                    )
                ) + 1,
        );
        $product = new ProductFeatureds();
        $result = $product->addUpdate($values);
        return $result;
    }
    
    public function removeFeatured($param)
    {    
        $productDetail = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($productDetail)) {
            self::errorNotExist('product_id');
            return false;
        }
        $product = new ProductFeatureds();
        $result = $product->remove($param);
        return $result;
    }
    
    public function updateSortFeatured($param) {
        $product = new ProductFeatureds();
        $result = $product->updateSort($param);
        return $result;
    }
    
    
    public function addTopSeller($param)
    {            
        $product = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($product)) {
            self::errorNotExist('product_id');
            return false;
        }
        $values = array(
            'product_id' => $param['product_id'],
            'website_id' => $product['website_id'],
            'sort' => 
                self::max(
                    array(
                        'table' => 'product_top_sellers',
                        'field' => 'sort'
                    ),
                    array('where' => array(
                            'website_id' => $product['website_id']
                        )
                    )
                ) + 1,
        );
        $product = new ProductTopSellers();
        $result = $product->addUpdate($values);
        return $result;
    }
    
    public function removeTopSeller($param)
    {    
        $productDetail = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($productDetail)) {
            self::errorNotExist('product_id');
            return false;
        }
        $product = new ProductTopSellers();
        $result = $product->remove($param);
        return $result;
    }
    
    public function updateSortTopSeller($param) {
        $product = new ProductTopSellers();
        $result = $product->updateSort($param);
        return $result;
    }
    
    public function addLatestArrival($param)
    {            
        $product = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($product)) {
            self::errorNotExist('product_id');
            return false;
        }
        $values = array(
            'product_id' => $param['product_id'],
            'website_id' => $product['website_id'],
            'sort' => 
                self::max(
                    array(
                        'table' => 'product_latest_arrivals',
                        'field' => 'sort'
                    ),
                    array('where' => array(
                            'website_id' => $product['website_id']
                        )
                    )
                ) + 1,
        );
        $product = new ProductLatestArrivals();
        $result = $product->addUpdate($values);
        return $result;
    }
    
    public function removeLatestArrival($param)
    {    
        $productDetail = self::find(
            array(            
                'where' => array('product_id' => $param['product_id'])
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($productDetail)) {
            self::errorNotExist('product_id');
            return false;
        }
        $product = new ProductLatestArrivals();
        $result = $product->remove($param);
        return $result;
    }
    
    public function updateSortLatestArrival($param) {
        $product = new ProductLatestArrivals();
        $result = $product->updateSort($param);
        return $result;
    }
    
    public function updateNoUrlId($param)
    {        
        $param['locale'] = \Application\Module::getConfig('general.default_locale');        
        $sql = new Sql(self::getDb());        
        $select = $sql->select()
            ->from(static::$tableName)
            ->columns(array(
                'website_id',
                'product_id',
            ))
            ->join(
                'product_locales',
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'name'                
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT 
            )
            ->join(
                'url_ids',
                static::$tableName . '.product_id = url_ids.product_id',
                array(
                    'url'                
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT 
            )       
            ->where(static::$tableName . ".website_id = ". $param['website_id'])
            ->where("url_ids.website_id = ". $param['website_id'])
            ->where("product_locales.locale = ". self::quote($param['locale']))
            ->where(new Expression(
                "(url_ids.url IS NULL OR url_ids.url = '')"
            ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        \Application\Lib\Log::info($selectString);
        $data = self::response(
            static::selectQuery($selectString), 
            self::RETURN_TYPE_ALL
        );
        $result = array();
        if (!empty($data)) {
            $urlIds = new UrlIds();
            foreach ($data as $row) {         
                $url = name_2_url($row['name']);
                $ok = $urlIds->addUpdateByProductId(array(
                    'url' => $url,
                    'product_id' => $row['product_id'],
                    'website_id' => $row['website_id']
                ));
                if ($ok) {
                    $result[] = array(
                        'product_id' => $row['product_id'],
                        'url' => $url,
                    );
                }
            }         
        }
        return $result;        
    }
    
    public function setPriority($param)
    {   
        $productDetail = self::find(
            array(            
                'where' => array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id']
                )
            ),
            self::RETURN_TYPE_ONE
        );
        if (empty($productDetail)) {
            self::errorNotExist('product_id');
            return false;
        }
        $maxPriority = self::max(
            array(
                'table' => 'products',
                'field' => 'priority',
                'where' => array(
                    'website_id' => $param['website_id'],                    
                )
            )            
        );
        $set = array(                       
            'priority' => $maxPriority + 1
        );
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id']
                )
            )
        )) {            
            return true;
        }
        return false;
    }
    
}
