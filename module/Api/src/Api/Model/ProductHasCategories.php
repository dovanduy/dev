<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;

class ProductHasCategories extends AbstractModel {
    
    protected static $properties = array(
        'product_id',        
        'category_id',
        'created',
        'updated',
    );
    
    protected static $primaryKey = array('product_id', 'category_id');
    
    protected static $tableName = 'product_has_categories';
    
    public function getAll($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'category_id', 
                'product_id'
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
                )  
            );             
        if (!empty($param['product_id'])) {            
            $select->where(static::$tableName . '.product_id = '. self::quote($param['product_id']));  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }  
    
    public function addUpdate($param)
    {        
        if (!is_array($param['category_id'])) {
            $param['category_id'] = array($param['category_id']);
        }
        $categories = self::find(
            array(     
                'where' => array(
                    'product_id' => $param['product_id']
                )
            )
        );
        $categoryValues = array();                     
        foreach ($param['category_id'] as $categoryId) {                
            $categoryValues[] = array(
                'product_id' => $param['product_id'],
                'category_id' => $categoryId,
                'created' => new Expression('UNIX_TIMESTAMP()'),
                'updated' => new Expression('UNIX_TIMESTAMP()'),
            );
            if (!self::batchInsert($categoryValues, array('updated' => new Expression('UNIX_TIMESTAMP()')))) {
                return false;
            }
        }           
        if (!empty($categories)) {
            foreach ($categories as $category) {                
                if (!in_array($category['category_id'], $param['category_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'product_id' => $param['product_id'],
                                'category_id' => $category['category_id']
                            ),
                        )
                    )) {
                        return false;
                    }
                }
            }
        }
        return true;        
    }
    
    public function getAllBrands($param) {
        if (empty($param['category_id'])) {
            return array();
        }
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()            
            ->from(static::$tableName)  
            ->columns(array(                
                'category_id', 
                'product_id'               
            ))            
            ->join(
                'products', 
                static::$tableName . '.product_id = products.product_id'
            )
            ->join(
                'brands', 
                'products.brand_id = brands.brand_id',
                array('brand_id'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'brands.brand_id = brand_locales.brand_id',
                array(
                    'brand_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where('products.active = 1')     
            ->where('brands.active = 1')     
            ->order('brand_locales.name')
            ->where(new Expression(static::$tableName . '.category_id IN ('. $param['category_id'] . ')')); 
        $rows = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        $result = array();
        if (!empty($rows)) {
            $brandId = array();
            foreach ($rows as $row) {
                if (isset($brandId[$row['brand_id']])) {
                    $brandId[$row['brand_id']]++;
                } else {
                    $result[] = array(
                        'brand_id' => $row['brand_id'],
                        'brand_name' => $row['brand_name'],
                    );
                    $brandId[$row['brand_id']] = 0;
                }                
            }      
            foreach ($result as &$row) {
                $row['count_product'] = !empty($brandId[$row['brand_id']]) ? $brandId[$row['brand_id']] : 0; 
            }
            unset($row);                
        }
        return $result;
    } 
    
    public function filter($param) {
        if (empty($param['category_id']) && empty($param['brand_id'])) {
            return array();
        }
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $categoryModel = new ProductCategories();
        $categories = $categoryModel->getAll(array(
            'website_id' => $param['website_id'],
            'active' => 1
        ));
        $sql = new Sql(self::getDb());
        $select = $sql->select()            
            ->from(static::$tableName)  
            ->columns(array(                
                'category_id', 
                'product_id'               
            ))   
            ->join(               
                array(
                    'product_category_locales' => 
                    $sql->select()
                        ->from('product_category_locales')
                        ->join(
                            'product_categories',
                            'product_categories.category_id = product_category_locales.category_id',
                            array(
                                'parent_id', 
                                'path_id', 
                                'sort'
                            )
                        )
                        ->where("locale = ". self::quote($param['locale']))
                ),                   
                static::$tableName . '.category_id = product_category_locales.category_id',
                array(
                    'category_parent_id' => 'parent_id',
                    'category_sort' => 'sort',
                    'category_name' => 'name',
                    'path_id' => 'path_id'
                )
            )
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
            ->join(               
                array(
                    'brand_locales' => 
                    $sql->select()
                        ->from('brand_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'brands.brand_id = brand_locales.brand_id',
                array(
                    'brand_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where('products.website_id = ' . $param['website_id'])
            ->where('products.active = 1')            
            ->order('brand_locales.name')
            ->order('product_category_locales.parent_id')
            ->order('product_category_locales.sort');
        $select->where(new Expression('(brands.active = 1 OR ISNULL(brands.active))')); 
        if (!empty($param['category_id'])) {
            $select->where(new Expression(static::$tableName . '.category_id IN ('. $param['category_id'] . ')')); 
        }
        if (!empty($param['brand_id'])) {
            $select->where(new Expression('products.brand_id IN ('. $param['brand_id'] . ')')); 
        }
        $rows = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        $result = array(
            'category' => array(),
            'brand' => array(),
            'price' => array('min' => 0, 'max' => 0),            
        );      
        if (!empty($rows)) {
            $brandId = $categoryId = array();
            foreach ($rows as $row) {                                   
                if (isset($categoryId[$row['category_id']])) {                    
                    if (!in_array($row['product_id'], $categoryId[$row['category_id']])) {
                        $categoryId[$row['category_id']][] = $row['product_id'];
                    }
                } else {
                    $result['category'][] = array(
                        'category_id' => $row['category_id'],
                        'category_name' => $row['category_name'],
                        'category_parent_id' => $row['category_parent_id'],
                        'path_id' => \Zend\Json\Decoder::decode($row['path_id']),
                    );
                    $categoryId[$row['category_id']] = array($row['product_id']);
                }
                
                if (isset($brandId[$row['brand_id']])) {                    
                    if (!in_array($row['product_id'], $brandId[$row['brand_id']])) {
                        $brandId[$row['brand_id']][] = $row['product_id'];
                    }
                } elseif (!empty($row['brand_id'])) {                    
                    $result['brand'][] = array(
                        'brand_id' => $row['brand_id'],
                        'brand_name' => $row['brand_name'],
                    );
                    $brandId[$row['brand_id']] = array($row['product_id']);
                }   
                
                if ($result['price']['min'] > $row['price']) {
                    $result['price']['min'] = $row['price'];
                }
                if ($result['price']['max'] < $row['price']) {
                    $result['price']['max'] = $row['price'];
                }
            }     
            foreach ($result['brand'] as &$row) {
                $row['count_product'] = !empty($brandId[$row['brand_id']]) ? count($brandId[$row['brand_id']]) : 0; 
            }
            foreach ($result['category'] as &$row) {
                $row['count_product'] = !empty($categoryId[$row['category_id']]) ? count($categoryId[$row['category_id']]) : 0; 
            }
            unset($row); 
            if (!empty($param['brand_id'])) {
                $secondResult = $this->filter(array(
                        'website_id' => $param['website_id'], 
                        'brand_id' => 0, 
                        'category_id' => implode(',', array_keys($categoryId))
                    )
                );               
                $result['brand'] = array_replace_recursive($result['brand'], $secondResult['brand']);
            }    
            if (!empty($categoryId)) {
                $productCategoryHasFieldsModel = new ProductCategoryHasFields();
                $result['fields'] = $productCategoryHasFieldsModel->getAll(array(
                    'category_id' => array_keys($categoryId),
                    'locale' => $param['locale'],
                ));
            }
            if (!empty($param['category_id'])) {
                if (count($result['category']) == 1 && $result['category'][0]['category_id'] == $param['category_id']) {               
                    $productCategoryModel = new ProductCategories();
                    $categores = $productCategoryModel->getAll(array(
                        'website_id' => $param['website_id'],
                        'parent_id' => $result['category'][0]['category_parent_id']
                    ));
                }
                if (!empty($categores)) {
                    $result['category'] = array();
                    foreach ($categores as $category) {                        
                        $result['category'][] = array(
                            'category_id' => $category['category_id'],
                            'category_name' => $category['name'],
                            'category_parent_id' => $category['parent_id'],
                            'path_id' => $category['path_id']
                        );                        
                    }
                }
            }
        } elseif (!empty($param['category_id'])) {
            $categoryDetail = Arr::filter($categories, 'category_id', $param['category_id'], 0, false);
            $categoryDetail = $categoryDetail[0]; 
            foreach ($categories as $category) {
                if ($categoryDetail['parent_id'] == $category['parent_id']) {            
                    $result['category'][] = array(
                        'category_id' => $category['category_id'],
                        'category_name' => $category['name'],
                        'category_parent_id' => $category['parent_id'],
                        'path_id' => $category['path_id']
                    );   
                }
            }
        }
        return $result;
    } 
    
    public function addProduct($param)
    {        
        $values = array(
            'category_id' => $param['category_id'],
            'product_id' => $param['product_id'],
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        );        
        if (!self::batchInsert($values, array('updated' => new Expression('UNIX_TIMESTAMP()')))) {
            return false;
        }
        return true;     
    }
    
    public function removeProduct($param)
    {     
        if (!self::delete(
            array(
                'where' => array(
                    'product_id' => $param['product_id'],
                    'category_id' => $param['category_id']
                ),
            )
        )) {
            return false;
        }          
        return true;              
    }
    
}
