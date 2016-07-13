<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;

class Products extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        '_id',
        'brand_id',
        'code',
        'code_src',
        'url_src',
        'model',
        'price',
        'price_src',
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
        'more',
        'meta_keyword',
        'meta_description',
        'created',
        'updated',
        'active',
        'image_id',
        'image_facebook',
        'sort',
        'priority',
        'default_color_id',
        'default_size_id',
        'discount_amount',
        'discount_percent',
    );
    
    protected static $primaryKey = 'product_id';
    
    protected static $tableName = 'products';
    
    public function getForHomepage($param)
    {
        $blockModel = new Blocks;
        $blockList = $blockModel->getAll(
            array(
                'website_id' => $param['website_id'],
                'sort' => 'sort ASC',
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
            'discount_percent',
            'discount_amount',
        );
        $select = $sql->select()
            ->from(static::$tableName) 
            ->join(
                'product_locales',
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
            ->where("product_locales.locale = ". self::quote($param['locale']))
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
        if (empty($param['keyword'])) {
            return array();
        }
        $param['keyword'] = strtolower($param['keyword']);
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
			'discount_percent',
            'discount_amount',
            'priority'
        );
        $select = $sql->select()
            ->from(static::$tableName) 
            ->columns($columns) 
            ->join(
               'product_locales',
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
            ->where(static::$tableName . '.website_id = '. self::quote($param['website_id']))            
            ->where(static::$tableName . '.active = 1')
            ->where('product_locales.locale = '. self::quote($param['locale']));
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
    
    public function getFeList($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $columns = array(                
            'product_id', 
            'code', 
            'code_src', 
            'model', 
            'brand_id', 
            '_id', 
            'price',
            'original_price',
            'discount_percent',
            'discount_amount',
            'priority',
        );
        $select = $sql->select()
            ->from(static::$tableName)   
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
        } elseif (!empty($param['option_value'])) {   
            if (is_array($param['option_value'])) {
                $param['option_value'] = implode(',', $param['option_value']);
            }
            $select->join(
                'product_has_fields', 
                static::$tableName . '.product_id = product_has_fields.product_id',
                array(
                    'field_id'                   
                )
            );
            $select->where(new Expression("LOCATE('[{$param['option_value']}]', product_has_fields.value_search) > 0"));
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
        
        if (!empty($param['category_id'])) {
            $categoryHasField = new ProductCategoryHasFields;
            $field = $categoryHasField->getAll(array(
                'website_id' => $param['website_id'],
                'category_id' => $param['category_id'],
                'allow_filter' => 1,
            ));
            $field = Arr::keyValues($field, 'field_id');     
        }      
        
        // get all brand_id, brand_name, product_id of a category
        $select2 = $sql->select()
            ->columns(array(                
                'product_id',            
                'brand_id', 
            ))
            ->from(static::$tableName) 
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
            );
        
        if (!empty($param['category_id'])) {
            $select2->join(
                    'product_has_categories', 
                    static::$tableName . '.product_id = product_has_categories.product_id',
                    array(
                        'category_id'
                    )
                )
                ->where(static::$tableName . ".website_id = ". self::quote($param['website_id']))            
                ->where(static::$tableName . '.active = 1')           
                ->where(new Expression(
                    "product_has_categories.category_id IN ({$param['category_id']})"
                ));
        }
        
        $columnData = static::column(
            $sql->getSqlStringForSqlObject($select2),
            'brand_id,brand_name,product_id'
        );
        // end: get all brand_id, brand_name, product_id of a category
        
        $brands = array();
        if (!empty($columnData['brand_id'])) {
            for ($i = 0; $i < count($columnData['brand_id']); $i++) {
                $brands[] = array(
                    'brand_id' => $columnData['brand_id'][$i],
                    'brand_name' => $columnData['brand_name'][$i]
                );
            }
        }
        if (!empty($columnData['product_id'])) {
            $hasField = new ProductHasFields;
            $fieldData = $hasField->getAll(array(
                'website_id' => $param['website_id'],
                'product_id' => $columnData['product_id']
            ));
            $attributes = array();
            if (!empty($fieldData)) { 
                foreach ($fieldData as $row) {
                    if (!isset($field[$row['field_id']])) {
                        //continue;
                    }
                    if (!isset($attributes[$row['field_id']])) {
                        $attributes[$row['field_id']] = array(
                            'name' => $row['name'],
                            'value' => array()
                        );
                    }
                    if (!empty($row['value'])) {
                        $arrayValue = explode(',', $row['value']);
                        foreach ($arrayValue as $value) {
                            $value = trim($value);
                            if (!in_array($value, $attributes[$row['field_id']]['value'])) {
                                $attributes[$row['field_id']]['value'][] = $value;
                            }
                        }      
                    }      
                    if (!empty($row['value_id'])) {
                        $arrayValueId = explode(',', $row['value_id']);
                        foreach ($arrayValueId as $valueId) {
                            $value = !empty($field[$row['field_id']]['options']['value_options'][$valueId]) 
                                    ? $field[$row['field_id']]['options']['value_options'][$valueId] : '';                       
                            if (!empty($value) && !in_array($value, $attributes[$row['field_id']]['value'])) {
                                $attributes[$row['field_id']]['value'][] = $value;
                            }                     
                        }
                    } 
                }
                $attributeFilter = array();
                foreach ($attributes as $fieldId => $attribute) {
                    if (count($attribute['value']) > 1) {
                        sort($attribute['value']);
                        $attributeFilter[$fieldId] = array(
                            'type' => $field[$fieldId]['type'],
                            'name' => $attribute['name'],
                            'value' => $attribute['value']
                        );
                    }
                } 
                $attributes = $attributeFilter;
            }
        }
        return array(
            'filter' => array(
                'brands' => $brands,
                'attributes' => $attributes,
            ),
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
                'discount_percent',
                'discount_amount',                
                'image_id'
            ))
            ->join(
               'product_locales',
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
            ->where("product_locales.locale = ". self::quote($param['locale']))
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
                'code', 
                'code_src', 
                'price',
                'original_price',
                'discount_percent',
                'discount_amount',
                'sort',
                'image_id',
                'priority',
                'website_id',
                'image_facebook',
            ))
            ->join(               
                'product_locales',                    
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
            ->where("product_locales.locale = ". self::quote($param['locale']));     
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
        if (!empty($param['is_duplicate_code'])) {            
            $select->join(
                array(
                    'product_duplicate_codes' => 
                    $sql->select()
                        ->columns(array(
                            'code', 
                            new Expression("COUNT(*)")
                        ))
                        ->from('products')                        
                        ->group('code')
                        ->having('COUNT(*) > 1')
                ),                   
                static::$tableName . '.code = product_duplicate_codes.code',
                array(
                    'duplicate_cnt' => 'Expression1'
                )
            );     
            $param['sort'] = 'code-asc';
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
        if (!empty($param['has_image_facebook'])) {    
            $select->where(new Expression(
                "IFNULL(image_facebook,'') <> ''"
            )); 
        }
        if (!empty($param['sort'])) {
            preg_match("/(code|name|price|created|priority)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("product_locales.{$match[1]} " . $match[2]);
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
        $select->order(static::$tableName . '.product_id ASC'); 
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);            
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );          
        return $result;
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
            $set = array();
            if (isset($param['code_src'])) {
                $set['code_src'] = $param['code_src'];
            }
            if (isset($param['price'])) {
                $set['price'] = $param['price'];
            }
            if (isset($param['price_src'])) {
                $set['price_src'] = $param['price_src'];
            }
            if (isset($param['original_price'])) {
                $set['original_price'] = $param['original_price'];
            }
            if (isset($param['discount_percent'])) {
                $set['discount_percent'] = $param['discount_percent'];
            }
            if (isset($param['default_size_id'])) {
                $set['default_size_id'] = $param['default_size_id'];
            }
            if (isset($param['default_color_id'])) {
                $set['default_color_id'] = $param['default_color_id'];
            }
            if (isset($param['url_src'])) {
                $set['url_src'] = $param['url_src'];
            }
            if (isset($param['priority'])) {
                $set['priority'] = $param['priority'];
            }
            if (!empty($set)) {
                self::update(array(
                    'table' => 'products',
                    'set' => $set,
                    'where' => array(
                        'product_id' => $id,
                        'website_id' => $param['website_id'],
                    ),            
                ));            
            }
//            if (!empty($param['category_id'])) {
//                $hasCategoryModel = new ProductHasCategories();
//                $hasCategoryModel->addUpdate(
//                    array(
//                        'product_id' => $id,
//                        'category_id' => $param['category_id']
//                    )
//                );
//            }
            if (isset($param['size_id'])) {
                if (!is_array($param['size_id'])) {
                    $param['size_id'] = unserialize($param['size_id']);
                }
                $hasSizeModel = new ProductHasSizes();
                $hasSizeModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'size_id' => $param['size_id']
                    )
                );
            }
            if (isset($param['color_id'])) {
                if (!is_array($param['color_id'])) {
                    $param['color_id'] = unserialize($param['color_id']);
                }
                $hasColorModel = new ProductHasColors();
                $hasColorModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'color_id' => $param['color_id']
                    )
                );
            }
            if (!empty($param['name'])) {
                $urlIds = new UrlIds();
                $urlIds->addUpdateByProductId(array(
                    'url' => name_2_url($param['name']),
                    'product_id' => $id,
                    'website_id' => $param['website_id']
                ));
            }
            
            if (isset($param['import_attributes'])) {
                if (!is_array($param['import_attributes'])) {
                    $param['import_attributes'] = unserialize($param['import_attributes']);
                }
                /*
                 * $param['import_attributes'] = array(
                 *      array(
                 *          name => ?,
                 *          value => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasFieldModel = new ProductHasFields;
                $hasFieldModel->import(array(
                    'attributes' => $param['import_attributes'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));        
            }
            
            if (isset($param['import_sizes'])) {  
                if (!is_array($param['import_sizes'])) {
                    $param['import_sizes'] = unserialize($param['import_sizes']);
                }
                /*
                 * $param['import_sizes'] = array(
                 *      array(
                 *          name => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasSizeModel = new ProductHasSizes;
                $hasSizeModel->import(array(
                    'sizes' => $param['import_sizes'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));            
            }
            
            if (isset($param['import_colors'])) {   
                if (!is_array($param['import_colors'])) {
                    $param['import_colors'] = unserialize($param['import_colors']);
                }
                /*
                 * $param['import_colors'] = array(
                 *      array(
                 *          name => ?,
                 *          url_image => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasColorModel = new ProductHasColors;
                $hasColorModel->import(array(
                    'colors' => $param['import_colors'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));            
            }
            
            if (isset($param['import_prices'])) {
                if (!is_array($param['import_prices'])) {
                    $param['import_prices'] = unserialize($param['import_prices']);
                }
                $priceModel = new ProductPrices();
                $priceModel->import(array(
                    'prices' => $param['import_prices'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));
            }
            
            return $detail['_id'];
        }
        $_id = mongo_id();  // products._id        
        $values = array(
            '_id' => $_id,         
            'website_id' => $param['website_id'],
        );
        if (isset($param['sort'])) {
            $values['sort'] = $param['sort'];
        }  
        if (isset($param['price_src'])) {
            $values['price_src'] = Util::toPrice($param['price_src']);
        }  
        if (isset($param['price'])) {
            $values['price'] = Util::toPrice($param['price']);
        }  
        if (isset($param['original_price'])) {
            $values['original_price'] = Util::toPrice($param['original_price']);
        }  
        if (isset($param['discount_percent'])) {
            $values['discount_percent'] = $param['discount_percent'];
        }  
        if (isset($param['discount_amount'])) {
            $values['discount_amount'] = $param['discount_amount'];
        }  
        if (isset($param['default_size_id'])) {
            $values['default_size_id'] = $param['default_size_id'];
        }
        if (isset($param['default_color_id'])) {
            $values['default_color_id'] = $param['default_color_id'];
        }
        if (isset($param['code'])) {
            $values['code'] = $param['code'];
        }  
        if (isset($param['code_src'])) {
            $values['code_src'] = $param['code_src'];
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
        if (isset($param['url_src'])) {
            $values['url_src'] = $param['url_src'];
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
        if (isset($param['priority']) && is_numeric($param['priority'])) {
            $values['priority'] = $param['priority'];
        }     
        $imagesModel = new Images();        
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $mainImageUrl = $uploadResult['url_image'];                
            }         
            if (!empty($uploadResult['image_facebook'])) {                    
                $param['image_facebook'] = $uploadResult['image_facebook'];
            }
        } elseif (!empty($param['url_image'])) {   
            $mainImageUrl = Util::uploadImageFromUrl($param['url_image'], 600, 600, $param['name']);            
        }
        if (!empty($mainImageUrl)) {
            $values['image_id'] = $imagesModel->add(array(
                'src' => 'products',
                'src_id' => 0,
                'url_image' => $mainImageUrl,
                'url_image_source' => !empty($param['url_image']) ? $param['url_image'] : '',
                'is_main' => 1,
            ));
            if (isset($param['add_image_to_content'])) {
                $param['content'] .= "<center><p><img src=\"{$mainImageUrl}\"/></p></center>";
            }
        }
        if (isset($param['image_facebook'])) {
            $values['image_facebook'] = $param['image_facebook'];
        } 
        // for batch
        if (isset($param['brand_name'])) {
            $brandModel = new Brands;
            $brandModel->add(
                array(
                    'name' => $param['brand_name'],              
                    'website_id' => $param['website_id'],
                ),
                $values['brand_id']    
            );           
        }
            
        if ($id = self::insert($values, 'products')) { 
			
            $hasCategoryModel = new ProductHasCategories();
            $hasCategoryModel->addUpdate(
                array(
                    'product_id' => $id,
                    'category_id' => $param['category_id']
                )
            );                
            
            if (!empty($param['name'])) {
                $urlIds = new UrlIds();
                $urlIds->addUpdateByProductId(array(
                    'url' => name_2_url($param['name']),
                    'product_id' => $id,
                    'website_id' => $param['website_id']
                ));
            }
                        
            if (isset($param['size_id'])) {
                if (!is_array($param['size_id'])) {
                    $param['size_id'] = unserialize($param['size_id']);
                }
                $hasSizeModel = new ProductHasSizes();
                $hasSizeModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'size_id' => $param['size_id']
                    )
                );
            }   
            
            if (isset($param['color_id'])) {
                if (!is_array($param['color_id'])) {
                    $param['color_id'] = unserialize($param['color_id']);
                }
                $hasColorModel = new ProductHasColors();
                $hasColorModel->addUpdate(
                    array(
                        'product_id' => $id,
                        'color_id' => $param['color_id']
                    )
                );
            }    
            
            if (isset($param['import_attributes'])) {
                if (!is_array($param['import_attributes'])) {
                    $param['import_attributes'] = unserialize($param['import_attributes']);
                }
                /*
                 * $param['import_attributes'] = array(
                 *      array(
                 *          name => ?,
                 *          value => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasFieldModel = new ProductHasFields;
                $hasFieldModel->import(array(
                    'attributes' => $param['import_attributes'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));           
            }
            
            if (!empty($values['image_id'])) {                
                $imagesModel->updateInfo(array(
                    'src' => 'products',
                    'src_id' => $id,
                    'id' => $values['image_id']
                ));
            }
            
            if (isset($param['images'])) {
                if (!is_array($param['images'])) {
                    $param['images'] = unserialize($param['images']);
                }               
                Log::error(implode(PHP_EOL, $param['images']));
                foreach ($param['images'] as $sourceImageUrl) {
                    if ($param['url_image'] != $sourceImageUrl) {
                        $imageUrl = Util::uploadImageFromUrl($sourceImageUrl, 600, 600, $param['name']);
                        if (!empty($imageUrl) && $imagesModel->add(array(
                            'src' => 'products',
                            'src_id' => $id,
                            'url_image' => $imageUrl,
                            'url_image_source' => $sourceImageUrl,
                            'is_main' => 0,
                        ))) {
                            if (isset($param['add_image_to_content'])) {
                                $param['content'] .= "<center><p><img src=\"{$imageUrl}\"/></p></center>";                                
                            }
                        } else {
                             Log::error('Error: ' . $imageUrl);
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
            if (isset($param['more'])) {
                $localeValues['more'] = $param['more'];
            }         
            if (isset($param['meta_keyword'])) {
                $localeValues['meta_keyword'] = mb_strtolower($param['meta_keyword']);
            }        
            if (isset($param['meta_description'])) {
                $localeValues['meta_description'] = $param['meta_description'];
            }
            Log::error($localeValues);
            self::insert($localeValues, 'product_locales'); 
            
            if (isset($param['import_sizes'])) {  
                if (!is_array($param['import_sizes'])) {
                    $param['import_sizes'] = unserialize($param['import_sizes']);
                }
                /*
                 * $param['import_sizes'] = array(
                 *      array(
                 *          name => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasSizeModel = new ProductHasSizes;
                $hasSizeModel->import(array(
                    'sizes' => $param['import_sizes'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));            
            }
            
            if (isset($param['import_colors'])) {   
                if (!is_array($param['import_colors'])) {
                    $param['import_colors'] = unserialize($param['import_colors']);
                }
                /*
                 * $param['import_colors'] = array(
                 *      array(
                 *          name => ?,
                 *          url_image => ?,
                 *      )
                 *      ...
                 * )
                 */
                $hasColorModel = new ProductHasColors;
                $hasColorModel->import(
                    array(
                        'colors' => $param['import_colors'],
                        'product_id' => $id,
                        'website_id' => $param['website_id'],
                    ), $param['name']
                );            
            }
            
            if (isset($param['import_prices'])) {
                if (!is_array($param['import_prices'])) {
                    $param['import_prices'] = unserialize($param['import_prices']);
                }
                $priceModel = new ProductPrices();
                $priceModel->import(array(
                    'prices' => $param['import_prices'],
                    'product_id' => $id,
                    'website_id' => $param['website_id'],
                ));
            }
            
            return $_id;
        }        
        return false;
    }

    public function updateInfo($param)
    {
        $where = array();
        if (!empty($param['_id'])) {
            $where['_id'] = $param['_id'];
        }
        if (!empty($param['product_id'])) {
            $where['product_id'] = $param['product_id'];
        }
        $self = self::find(
            array(            
                'table' => 'products',
                'where' => $where
            ),
            self::RETURN_TYPE_ONE
        );   
        if (empty($self)) {
            self::errorNotExist('product_id_or_id');
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
        if (isset($param['code_src'])) {
            $set['code_src'] = $param['code_src'];
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
            if (!empty($uploadResult['image_facebook'])) {                    
                $param['image_facebook'] = $uploadResult['image_facebook'];
            }
        } else {   
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'products'
                ));
            }
        }
        if (isset($param['image_facebook'])) {
            $set['image_facebook'] = $param['image_facebook'];
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }
        if (self::update(
            array(
                'table' => 'products',
                'set' => $set,
                'where' => array(
                    'product_id' => $self['product_id']
                ),
            )
        )) {
            $locales = \Application\Module::getConfig('general.locales');
            if (count($locales) == 1) {
                $param['locale'] = array_keys($locales)[0];
                $param['_id'] = $self['_id'];
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
            /*
            $hasSizeModel = new ProductHasSizes();
            $hasSizeModel->addUpdate(
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
             * 
             */
            return true;
        } 
        return false;
    }

    public function addUpdateLocale($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }       
        $detail = self::getDetail(array(
            '_id' => $param['_id'],
            'locale' => $param['locale'], 
            'website_id' => $param['website_id']
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
                'table' => 'product_locales',
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

    public function updateLocale($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $detail = self::getDetail(array(
            'code' => !empty($param['code']) ? $param['code'] : null,
            'product_id' => !empty($param['product_id']) ? $param['product_id'] : null,
            'locale' => $param['locale'], 
            'website_id' => $param['website_id']
        ));        
        if (empty($detail)) {
            self::errorNotExist('product_id');
            return false;
        }
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
        if (isset($param['more'])) {
            $values['more'] = $param['more'];
        }
        if (isset($param['meta_keyword'])) {
            $values['meta_keyword'] = mb_strtolower($param['meta_keyword']);
        }
        if (isset($param['meta_description'])) {
            $values['meta_description'] = $param['meta_description'];
        }
        if (isset($param['add_image_to_content']) 
            && !empty($param['website_id'])
            && !empty($detail['product_id'])) {
            $imageModel = new Images;
            $images = $imageModel->getForBatch(array(
                'product_images_only' => 1,
                'website_id' => $param['website_id'],
                'product_id' => $detail['product_id'],
            ));          
            if (!empty($images)) {
                if (empty($values['content'])) {
                    $values['content'] = $detail['content'];
                }
                $values['content'] = strip_tags($values['content'], '<p><div><span><ul><li><strong><b><br><center>');
                $values['content'] = str_replace(PHP_EOL, '<br/>', $values['content']);
                foreach ($images as $image) { 
                    $values['content'] .= "<center><p><img src=\"{$image['url_image']}\"/></p></center>";
                }
            }          
        }
        $ok = false;
        if (!empty($values)) {
            $ok = self::update(
                array(
                    'table' => 'product_locales',
                    'set' => $values,
                    'where' => array(
                        'product_id' => $detail['product_id'],
                        'locale' => $param['locale'],
                    ),
                )
            );
        }
        if ($ok && !empty($param['name']) && $param['name'] != $detail['name']) {
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
            ->from('products')  
            ->columns(array(                
                'product_id', 
                '_id', 
                'brand_id',
                'code',
                'code_src',
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
                'made_in',                
                'active',
                'image_id',
                'image_facebook',
                'default_color_id',
                'default_size_id',
                'discount_percent',
                'discount_amount',
            ))
            ->join(               
                'product_locales',                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                    'more',
                    'meta_keyword',
                    'meta_description',
                )  
            )
            ->where("product_locales.locale = ". self::quote($param['locale']));                      
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['product_id'])) {            
            $select->where(static::$tableName . '.product_id = '. self::quote($param['product_id']));  
        }
        if (!empty($param['code'])) {            
            $select->where(static::$tableName . '.code = '. self::quote($param['code']));  
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
            
            $hasCategoryModel = new ProductHasCategories();
            $result['categories'] = $hasCategoryModel->getAll(
                array( 
                    'product_id' => $result['product_id']
                )
            );           
            $result['category_id'] = Arr::field(
                $result['categories'],
                'category_id'
            ); 
            
            $hasSizeModel = new ProductHasSizes();
            $result['sizes'] = $hasSizeModel->getAll(array(
                'product_id' => $result['product_id']
            ));
            $result['size_id'] = Arr::field(
                $result['sizes'],
                'size_id'
            ); 
            
            $hasColorModel = new ProductHasColors();
            $result['colors'] = $hasColorModel->getAll(array(
                'product_id' => $result['product_id']
            ));
            $result['color_id'] = Arr::field(
                $result['colors'],
                'color_id'
            );
            
            $categoryHasFieldModel = new ProductCategoryHasFields();
            $result['attributes'] = $categoryHasFieldModel->getAll(array(
                'website_id' => $result['website_id'],
                'category_id' => $result['category_id'],
                'locale' => $param['locale'],
            ));
            
            $hasFieldModel = new ProductHasFields;
            $values = $hasFieldModel->getAll(array(
                'product_id' => $result['product_id']                
            ));
         
            $optionId = array();
            foreach ($result['attributes'] as &$attribute) {
                foreach ($values as $value) {
                    if ($attribute['field_id'] == $value['field_id']) { 
                        switch ($attribute['type']) {
                            case 'select':                        
                            case 'radio':  
                                if (isset($attribute['options']['value_options'][$value['value_id']])) {
                                    $attribute['value'] = $attribute['options']['value_options'][$value['value_id']];
                                }
                                break;
                            case 'checkbox':
                                $valueId = explode(',', $value['value_id']);
                                $valueText = array();
                                foreach ($valueId as $optId) {
                                    if (isset($attribute['options']['value_options'][$optId])) {
                                        $valueText[] = $attribute['options']['value_options'][$optId];
                                    }
                                }
                                $attribute['value'] = implode(', ', $valueText);
                                break;
                            default:
                                $attribute['value'] = $value['value'];
                        }
                        $attribute['value_id'] = $value['value_id'];
                        /*
                        if (!empty($value['value'])) {
                            $attribute['value'] = $value['value'];
                        } elseif (!empty($value['value_id'])) {
                            if (isset($attribute['options']['value_options'][$value['value_id']])) {
                                $attribute['value'] = $attribute['options']['value_options'][$value['value_id']];
                            }                           
                            $attribute['value_id'] = $value['value_id'];
                        }  
                        * 
                        */                      
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
            
            if (isset($param['get_prices'])) {
                $result['prices'] = $this->getAllPrice(array(
                    'product_id' => $result['product_id'],
                    'website_id' => $result['website_id'],
                ));
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
                'product_locales',                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                    'content',
                    'meta_keyword',
                    'meta_description',
                )   
            )
            ->where("product_locales.locale = ". self::quote($param['locale']));                      
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
    
    public function setPriorityAfterImported($param)
    {   
        $productList = $this->getAll(array(
            'website_id' => $param['website_id'],
            'category_id' => $param['category_id'],
        ));
        if (!empty($productList)) {
            foreach ($productList as $product) {
                $ok = $this->setPriority(array(
                    'website_id' => $param['website_id'],
                    'product_id' => $product['product_id']
                ));
                if (!$ok) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function getPrice($param)
    {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('product_prices')  
            ->columns(array(                
                'website_id', 
                'product_id', 
                'color_id', 
                'size_id',
                'price',
            ));
        if (!empty($param['website_id'])) {            
            $select->where('website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['product_id'])) {            
            $select->where('product_id = '. self::quote($param['product_id']));  
        }
        if (!empty($param['color_id'])) {            
            $select->where('color_id = '. self::quote($param['color_id']));  
        }
        if (!empty($param['size_id'])) {            
            $select->where('size_id = '. self::quote($param['size_id']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if (!empty($result)) {
            $product = self::find(
                array(            
                    'where' => array(
                        'website_id' => $param['website_id'],
                        'product_id' => $param['product_id']
                    )
                ),
                self::RETURN_TYPE_ONE
            );
            $result['original_price'] = $result['price'];
            if (!empty($product['discount_percent'])) { 
                $result['price'] = $result['original_price'] - ($result['original_price'] * $product['discount_percent'] / 100);
            } elseif (!empty($product['discount_amount'])) {
                $result['price'] = $result['original_price'] + $product['discount_amount'];
            }
            $result['price'] = round($result['price'], -3);
        }
        return $result;
    }
    
    public function getAllPrice($param)
    {        
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('product_prices')  
            ->columns(array(                
                'website_id', 
                'product_id', 
                'color_id', 
                'size_id',
                'price',
                'active',
            ))
            ->join(  
                array(
                    'product_color_locales' => 
                    $sql->select()                       
                        ->from('product_color_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),                       
                'product_prices.color_id = product_color_locales.color_id',
                array(
                    'color_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(  
                array(
                    'product_size_locales' => 
                    $sql->select()                       
                        ->from('product_size_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),             
                'product_prices.size_id = product_size_locales.size_id',
                array(
                    'size_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );
        if (!empty($param['website_id'])) {            
            $select->where('product_prices.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['product_id'])) {            
            $select->where('product_prices.product_id = '. self::quote($param['product_id']));  
        }        
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        if (!empty($result)) {
            foreach ($result as &$item) {
                $item['id'] = $item['product_id'] . '_' . $item['color_id'] . '_' . $item['size_id'];
            }
            unset($item);
        }
        return $result;
    }
    
    public function addPrice($param)
    { 
        if (empty($param['website_id'])             
            || empty($param['product_id'])) {
            return false;
        }
        $productDetail = self::find(
            array(            
                'where' => array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id']
                )
            ),
            self::RETURN_TYPE_ONE
        );
        $priceModel = new ProductPrices;
        $param['price'] = $productDetail['price'];
        $values = array();    
        if (!empty($param['color_id']) && !empty($param['size_id'])) {
            foreach ($param['color_id'] as $colorId) { 
                if (!empty($param['size_id'])) {
                    foreach ($param['size_id'] as $sizeId) {
                        $values[] = array(
                            'website_id' => $param['website_id'],
                            'product_id' => $param['product_id'],                            
                            'color_id' => $colorId,
                            'size_id' => $sizeId,
                            'price' => $param['price'],
                            'created' => new Expression('UNIX_TIMESTAMP()'),
                            'updated' => new Expression('UNIX_TIMESTAMP()'),
                        );
                    }
                }
            }
        } elseif (!empty($param['color_id'])) {
            foreach ($param['color_id'] as $colorId) { 
                $values[] = array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id'],                    
                    'color_id' => $colorId,
                    'size_id' => 0,
                    'price' => $param['price'],
                    'created' => new Expression('UNIX_TIMESTAMP()'),
                    'updated' => new Expression('UNIX_TIMESTAMP()'),
                );
            }
        } elseif (!empty($param['size_id'])) {
            foreach ($param['size_id'] as $sizeId) { 
                $values[] = array(
                    'website_id' => $param['website_id'],
                    'product_id' => $param['product_id'],
                    'color_id' => 0,
                    'size_id' => $sizeId,
                    'price' => $param['price'],
                    'created' => new Expression('UNIX_TIMESTAMP()'),
                    'updated' => new Expression('UNIX_TIMESTAMP()'),
                );
            }
        } else {
            $priceModel->delete(array(
                'where' => new Expression(
                    "product_id = {$param['product_id']}"
                )
            )); 
        }
       
        $hasColorModel = new ProductHasColors;
        $hasColorModel->addUpdate(array(
            'website_id' => $param['website_id'],
            'product_id' => $param['product_id'],
            'color_id' => $param['color_id'],
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        ));                  
        if (!empty($param['color_id'])) {
            $priceModel->delete(array(
                'where' => new Expression(
                    "product_id = {$param['product_id']} AND color_id NOT IN (" . implode(',', $param['color_id']) . ")"
                )
            ));       
        } else {
            $priceModel->delete(array(
                'where' => new Expression(
                    "product_id = {$param['product_id']} AND color_id > 0"
                )
            ));
        }
       
        $hasSizeModel = new ProductHasSizes;
        $hasSizeModel->addUpdate(array(
            'website_id' => $param['website_id'],
            'product_id' => $param['product_id'],
            'size_id' => $param['size_id'],
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        )); 
        if (!empty($param['size_id'])) {
            $priceModel->delete(array(
                'where' => new Expression(
                    "product_id = {$param['product_id']} AND size_id NOT IN (" . implode(',', $param['size_id']) . ")"
                )
            ));       
        } else {
            $priceModel->delete(array(
                'where' => new Expression(
                    "product_id = {$param['product_id']} AND size_id > 0"
                )
            ));
        }
        
        if (!empty($values) && $priceModel->batchInsert($values)) {
            $this->updateDefaultColorIdAndSizeId($productDetail['product_id'], $productDetail['price']);
            return true;
        } elseif (!empty($values)) {
            $this->updateDefaultColorIdAndSizeId($productDetail['product_id'], $productDetail['price']);
        }
        return false;
    }
    
    public function updateDefaultColorIdAndSizeId($productId, $price = 0)
    {
        if (empty($price)) {
            $productDetail = self::find(
                array(     
                    'table' => 'products',
                    'where' => array(                       
                        'product_id' => $productId
                    )
                ),
                self::RETURN_TYPE_ONE
            );
            $price = !empty($productDetail['original_price']) ? $productDetail['original_price'] : 0;
        }
        if (empty($price)) {
            return false;
        }
        $priceModel = new ProductPrices;
        $prices = $priceModel->find(array(
            'where' => array(
                'product_id' => $productId
            )
        ));
        if (!empty($prices)) {
            foreach ($prices as $value) {
                if ($value['price'] == $price) {
                    $this->update(array(
                        'set' => array(
                            'default_color_id' => $value['color_id'],
                            'default_size_id' => $value['size_id'],
                        ),
                        'where' => array(
                            'product_id' => $value['product_id'],
                        )
                    ));
                    break;
                }
            }
        } else {
            $this->update(array(
                'set' => array(
                    'default_color_id' => 0,
                    'default_size_id' => 0,
                 ),
                 'where' => array(
                      'product_id' => $productId,   
                 )
            ));
        }
        return true;  
    } 
    
    public function savePrice($param)
    {
        $priceModel = new ProductPrices;
        return $priceModel->savePrice($param);   
    }             
    
    /**
     * ON/OFF price
     *     
     * @param array $param field, value and _id 
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function updateOnOffPrice($param) {
        $priceModel = new ProductPrices;
        $id = $param['_id'];
        list($productId, $colorId, $sizeId) = explode('_', $id);
        if (!$priceModel->update(array(
            'set' => array('active' => $param['value']),
            'where' => array(
                'product_id' => $productId,
                'color_id' => $colorId,
                'size_id' => $sizeId,
            ),
        ))) {
            return false;
        }  
        return true;
    }
    
    public static function updateCode($param) {
        
       
        
    }

    public static function updateFbImage($param) {          
        $products = \Zend\Json\Decoder::decode($param['products'], \Zend\Json\Json::TYPE_ARRAY);            
        $config = \Application\Module::getConfig('upload.image'); 
        $result = array();
        foreach ($products as $i => $product) {           
            try {
                if (!empty($product['image_facebook'])) {
                    $oldImage = str_replace($config['url'], $config['path'], $product['image_facebook']); 
                    if (is_file($oldImage) && file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                $product['image_facebook'] = Util::uploadImageFromUrl($product['url_image'], 300, 300, $product['name']);
                if (!empty($product['image_facebook'])) {
                    $ok = self::update([
                        'table' => 'products',
                        'set' => [
                            'image_facebook' => $product['image_facebook']
                        ],
                        'where' => [
                            'website_id' => $product['website_id'],
                            'product_id' => $product['product_id']
                        ]
                    ]);           
                }
                $result[$product['product_id']] = !empty($ok) ? $product['image_facebook'] : 'Fail';  
            } catch (\Exception $e) {
                \Application\Lib\Log::info("{$i} - {$product['product_id']}: {$e->getMessage()}");
            }    
        }
        return $result;
    }
    
    public static function updatePrice($param) {    
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $products = \Zend\Json\Decoder::decode($param['products'], \Zend\Json\Json::TYPE_ARRAY);            
        $result = array();
        if (!empty($products)) {
            foreach ($products as $product) {                
                $set = [
                    'url_src' => $product['url_src'],
                    'price_src' => $product['price_src'],
                    'discount_percent' => $product['discount_percent'],
                    'original_price' => $product['original_price'],
                    'price' => $product['price']
                ]; 
                
                $detail = self::find(
                    array(            
                        'table' => 'products',
                        'where' => [                            
                            'code' => $product['code']
                        ]
                    ),
                    self::RETURN_TYPE_ONE
                );                 
                if (empty($detail)) {
                    Log::warning("CODE {$product['code']} does not exists");
                    continue;
                }
                $ok = self::update([
                    'table' => 'products',
                    'set' => $set,
                    'where' => [
                        'website_id' => $detail['website_id'],
                        'product_id' => $detail['product_id'],
                    ]
                ]);
                if ($ok && isset($product['more'])) {
                    $ok = self::update([
                        'table' => 'product_locales',
                        'set' => [
                            'more' => $product['more']
                        ],
                        'where' => [                           
                            'product_id' => $detail['product_id'],
                            'locale' => $param['locale']
                        ]
                    ]);
                }
                $result[$product['code']] = !empty($ok) ? 'OK' : 'Fail';  
            }
        }
        return $result;
    }
    
    public static function deleteProduct($param) {  
        $imageModel = new Images;
        $imageList = $imageModel->getAll([
            'src' => 'products',
            'src_id' => $param['product_id'],
        ]);
        $tables = array(
            'products',
            'product_locales',
            'product_has_colors',
            'product_has_sizes',
            'product_has_fields',
            'product_has_categories',
            'product_images',
            'url_ids',
        );     
        $db = self::getDb();
        $sql = new Sql($db); 
        //$db->getDriver()->getConnection()->beginTransaction();
        foreach ($tables as $table) {
            if ($table == 'product_images') {
                $where = [
                    'website_id' => $param['website_id'],
                    'src_id' => $param['product_id'],
                ];
            } else {
                $where = [
                    'product_id' => $param['product_id'],
                ];
                if ($table == 'products') {
                    $where['website_id'] = $param['website_id'];
                }
            }
            $delete = $sql->delete()
                ->from($table)                
                ->where($where);    
            //Log::info($sql->getSqlStringForSqlObject($delete));
            $ok = self::getDb()->query($sql->getSqlStringForSqlObject($delete), Adapter::QUERY_MODE_EXECUTE);            
            //Log::info(json_encode($ok)); 
            if (!$ok) {
                //$db->getDriver()->getConnection()->rollback();
                //return $ok; 
            }
        }
        //$db->getDriver()->getConnection()->commit();
        if ($ok) {
            //$db->getDriver()->getConnection()->commit();
            if (!empty($imageList)) {
                foreach ($imageList as $image) {
                    if (!empty($image['url_image'])) {                        
                        $filename = str_replace(\Application\Module::getConfig('upload.image.url'), \Application\Module::getConfig('upload.image.path'), $image['url_image']);   
                        if (is_file($filename) && file_exists($filename)) {
                            unlink($filename);
                        }
                    }
                }
            }
        }
        return $ok;
    }
    
    public function getForBlogger($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        if (empty($param['limit'])) {
            $param['limit'] = 30;
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'product_id',                
                'code', 
                'code_src', 
                'price',
                'original_price',
                'discount_percent',
                'discount_amount',
                'sort',
                'image_id',
                'priority',
                'website_id'
            ))
            ->join(               
                'product_locales',                    
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
            ->where("product_locales.locale = ". self::quote($param['locale']))
            ->where(new Expression(
                "NOT EXISTS (
                    SELECT * 
                    FROM blogger_post_ids 
                    WHERE
                        blog_id = " . self::quote($param['blog_id']) . " 
                        AND blogger_post_ids.product_id = products.product_id)"
            ));
        
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
       
        $select->order(static::$tableName . '.product_id DESC');
        $select->limit($param['limit']);
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );          
        return $result;
    }
    
}
