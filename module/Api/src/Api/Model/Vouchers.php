<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Util;
use Application\Lib\Log;
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
        'phone',
    );
    
    protected static $primaryKey = 'voucher_id';
    
    protected static $tableName = 'vouchers';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)                       
            ->where(static::$tableName . '.website_id = ' . $param['website_id']);
        
        if (isset($param['used']) && $param['used'] !== '') {        
            if (!empty($param['used'])) {
                $select->where(static::$tableName . '.used > 0');  
            } else {
                $select->where(static::$tableName . '.used = 0 OR ' . static::$tableName . '.used IS NULL');
            }
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['code'])) {
            $select->where(new Expression("code LIKE '%{$param['code']}%'"));
        }        
        if (!empty($param['phone'])) {
            $select->where(new Expression("phone LIKE '%{$param['phone']}%'"));
        }        
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(code|used|type|created|updated)-(asc|desc)+/", $param['sort'], $match);
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
    
    public function generateVoucherCode($userId = null, $mobile = null)
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
        if (empty($param['phone']) && empty($param['user_id'])) {
            self::errorParamInvalid('phone_or_user_id');
            return false;
        }        
        if (empty($param['code'])) {
            $param['code'] = $this->generateVoucherCode($param['user_id'], $param['phone']);
        }
        $_id = mongo_id();  // vouchers._id              
        $values = array(
            '_id' => $_id,
            'website_id' => $param['website_id'],             
            'amount' => Util::toPrice($param['amount']),
            'type' => $param['type'],
            'code' => $param['code'],           
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
        if (isset($param['user_id'])) {
            $values['user_id'] = $param['user_id'];
        }
        if (isset($param['phone'])) {
            $values['phone'] = $param['phone'];
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
                'min_total_money',
                'phone',
                'user_id',
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
        if (!empty($param['phone'])) {            
            $select->where(static::$tableName . '.phone = '. self::quote($param['phone']));  
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
        $sql = $sql->getSqlStringForSqlObject($select);
        Log::info('SQL', $sql);
        $result = self::response(
            static::selectQuery($sql), 
            self::RETURN_TYPE_ONE
        );        
        return $result;
    }
    
    function check($param) {                
        if (empty($param['voucher_code'])) {
            self::errorParamInvalid('voucher_code');
            return false;
        }    
        $cond = [
            'website_id' => $param['website_id'],            
            'code' => $param['voucher_code'],
            'active' => 1
        ];
        if (!empty($param['phone'])) {
            $cond['phone'] = $param['phone'];
        } elseif (!empty($param['user_id'])) {
            $cond['user_id'] = $param['user_id'];
        }
        $voucherDetail = $this->find([
                'where' => $cond,
                'order' => 'used ASC, expired ASC'
            ],
            self::RETURN_TYPE_ONE
        );                  
        if (!empty($voucherDetail)) {
            if (!empty($voucherDetail['used'])) {
                //self::errorNotExist('voucher_code');
                self::errorOther(self::ERROR_CODE_OTHER_1, 'voucher_code', 'The voucher_code have been already used');
                return false;
            }
            if (!empty($voucherDetail['expired']) && $voucherDetail['expired'] <= time()) {
                self::errorNotExist('voucher_code');
                self::errorOther(self::ERROR_CODE_OTHER_2, 'voucher_code');
                return false;
            }
        } else {
            self::errorNotExist('voucher_code');
            return false;
        }
        return $voucherDetail;
    }

}
