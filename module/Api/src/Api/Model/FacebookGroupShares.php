<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;

class FacebookGroupShares extends AbstractModel {

    protected static $properties = array(
        'id',     
        'group_id',
        'user_id',
        'facebook_id',        
        'social_id',
        'wall_social_id',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'facebook_group_shares';

    /*
    * @desction
    */
    public function getAll($param = array())
    {  
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('facebook_group_shares')             
            ->order('updated DESC');      
        if (!empty($param['group_id'])) {
            $param['group_id'] = strtolower($param['group_id']);
            $select->where(new Expression("(
                LOWER(group_id) = '{$param['group_id']}'                
            )"));
        }       
        if (!empty($param['user_id'])) {
            $select->where('user_id = ' . self::quote($param['user_id']));
        }     
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        ); 
        return $data;
    }
    
    public function add($param = array())
    {
        if (empty($param['group_id']) 
            || empty($param['facebook_id'])) {
            return false;
        }  
        if (isset($param['delete_group_post']) 
            && !empty($param['group_id']) 
            && !empty($param['facebook_id']) 
            && !empty($param['wall_social_id'])
            && !empty($param['social_id'])) {
            $ok = self::delete(array(
                'table' => 'facebook_group_shares',
                'where' => array(
                    'group_id' => $param['group_id'],
                    'facebook_id' => $param['facebook_id'],
                    'wall_social_id' => $param['wall_social_id'],
                    'social_id' => $param['social_id'],
                )
            ));
            return $ok;
        }
        $values = array(
            'website_id' => $param['website_id'],
            'group_id' => $param['group_id'],
            'social_id' => $param['social_id'],
            'wall_social_id' => $param['wall_social_id'],
            'facebook_id' => $param['facebook_id'],            
            'user_id' => !empty($param['user_id']) ? $param['user_id'] : 0,            
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
