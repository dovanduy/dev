<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class InputOptions extends AbstractModel {
    
    protected static $properties = array(
        'option_id',
        '_id',
        'sort',        
        'locale',
        'name',
        'short',
        'content',
        'created',
        'updated',
        'active',
        'field_id',
    );
    
    protected static $primaryKey = 'option_id';
    
    protected static $tableName = 'input_options';
    
    public function getList($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'input_option_locales', 
                static::$tableName . '.option_id = input_option_locales.option_id',
                array('name', 'short')
            )            
            ->where("input_option_locales.locale = ". self::quote(\Application\Module::getConfig('general.default_locale')));  
        
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['name'])) {
            $select->where(new Expression("input_option_locales.name LIKE '%{$param['name']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(name|sort)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
                switch ($match[1]) {
                    case 'name':
                        $select->order("input_option_locales.{$match[1]} " . $match[2]);
                        break;
                    case 'sort':
                        $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);
                        break;
                }                
            }            
        } else {
            $select->order(static::$tableName . '.sort ASC');
        }         
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
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
                'option_id', 
                'field_id', 
                '_id', 
                'sort',
                'active',
            ))
            ->join(               
                array(
                    'input_option_locales' => 
                    $sql->select()
                        ->from('input_option_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.option_id = input_option_locales.option_id',
                array(
                    'locale', 
                    'name', 
                    'short',
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->order('sort');   
        if (!empty($param['field_id'])) {      
            if (is_array($param['field_id'])) {
                $param['field_id'] = implode(',', $param['field_id']);
            }
            $select->where(static::$tableName . '.field_id IN ('. $param['field_id'] . ')');  
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param, &$id = 0)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        
        $detail = self::getDetail(array(
            'field_id' => $param['field_id'],
            'name' => $param['name'],
            'locale' => $param['locale'],
        ));
        if (!empty($detail)) {
            $id = $detail['option_id'];
            if (isset($param['return_id'])) {
                return $id;
            }
            return $detail['_id'];
        }
        
        $_id = mongo_id();  // input_options._id        
        $values = array(
            '_id' => $_id,
            'field_id' => $param['field_id'],
            'sort' => self::max(array(
                'table' => 'input_options',
                'field' => 'sort',
                'where' => array(
                    'field_id' => $param['field_id']
                )
            )) + 1,
        );                
        if ($id = self::insert($values)) {
            $localeValues = array(
                'option_id' => $id,
                'locale' => $param['locale']
            );
            if (isset($param['name'])) {
                $localeValues['name'] = $param['name'];
            } 
            if (isset($param['short'])) {
                $localeValues['short'] = $param['short'];
            }
            self::insert($localeValues, 'input_option_locales');
            if (empty(self::error()) && !empty($param['name'])) {
                $field = self::find(
                    array(     
                        'table' => 'input_field_locales',
                        'where' => array('field_id' => $param['field_id'])
                    ),
                    self::RETURN_TYPE_ONE
                );
                if ($field) {        
                    $urlIds = new UrlIds();
                    $urlIds->addUpdateByOptionId(array(
                        'url' => name_2_url($field['name'] . '-' . $param['name']),
                        'option_id' => $id,
                        'website_id' => $param['website_id'],
                    ));
                }
            }
            if (isset($param['return_id'])) {
                return $id;
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
        if (isset($param['field_id'])) {
            $set['field_id'] = $param['field_id'];
        }
        if (isset($param['sort'])) {
            $set['sort'] = $param['sort'];
        }
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {
            $locales = \Application\Module::getConfig('general.locales');
            if (count($locales) == 1) {
                $param['locale'] = array_keys($locales)[0];
                self::addUpdateLocale($param);
            }
            return true;
        }
        return false;
    }

    public function addUpdateLocale($param)
    {
        $detail = self::getDetail(array(
            '_id' => $param['_id'],
            'locale' => $param['locale'],
        ));
        if (empty($detail)) {
            self::errorNotExist('_id');
            return false;
        }
        
        static::$tableName = 'input_option_locales';
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
        if (empty($detail['locale'])) {
            $values['option_id'] = $detail['option_id'];
            $values['locale'] = $param['locale'];
            return self::insert($values);
        }
        return self::update(
            array(
                'set' => $values,
                'where' => array(
                    'option_id' => $detail['option_id'],
                    'locale' => $param['locale'],
                ),
            )
        );
    }

    public function getDetail($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'option_id', 
                '_id',                 
                'sort',
                'field_id',
            ))
            ->join(               
                array(
                    'input_option_locales' => 
                    $sql->select()
                        ->from('input_option_locales')
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                static::$tableName . '.option_id = input_option_locales.option_id',
                array(
                    'locale', 
                    'name', 
                    'short'
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            );  
        if (!empty($param['_id'])) {
            $select->where(static::$tableName . "._id = ". self::quote($param['_id'])); 
        }
        if (!empty($param['option_id'])) {
            $select->where(static::$tableName . ".option_id = ". self::quote($param['option_id'])); 
        }
        if (!empty($param['field_id'])) {
            $select->where(static::$tableName . ".field_id = ". self::quote($param['field_id'])); 
        }
        if (!empty($param['name'])) {
            $select->where("input_option_locales.name = ". self::quote($param['name'])); 
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
    }
    
    public function updateSort($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateSort($param);
    }
    
    public function save($param) {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        if (is_array(self::$primaryKey)) {
            self::errorParamInvalid();
            return false;
        }
        if (empty($param['name'])) {
            self::errorParamInvalid();
            return false;
        }
        $param['name'] = \Zend\Json\Decoder::decode($param['name'], \Zend\Json\Json::TYPE_ARRAY);        
        $values = array();
        foreach ($param['name'] as $id => $name) {
            $values[] = array(
                'option_id' => $id,
                'locale' => $param['locale'],
                'name' => $name
            ); 
        }
        if (self::updateSort($param)) {
            self::$tableName = 'input_option_locales';        
            return self::batchInsert(
                $values,
                array(
                    'name' => new Expression('VALUES(`name`)'),
                ),
                false
            );
        }
        return false; 
    }
}
