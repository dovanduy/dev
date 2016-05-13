<?php

namespace Api\Model;

use Application\Lib\Arr;
use Application\Lib\Log;
use Application\Lib\Util;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class News extends AbstractModel {
    
    protected static $properties = array(
        'news_id',
        '_id',       
        'locale',
        'title',
        'short',
        'content',
        'created',
        'updated',
        'active',
        'image_id',
        'category_id',
        'website_id',
    );
    
    protected static $primaryKey = 'news_id';
    
    protected static $tableName = 'news';
    
    public function getList($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)   
            ->columns(array(                
                'news_id', 
                '_id', 
                'title', 
                'active',
            ))
            ->join(
                'news_has_categories', 
                static::$tableName . '.news_id = news_has_categories.news_id',
                array('category_id'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(               
                array(
                    'news_category_locales' => 
                    $sql->select()
                        ->from('news_category_locales')                        
                        ->where("locale = ". self::quote($param['locale']))
                ),                    
                'news_category_locales.category_id = news_has_categories.category_id',
                array(
                    'category_name' => new Expression('GROUP_CONCAT(name SEPARATOR  \', \')')
                ),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->join(
                'news_images', 
                static::$tableName . '.image_id = news_images.image_id',
                array('url_image'),
                \Zend\Db\Sql\Select::JOIN_LEFT    
            )
            ->where(static::$tableName . '.locale = '. self::quote($param['locale']));
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (isset($param['active']) && $param['active'] !== '') {            
            $select->where(static::$tableName . '.active = '. $param['active']);  
        }
        if (!empty($param['title'])) {
            $select->where(new Expression(static::$tableName .  ".title LIKE '%{$param['title']}%'"));
        }
        if (!empty($param['category_id'])) {
            $select->where(new Expression(
                static::$tableName .  ".news_id IN (
                    SELECT news_id 
                    FROM news_has_categories 
                    WHERE category_id IN ({$param['category_id']})
                )"
            ));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            preg_match("/(title)-(asc|desc)+/", $param['sort'], $match);
            if (count($match) == 3) {
               $select->order(static::$tableName . '.' . $match[1] . ' ' . $match[2]);               
            }            
        } else {
            $select->order(static::$tableName . '.updated DESC');
        }  
        $select->group('news_id');
        $selectString = $sql->getSqlStringForSqlObject($select);
        Log::info('SQL', $selectString);
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
                'news_id', 
                '_id', 
                'title',
                'short',
                'image_id',
            ))            
            ->where(static::$tableName . '.active = 1')
            ->where(static::$tableName . '.locale = '. self::quote($param['locale']));   
        if (!empty($param['website_id'])) {            
            $select->where(static::$tableName . '.website_id = '. $param['website_id']);  
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            $select->offset(0);
        }
        return self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ALL
        );        
    }    
    
    public function add($param)
    {
        if (empty($param['locale'])) {
            $param['locale'] = \Application\Module::getConfig('general.default_locale');
        }
        $_id = mongo_id();  // news._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {
                $image = new Images();
                $param['image_id'] = $image->add(array(
                    'src' => 'news',
                    'src_id' => 0,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
            }          
        } elseif (!empty($param['url_image'])) {
            $image = new Images();
            $param['image_id'] = $image->add(array(
                'src' => 'news',
                'src_id' => 0,
                'url_image' => Util::uploadImageFromUrl($param['url_image']),
                'is_main' => 1,
            ));
        }
        $values = array(
            '_id' =>  $_id,
            'locale' => $param['locale']
        );          
        if (isset($param['image_id'])) {
            $values['image_id'] = $param['image_id'];
        }
        if (isset($param['title'])) {
            $values['title'] = $param['title'];
        } 
        if (isset($param['short'])) {
            $values['short'] = $param['short'];
        } 
        if (isset($param['content'])) {
            $values['content'] = $param['content'];
        }
        if (isset($param['website_id'])) {
            $values['website_id'] = $param['website_id'];
        }
        if ($id = self::insert($values)) {            
            if (empty(self::error()) && !empty($param['image_id'])) {
                $image = new Images();
                $image->updateInfo(array(
                    'src' => 'news',
                    'src_id' => $id,
                    'id' => $param['image_id']
                ));
            }
            if (!empty($param['category_id'])) {
                $newsHasCategories = new NewsHasCategories();
                $newsHasCategories->addUpdate(
                    array(
                        'news_id' => $id,
                        'category_id' => $param['category_id']
                    )
                );
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
        if (isset($param['locale'])) {
            $set['locale'] = $param['locale'];
        } 
        if (isset($param['title'])) {
            $set['title'] = $param['title'];
        } 
        if (isset($param['short'])) {
            $set['short'] = $param['short'];
        } 
        if (isset($param['content'])) {
            $set['content'] = $param['content'];
        }
        $image = new Images();
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (!empty($uploadResult['url_image'])) {                    
                if (!empty($self['image_id'])) { 
                    $param['image_id'] = $self['image_id']; 
                    $image->updateInfo(array(
                        'src' => 'news',
                        'id' => $self['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'src' => 'news',
                        'src_id' => $self['news_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
            }
        } else {            
            if (!empty($self['image_id']) && empty($param['image_id'])) {
                $image->remove(array(
                    'id' => $self['image_id'],
                    'src' => 'news'
                ));
            }
        }
        if (isset($param['image_id'])) {
            $set['image_id'] = $param['image_id'];
        }        
        if (self::update(
            array(
                'set' => $set,
                'where' => array(
                    '_id' => $param['_id']
                ),
            )
        )) {
            if (empty($param['category_id'])) {
                $param['category_id'] = array();
            }
            $newsHasCategories = new NewsHasCategories();
            $newsHasCategories->addUpdate(
                array(
                    'news_id' => $self['news_id'],
                    'category_id' => $param['category_id']
                )
            );
            return true;
        }
        return false;
    }    

    public function getDetail($param)
    {
        $sql = new Sql(self::getDb());
        $select = $sql->select()
            ->from(static::$tableName)  
            ->columns(array(                
                'news_id', 
                '_id', 
                'locale', 
                'title',
                'short',
                'content',
                'image_id',
            ))            
            ->where("_id = ". self::quote($param['_id']));                      
        $result = self::response(
            static::selectQuery($sql->getSqlStringForSqlObject($select)), 
            self::RETURN_TYPE_ONE
        );
        if ($result) {
            $newsHasCategories = new NewsHasCategories();
            $result['category_id'] = Arr::field(
                $newsHasCategories->find(
                    array(            
                        'where' => array(
                            'news_id' => $result['news_id']
                        )
                    )
                ),
                'category_id'
            );
        }
        return $result;
    }
    
}
