<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\Expression;
use Application\Lib\Arr;
use Application\Lib\Util;
use Application\Lib\Log;

class FacebookWallShares extends AbstractModel {

    protected static $properties = array(
        'id',     
        'keyword',
        'user_id',
        'facebook_id',        
        'social_id',
        'website_id',
        'site',
        'created',
        'updated',           
    );
    
    protected static $tableName = 'facebook_wall_shares';

    /*
    * @desction
    */
    public function getAll($param = array())
    {  
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('facebook_wall_shares')  
            ->join(               
                array(
                    'facebook_group_shares' => 
                    $sql->select()
                        ->columns(array(
                            'wall_social_id',
                            'group_id' => new Expression('GROUP_CONCAT(group_id SEPARATOR  \',\')'),
                            'group_social_id' => new Expression('GROUP_CONCAT(social_id SEPARATOR  \',\')')
                        ))
                        ->from('facebook_group_shares')
                        ->group('wall_social_id')
                ),                 
                'facebook_wall_shares.social_id = facebook_group_shares.wall_social_id',
                array(           
                    'group_social_id',
                    'group_id',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT
            )            
            ->order(new Expression('RAND()'))
            ->limit(100);      
        if (!empty($param['keyword'])) {
            $param['keyword'] = strtolower($param['keyword']);
            $select->where(new Expression("(
                LOWER(facebook_wall_shares.keyword) = '{$param['keyword']}'                
            )"));
        }       
        if (!empty($param['user_id'])) {
            $select->where('facebook_wall_shares.user_id = ' . self::quote($param['user_id']));
        }
        if (!empty($param['site'])) {
            $select->where('facebook_wall_shares.site = ' . self::quote($param['site']));
        }
        Log::info($sql->getSqlStringForSqlObject($select));
        $data = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        ); 
        return $data;
    }
    
    public function add($param = array())
    {
        if (empty($param['keyword']) 
            || empty($param['facebook_id']) 
            || empty($param['social_id'])) {
            return false;
        }
        if (empty($param['site'])) {
            $param['site'] = 'sendo.vn';
        }
        $values = array(
            'website_id' => $param['website_id'],
            'facebook_id' => $param['facebook_id'],            
            'user_id' => !empty($param['user_id']) ? $param['user_id'] : 0,     
            'keyword' => $param['keyword'],
            'social_id' => $param['social_id'],                      
            'site' => $param['site'],                      
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
