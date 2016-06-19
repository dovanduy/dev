<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;

class ShareUrls extends AbstractModel {

    protected static $properties = array(
        'id',
        'url',
        'product_id',
        'website_id',
        'short_url',
        'data',
        'shared',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'share_urls';

    /*
    * @desction get List users
    */
    public function getForShare($param = array())
    {   
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        if (empty($param['limit'])) {
            $param['limit'] = 9;
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('share_urls')
            ->join(               
                'products',                    
                static::$tableName . '.product_id = products.product_id',
                array(                   
                    'code',           
                    'price',           
                    'original_price',
                    'discount_percent',
                    'discount_amount',                                       
                    'image_facebook',                     
                )
            )
            ->join(               
                'product_locales',                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(                   
                    'name',                     
                    'short',                     
                )
            )
             ->join(
                'product_images', 
                'products.image_id = product_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT   
            )
            ->order('shared ASC')
            ->order('updated DESC')
            ->limit($param['limit'])
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
        if (!empty($param['option_id'])) {   
            if (is_array($param['option_id'])) {
                $param['option_id'] = implode(',', $param['option_id']);
            }
            $select->join(
                'product_has_fields', 
                'products.product_id = product_has_fields.product_id',
                array(
                    'field_id'                   
                )
            );
            $select->where(new Expression("LOCATE('[{$param['option_id']}]', product_has_fields.value_id) > 0"));
            $select->where('product_has_fields.active = 1');
        }
        if (!empty($param['not_in_product_id'])) {
            $select->where(new Expression(
                "products.product_id NOT IN ({$param['not_in_product_id']})"
            ));
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));       
        if (!empty($data)) {            
            $productIds = Arr::field($data, 'product_id'); 
            $productHasColor = new ProductHasColors;
            $colors = $productHasColor->getAll($productIds);
            foreach ($data as &$row) {
                $row['colors'] = Arr::filter($colors, 'product_id', $row['product_id']);
            }
            unset($row);
            if (!isset($param['no_update'])) {
                $ids = Arr::field($data, 'id'); 
                self::update(array(
                    'table' => 'share_urls',
                    'set' => array(
                        'shared' => new Expression('IFNULL(shared,0) + 1')
                    ),
                    'where' => array(
                        new Expression('id IN (' . implode(',', array_values($ids)) . ')')
                    )
                ));         
            }
        }
        return $data;
    }  
    
    public function add($param = array())
    {
        if (empty($param['website_id'])            
            || empty($param['url'])) {
            return false;
        }        
        $param['url'] = $param['url']  . '?utm_source=facebook&utm_medium=social&utm_campaign=product';
        $param['short_url'] = Util::googleShortUrl($param['url']); 
        $values = array(
            'website_id' => $param['website_id'],
            'product_id' => $param['product_id'],
            'url' => $param['url'],
            'short_url' => $param['short_url'],
            'shared' => 0,
            'created' => new Expression('UNIX_TIMESTAMP()'),
            'updated' => new Expression('UNIX_TIMESTAMP()'),
        );
        if (!empty($param['data'])) {
            $values['data'] = $param['data'];
        }
        if (!empty($values) && self::batchInsert(
                $values, 
                array(                    
                    'data' => new Expression('VALUES(`data`)'),
                    'updated' => new Expression('VALUES(`updated`)'),
                ),
                false
            )
        ) {
            if (!empty($param['image_facebook'])) {
                $productModel = new Products;
                $productModel->update(array(
                    'set' => array('image_facebook' => $param['image_facebook']), 
                    'where' => array(
                        'website_id' => $param['website_id'],
                        'product_id' => $param['product_id']
                    ),
                ));
            }
            return true;
        }
        return false;  
    }
}
