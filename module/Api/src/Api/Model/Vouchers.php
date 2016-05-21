<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Vouchers extends AbstractModel {
    
    protected static $properties = array(
        'voucher_id',
        'code',
        '_id',
        'amount',      
        'type',
        'used',
        'expired',        
        'created',
        'updated',
        'active',
        'min_total_money',
        'website_id',
        'user_id',
    );
    
    protected static $primaryKey = 'voucher_id';
    
    protected static $tableName = 'vouchers';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)                       
            ->where(static::$tableName . '.website_id = ' . $param['website_id']);
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['code'])) {
            $select->where(new Expression("code LIKE '%{$param['code']}%'"));
        }        
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(code|used|type)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {                                
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.used DESC');
        }         
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );
    }
    
    public function getAll($param) {       
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(    
                '_id',
                'voucher_id', 
                'code', 
                'amount', 
                'type', 
                'used', 
                'expired',
                'min_total_money'
            ))                     
            ->where(static::$tableName . '.website_id = ' . $param['website_id']) 
            ->where(static::$tableName . '.active = 1')     
            ->order('sort');     
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function generateVoucherCode($userId)
    {
        do {
            $code = voucher_code();
            $find = self::find(
                array(            
                    'where' => array(
                        'code' => $code,
                        'user_id' => $userId
                    )
                ),
                self::RETURN_TYPE_ONE
            );
        } while(!empty($find));
        return $code;
    }
    
    public function add($param)
    {
        $param['code'] = $this->generateVoucherCode($param['user_id']);
        $_id = mongo_id();  // vouchers._id              
        $values = array(
            '_id' => $_id,
            'website_id' => $param['website_id'],             
            'amount' => Util::toPrice($param['amount']),
            'type' => $param['type'],
            'code' => $param['code'],
            'user_id' => $param['user_id'],
        );
        if (isset($param['used'])) {
            $values['used'] = $param['used'];
        }
        if (isset($param['expired'])) {
            $values['expired'] = $param['expired'];
        }
        if (isset($param['min_total_money'])) {
            $values['min_total_money'] = $param['min_total_money'];
        }
        if ($id = self::insert($values)) {
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
        if (isset($param['used'])) {
            $set['used'] = $param['used'];
        }    
        if (isset($param['expired'])) {
            $set['expired'] = $param['expired'];
        }    
        if (isset($param['code'])) {
            $set['code'] = $param['code'];
        }
        if (isset($param['amount'])) {
            $set['amount'] = Util::toPrice($param['amount']);
        }
        if (isset($param['type'])) {
            $set['type'] = $param['type'];
        }
        if (isset($param['min_total_money'])) {
            $set['min_total_money'] = $param['min_total_money'];
        }
        if (isset($param['user_id'])) {
            $set['user_id'] = $param['user_id'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {                                  
            return true;
        } 
        return false;
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
                '_id',
                'voucher_id', 
                'code', 
                'amount', 
                'type', 
                'used', 
                'expired',
                'min_total_money'
            ))
            ->join(               
                'websites',                    
                static::$tableName . '.website_id = websites.website_id',
                array(
                    'website_logo' => 'logo',                    
                    'website_url' => 'url',                    
                    'website_email' => 'email',                    
                    'website_phone' => 'phone',
                )
            )
            ->join(               
                'website_locales',                    
                static::$tableName . '.website_id = website_locales.website_id',
                array(
                    'website_name' => 'name',                    
                    'website_company_name' => 'company_name',                    
                    'website_address' => 'address',                    
                )
            )
            ->join(               
                'users',                    
                static::$tableName . '.user_id = users.user_id',
                array(                            
                    'user_name' => 'name',                    
                    'user_email' => 'email'                   
                )
            )
            ->where("website_locales.locale = ". self::quote($param['locale']));
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['user_id'])) {            
            $select->where(static::$tableName . '.user_id = '. self::quote($param['user_id']));  
        }
        if (!empty($param['_id'])) {            
            $select->where(static::$tableName . '._id = '. self::quote($param['_id']));  
        }
        if (!empty($param['voucher_id'])) {            
            $select->where(static::$tableName . '.voucher_id = '. self::quote($param['voucher_id']));  
        }
        if (!empty($param['code'])) {            
            $select->where(static::$tableName . '.code = '. self::quote($param['code']));  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. self::quote($param['active']));  
        }
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );        
        return $result;
    }
    
    function check($param) {                 
        $voucherDetail = $this->getDetail(array(
            'website_id' => $param['website_id'],
            'user_id' => $param['user_id'],
            'code' => $param['voucher_code'],
            'active' => 1
        ));
        if (!empty($voucherDetail)) {
            if (!empty($voucherDetail['used'])) {
                self::errorNotExist('voucher_code');
                //self::errorOther(self::ERROR_CODE_OTHER_1, 'used', 'The voucher_code have been already used');
                return false;
            }
            if (!empty($voucherDetail['expired']) && $voucherDetail['expired'] <= time()) {
                self::errorNotExist('voucher_code');
                //self::errorOther(self::ERROR_CODE_OTHER_2, 'expired');
                return false;
            }
        } else {
            self::errorNotExist('voucher_code');
            return false;
        }
        return $voucherDetail;
    }

}
