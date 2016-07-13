<?php

namespace Api\Model;

use Application\Lib\Log;
use Application\Lib\Arr;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class ProductOrders extends AbstractModel {
    
    protected static $properties = array(
        'order_id',        
        '_id',
        'user_id',
        'code',
        'total_money',
        'discount',
        'tax',
        'shipping',
        'ip',
        'user_name',
        'user_email',
        'user_phone',
        'user_mobile',
        'website_id',
        'user_address_id',
        'user_country_code',
        'user_state_code',
        'user_city_code',
        'user_street',
        'user_address_name',
        'note',
        'is_new',
        'is_paid',
        'is_shipping',
        'is_cancel',
        'is_done',
        'payment_date',
        'shipping_date',
        'cancel_date',
        'done_date',
        'payment',
        'created',
        'updated',
        'active',        
        'address_id', 
        'voucher_code', 
        'created',
        'updated',
    );
    
    protected static $primaryKey = 'order_id';
    
    protected static $tableName = 'product_orders';
    
    public function getList($param)
    { 
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'order_id',        
                '_id',
                'user_id',
                'code',
                'total_money',
                'discount',
                'tax',
                'shipping',
                'ip',
                'user_name',
                'user_email',
                'user_phone',
                'user_mobile',
                'website_id',
                'user_address_id',
                'country_code' => 'user_country_code',
                'state_code' => 'user_state_code',
                'city_code' => 'user_city_code',
                'street' => 'user_street',
                'note',
                'is_new',
                'is_paid',
                'is_shipping',
                'is_cancel',
                'is_done',
                'payment_date',
                'shipping_date',
                'cancel_date',
                'done_date',
                'payment',
                'created',
                'updated',
                'active', 
            ))
            ->join(               
                'location_countries',                    
                static::$tableName . '.user_country_code = location_countries.iso_a2',
                array(
                    'country_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_states',                    
                static::$tableName . '.user_state_code = location_states.iso',
                array(
                    'state_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_cities',                    
                static::$tableName . '.user_city_code = location_cities.code',
                array(
                    'city_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.website_id = ' . $param['website_id']); 
        if (!empty($param['code'])) {            
            $select->where(static::$tableName . '.code = '. self::quote($param['code']));  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. self::quote($param['active']));  
        }
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['user_id'])) {            
            $select->where(static::$tableName . '.user_id = '. self::quote($param['user_id']));  
        } 
        if (!empty($param['country_code'])) {            
            $select->where(static::$tableName . '.user_country_code = '. self::quote($param['country_code']));  
        } 
        if (!empty($param['state_code'])) {            
            $select->where(static::$tableName . '.user_state_code = '. self::quote($param['state_code']));  
        } 
        if (!empty($param['city_code'])) {            
            $select->where(static::$tableName . '.user_city_code = '. self::quote($param['city_code']));  
        } 
        if (!empty($param['user_email'])) {
            $select->where(new Expression(static::$tableName .  ".user_email LIKE '%{$param['user_email']}%'"));
        }
        if (!empty($param['user_name'])) {
            $select->where(new Expression(static::$tableName .  ".user_name LIKE '%{$param['user_name']}%'"));
        }   
        if (!empty($param['user_mobile'])) {
            $select->where(new Expression(static::$tableName .  ".user_mobile LIKE '%{$param['user_mobile']}%'"));
        }   
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(order_id|code|state_name|city_name|street|user_name|user_email|user_mobile|total_money|created|updated)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {  
                    case 'state_name':
                        $select->order('location_states.name ' . $match[2]);
                        break;
                    case 'city_name':
                        $select->order('location_cities.name ' . $match[2]);
                        break;
                    case 'street':
                        $match[1] = 'user_street';                        
                    default:
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.is_cancel ASC');
        } 
        $select->group('order_id');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $data = static::toArray(static::selectQuery($selectString));
        foreach ($data as &$row) {
            $row['subtotal_money'] = $row['total_money'];
            $row['total_money'] = $row['total_money'] + db_float($row['tax']) + db_float($row['shipping']);
            
            if ($row['is_cancel']) {
                $row['status'] = 'cancel';
            } elseif ($row['is_done']) {
                $row['status'] = 'done';
            } elseif ($row['is_shipping']) {
                $row['status'] = 'shipping';
            } elseif ($row['is_new']) {
                $row['status'] = 'new';
            } else {
                $row['status'] = 'none';
            }
            $address = array(); 
            if (!empty($row['street'])) {
                $address[] = $row['street']; 
            }
            if (!empty($row['city_name'])) {
                $address[] = $row['city_name']; 
            }
            if (!empty($row['state_name'])) {
                $address[] = $row['state_name']; 
            }
//            if (!empty($row['country_name'])) {
//                $address[] = $row['country_name']; 
//            }
            if (!empty($row)) {
                $row['address'] = implode(', ', $address);
            }
        }
        unset($row);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => $data, 
        );
    }
    
    public function getAll($param) {        
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'order_id',        
                '_id',
                'user_id',
                'code',
                'is_paid',
                'is_cancel',
                'total_money',
                'discount',
                'ip',
                'user_name',
                'user_email',
                'user_phone',
                'user_mobile',
                'website_id',
                'user_address_id',
                'user_country_code',
                'user_state_code',
                'user_city_code',
                'user_street',
                'note',
                'is_new',
                'payment',
                'created',
                'updated',
                'active', 
            ))            
            ->where(static::$tableName . '.active = 1')     
            ->order('create DESC');     
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. self::quote($param['website_id']));  
        }
        if (!empty($param['user_id'])) {            
            $select->where(static::$tableName . '.user_id = '. self::quote($param['user_id']));  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        if (empty($param['payment']) || !in_array($param['payment'], array('COD', 'ATM'))) {
            $param['payment'] = 'COD';
        }
        
        $_id = mongo_id();  // product_orders._id                
        $values = array(
            '_id' => $_id,
            'website_id' => $param['website_id'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'is_new' => 1
        );
        if (isset($param['is_cancel'])) {
            $values['is_cancel'] = $param['is_cancel'];
        }
        if (isset($param['shipping'])) {
            $values['shipping'] = db_float($param['shipping']);
        }
        if (isset($param['discount'])) {
            $values['discount'] = db_float($param['discount']);
        }
        if (isset($param['total_money'])) {
            $values['total_money'] = db_float($param['total_money']);
        }
        if (isset($param['code'])) {
            $values['code'] = $param['code'];
        }  
        if (isset($param['is_paid'])) {
            $values['is_paid'] = $param['is_paid'];
        }
        if (!empty($param['user_id'])) {
            $user = self::find(
                array(  
                    'table' => 'users',
                    'where' => array('user_id' => $param['user_id'])
                ),
                self::RETURN_TYPE_ONE
            );   
            if (empty($user)) {
                self::errorNotExist('user_id');
                return false;
            }
            if (!isset($param['user_name'])) {
                $param['user_name'] = $user['name'];
            }
            if (!isset($param['user_email'])) {
                $param['user_email'] = $user['email'];
            }
            if (!isset($param['user_phone'])) {
                $param['user_phone'] = $user['phone'];
            }
            if (!isset($param['user_mobile'])) {
                $param['user_mobile'] = $user['mobile'];
            }    
            if (empty($param['user_address_id']) 
                && isset($param['user_country_code'])
                && isset($param['user_state_code'])
                && isset($param['user_city_code'])
                && isset($param['user_address_name'])
                && isset($param['user_street'])) {
                $address = new Addresses;
                $address->add(array(
                        'user_id' => $user['user_id'],
                        'street' => $param['user_street'],
                        'country_code' => $param['user_country_code'],
                        'state_code' => $param['user_state_code'],
                        'city_code' => $param['user_city_code'],
                        'name' => $param['user_address_name'],
                    ), 
                    $param['user_address_id']
                );
            } else { 
                $addressId = !empty($param['user_address_id']) ? $param['user_address_id'] : $user['address_id']; 
                $address = self::find(
                    array(  
                        'table' => 'addresses',
                        'where' => array(
                            'user_id' => $user['user_id'],
                            'address_id' => $addressId
                        )
                    ),
                    self::RETURN_TYPE_ONE
                );
                if (!empty($address)) {                   
                    $param['user_address_id'] = $address['address_id'];                    
                    $param['user_address_name'] = $address['name'];                    
                    $param['user_country_code'] = $address['country_code'];                    
                    $param['user_state_code'] = $address['state_code'];                    
                    $param['user_city_code'] = $address['city_code'];                    
                    $param['user_street'] = $address['street'];  
                }
            }
        }
        if (isset($param['user_id'])) {
            $values['user_id'] = $param['user_id'];
        }
        if (isset($param['user_name'])) {
            $values['user_name'] = $param['user_name'];
        }  
        if (isset($param['user_email'])) {
            $values['user_email'] = $param['user_email'];
        }  
        if (isset($param['user_phone'])) {
            $values['user_phone'] = $param['user_phone'];
        }  
        if (isset($param['user_mobile'])) {
            $values['user_mobile'] = $param['user_mobile'];
        }  
        if (isset($param['user_address_id'])) {
            $values['user_address_id'] = $param['user_address_id'];
        }
        if (isset($param['user_address_name'])) {
            $values['user_address_name'] = $param['user_address_name'];
        }
        if (isset($param['user_country_code'])) {
            $values['user_country_code'] = $param['user_country_code'];
        }
        if (isset($param['user_state_code'])) {
            $values['user_state_code'] = $param['user_state_code'];
        }  
        if (isset($param['user_city_code'])) {
            $values['user_city_code'] = $param['user_city_code'];
        } 
        if (isset($param['user_street'])) {
            $values['user_street'] = $param['user_street'];
        } 
        if (isset($param['is_new'])) {
            $values['is_new'] = $param['is_new'];
        }
        if (isset($param['payment'])) {
            $values['payment'] = $param['payment'];
        }
        if (isset($param['voucher_code'])) {
            $values['voucher_code'] = $param['voucher_code'];
        }
        if (isset($param['note'])) {
            $values['note'] = $param['note'];
        }        
        // check voucher valid
        $voucher = new Vouchers();
        if (!empty($param['voucher_code'])) {            
            $voucherDetail = $voucher->getDetail(array(
                'website_id' => $param['website_id'],
                'user_id' => $param['user_id'],
                'code' => $param['voucher_code'],
                'active' => 1
            ));
            if (!empty($voucherDetail)) {
                if (!empty($voucherDetail['used'])) {
                    self::errorNotExist('voucher_code');
                    return false;
                }
                if (!empty($voucherDetail['expired']) && $voucherDetail['expired'] <= time()) {
                    self::errorNotExist('voucher_code');
                    return false;
                }                  
            } else {
                self::errorNotExist('voucher_code');
                return false;
            }
        }
        
        if ($id = self::insert($values)) { 
            if (!empty($param['products'])) { 
                $products = \Zend\Json\Decoder::decode($param['products'], \Zend\Json\Json::TYPE_ARRAY);            
                $param['total_money'] = 0;
                $param['discount'] = 0;
                foreach ($products as $product) {
                    $param['total_money'] += db_float($product['price'])*db_float($product['quantity']);                   
                }
                if (!empty($voucherDetail)) { 
                    $voucher->updateInfo(array(
                        '_id' => $voucherDetail['_id'],
                        'used' => new Expression('UNIX_TIMESTAMP()')
                    ));
                    switch ($voucherDetail['type']) {
                        case 0:  
                            $param['discount'] = db_float($voucherDetail['amount']*$param['total_money']/100);
                            break;
                        case 1:
                            $param['discount'] = db_float($voucherDetail['amount']);
                            break;
                    }
                }                
                self::saveDetail(array(
                    'order_id' => $id,
                    'products' => $param['products'],
                    'total_money' => $param['total_money'],
                    'discount' => $param['discount'],
                ));
                
                $orderList = $this->find(array(
                    'where' => array(
                        'user_id' => $param['user_id'],
                        'active' => 1
                    )
                ));
                if (count($orderList) == 1) {                    
                    $userModel = new Users;
                    $user = $userModel->getDetail(array(                        
                        'user_id' => $values['user_id']
                    ));
                    if (!empty($user)) {                       
                        $userUpdateInfo = array();
                        if (empty($user['name']) && !empty($values['user_name'])) {
                            $userUpdateInfo['name'] = $values['user_name'];
                        }
                        if (empty($user['phone']) && !empty($values['user_phone'])) {
                            $userUpdateInfo['phone'] = $values['user_phone'];
                        }
                        if (empty($user['mobile']) && !empty($values['user_mobile'])) {
                            $userUpdateInfo['mobile'] = $values['user_mobile'];
                        }                    
                        if (empty($user['address_id']) && !empty($values['user_address_id'])) {
                            $userUpdateInfo['address_id'] = $values['user_address_id'];
                        }                        
                        if (!empty($userUpdateInfo)) {
                            $userModel->update(array(
                               'set' =>  $userUpdateInfo,
                               'where' => array('user_id' => $user['user_id'])
                            ));                        
                        }
                    }
                }
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
        if (isset($param['is_cancel']) && $param['is_cancel'] !== '') {
            $set['is_cancel'] = $param['is_cancel'];
            if (!empty($param['is_cancel'])) {
                $set['cancel_date'] = self::unix_timestamp();            
            }
        }        
        if (isset($param['is_shipping']) && $param['is_shipping'] !== '') {
            $set['is_shipping'] = $param['is_shipping'];
            if (!empty($param['is_shipping'])) {
                $set['shipping_date'] = self::unix_timestamp();
            }
        }
        if (isset($param['is_paid']) && $param['is_paid'] !== '') {           
            $set['is_paid'] = $param['is_paid'];  
            if (!empty($param['is_paid'])) {
                $set['payment_date'] = self::unix_timestamp();
            }
        }
        if (isset($param['is_done']) && $param['is_done'] !== '') {
            if (empty($self['is_shipping']) || empty($self['is_paid'])) {
                self::errorOther(ERROR_CODE_OTHER_1, 'is_done');
                return false;
            }
            if (!empty($param['is_done'])) {                               
                $set['done_date'] = self::unix_timestamp(); 
            }            
            $set['is_done'] = $param['is_done'];                       
        }
        if (isset($param['total_money'])) {
            $set['total_money'] = db_float($param['total_money']);
        }       
        if (isset($param['discount'])) {
            $set['discount'] = db_float($param['discount']);
        }       
        if (isset($param['code'])) {
            $set['code'] = $param['code'];
        }
        if (isset($param['user_id'])) {
            $set['user_id'] = $param['user_id'];
        }
        if (isset($param['user_name'])) {
            $set['user_name'] = $param['user_name'];
        } 
        if (isset($param['user_phone'])) {
            $set['user_phone'] = $param['user_phone'];
        }  
        if (isset($param['user_mobile'])) {
            $set['user_mobile'] = $param['user_mobile'];
        }  
        if (isset($param['user_address_id'])) {
            $set['user_address_id'] = $param['user_address_id'];
        }
        if (isset($param['country_code'])) {
            $set['user_country_code'] = $param['country_code'];
        }           
        if (isset($param['state_code'])) {
            $set['user_state_code'] = $param['state_code'];
        }  
        if (isset($param['city_code'])) {
            $set['user_city_code'] = $param['city_code'];
        } 
        if (isset($param['street'])) {
            $set['user_street'] = $param['street'];
        }                 
        if (isset($param['is_new'])) {
            $set['is_new'] = $param['is_new'];
        }
        if (isset($param['note'])) {
            $set['note'] = $param['note'];
        }
        if (isset($param['tax']) && $param['tax'] !== '') {
            $set['tax'] = db_float($param['tax']);
        }
        if (isset($param['shipping']) && $param['shipping'] !== '') {
            $set['shipping'] = db_float($param['shipping']);
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {   
            if (isset($param['get_detail'])) {
                return self::getDetail($param);
            }
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
                'order_id',        
                '_id',
                'user_id',
                'code',                
                'total_money',
                'discount',
                'tax',
                'shipping',
                'ip',
                'user_name',
                'user_email',
                'user_phone',
                'user_mobile',
                'website_id',
                'user_address_id',
                'country_code' => 'user_country_code',
                'state_code' => 'user_state_code',
                'city_code' => 'user_city_code',
                'street' => 'user_street',
                'note',
                'is_new',
                'is_shipping',
                'is_paid',
                'is_cancel',
                'is_done',
                'cancel_date',
                'shipping_date',
                'payment_date',
                'done_date',
                'payment',
                'created',
                'updated',
                'active',
            )) 
            ->join(               
                'websites',                    
                static::$tableName . '.website_id = websites.website_id',
                array(
                    'website_logo' => 'logo',                    
                    'website_url' => 'url',                    
                    'website_email' => 'email',                    
                    'website_phone' => 'phone'                    
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
                'location_countries',                    
                static::$tableName . '.user_country_code = location_countries.iso_a2',
                array(
                    'country_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_states',                    
                static::$tableName . '.user_state_code = location_states.iso',
                array(
                    'state_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                'location_cities',                    
                static::$tableName . '.user_city_code = location_cities.code',
                array(
                    'city_name' => 'name'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where("website_locales.locale = ". self::quote($param['locale']));   
        if (!empty($param['_id'])) {
            $select->where(static::$tableName . "._id = ". self::quote($param['_id']));   
        }
        if (!empty($param['order_id'])) {
            $select->where(static::$tableName . ".order_id = ". self::quote($param['order_id']));   
        }     
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if ($result) {            
            if ($result['is_cancel']) {
                $result['status'] = 'cancel';    
            } elseif ($result['is_done']) {
                $result['status'] = 'done';
            } elseif ($result['is_shipping']) {
                $result['status'] = 'shipping';            
            } elseif ($result['is_new']) {
                $result['status'] = 'new';
            } else {
                $result['status'] = 'none';
            }
            $address = array(); 
            if (!empty($result['street'])) {
                $address[] = $result['street']; 
            }
            if (!empty($result['city_name'])) {
                $address[] = $result['city_name']; 
            }
            if (!empty($result['state_name'])) {
                $address[] = $result['state_name']; 
            }
            if (!empty($result['country_name'])) {
                $address[] = $result['country_name']; 
            }
            if (!empty($result)) {
                $result['address'] = implode(', ', $address);
            }    
            if (empty($result['discount'])) {
                $result['discount'] = 0;
            }
            if (empty($result['tax'])) {
                $result['tax'] = 0;
            }
            if (empty($result['shipping'])) {
                $result['shipping'] = 0;
            }
            $has = new OrderHasProducts();
            $result['products'] = $has->getAll(array(
                'order_id' => $result['order_id'],
                'active' => 1
            ));
        }
        return $result;
    }
    
    public function updateSort($param) { 
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
    public function addProduct($param)
    {
        $has = new OrderHasProducts();
        if ($has->add($param)) {
            self::updateTotalMoney(array(
                'order_id' => $param['order_id']
            ));
            if (isset($param['get_order'])) {
                return self::find(
                    array(     
                        'where' => array(
                            'order_id' => $param['order_id'],                           
                            'active' => 1
                        )
                    ),
                    self::RETURN_TYPE_ONE
                );
            }
            return true;
        }
        return false;
    }
    
    public function onOffProduct($param)
    { 
        $param['product_id'] = $param['_id'];
        $param['active'] = $param['value'];
        $has = new OrderHasProducts();
        if ($has->onoff($param)) {
            self::updateTotalMoney(array(
                'order_id' => $param['order_id']
            ));
            if (isset($param['get_order'])) {
                return self::find(
                    array(     
                        'where' => array(
                            'order_id' => $param['order_id'],                           
                            'active' => 1
                        )
                    ),
                    self::RETURN_TYPE_ONE
                );
            }
            return true;
        }
        return false;
    }
        
    public function saveDetail($param)
    {        
        $has = new OrderHasProducts();
        if ($has->saveDetail($param)) {
            return self::updateTotalMoney(array(
                'order_id' => $param['order_id'],
                'total_money' => !empty($param['total_money']) ? $param['total_money'] : 0,
                'discount' => !empty($param['discount']) ? $param['discount'] : 0,
            ));
        }
        return false;
    }
    
    public function getTotalMoney($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from('product_order_has_products')  
            ->columns(array(
                'total_money' => new Expression("SUM(quantity*price)")
            ))
            ->where('order_id = ' . static::quote($param['order_id']))
            ->where('active = 1');
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        return !empty($result['total_money']) ? $result['total_money'] : 0;
    }
    
    public function updateTotalMoney($param)
    {  
        if (empty($param['total_money'])) {
            $param['total_money'] = $this->getTotalMoney($param);
        }
        if (empty($param['discount'])) {
            $param['discount'] = 0;
        }
        if (!self::update(array(
            'set' => array(
                'total_money' => db_float($param['total_money']),
                'discount' => db_float($param['discount']),
            ),
            'where' => array(
                'order_id' => $param['order_id'],
            ),
        ))) {
            return false;
        }  
        return true;
    }
    
}
