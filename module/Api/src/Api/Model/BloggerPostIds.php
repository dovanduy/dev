<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;

class BloggerPostIds extends AbstractModel {

    protected static $properties = array(
        'id',     
        'product_id',
        'blog_id',        
        'post_id',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'blogger_post_ids';

    /*
    * @desction get List users
    */
    public function getAll($param = array())
    {  
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('blogger_post_ids')
            ->order('updated DESC');        
        
        if (!empty($param['blog_id'])) {
            $select->where('blog_id = ' . self::quote($param['blog_id']));
        }
        if (!empty($param['post_id'])) {
            $select->where('post_id = ' . self::quote($param['post_id']));
        }
		if (!empty($param['product_id'])) {
            $select->where('product_id = ' . self::quote($param['product_id']));
        }       
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );
        return $data;
    }
    
    public function add($param = array())
    {
        if (empty($param['product_id']) 
            || empty($param['blog_id']) 
            || empty($param['post_id'])) {
            return false;
        }
        $values = array(          
            'product_id' => $param['product_id'],                      
            'blog_id' => !empty($param['blog_id']) ? $param['blog_id'] : 0,
            'post_id' => !empty($param['post_id']) ? $param['post_id'] : 0,
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
            return true;
        }
        return false;  
    }
    
}
