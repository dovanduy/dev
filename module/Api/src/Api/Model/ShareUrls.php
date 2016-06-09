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
                    'price',                     
                    'image_facebook',                     
                )
            )
            ->join(               
                'product_locales',                    
                static::$tableName . '.product_id = product_locales.product_id',
                array(                   
                    'name',                     
                )
            )
            ->order('shared ASC')
            ->order('updated DESC')
            ->limit($param['limit']);        
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));       
        if (!empty($data) && !isset($param['no_update'])) {
            $id = Arr::field($data, 'id'); 
            self::update(array(
                'table' => 'share_urls',
                'set' => array(
                    'shared' => new Expression('IFNULL(shared,0) + 1')
                ),
                'where' => array(
                    new Expression('id IN (' . implode(',', array_values($id)) . ')')
                )
            ));         
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
        if (!empty($values) && self::batchInsert(
                $values, 
                array(                    
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
