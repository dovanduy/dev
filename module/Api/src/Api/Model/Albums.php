<?php

namespace Api\Model;

use Application\Lib\Util;
use Application\Lib\Log;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Albums extends AbstractModel {
    
    protected static $properties = array(
		'album_id',
		'image_url',
		'artist',
		'title',
		'created_at',
		'user_id',
		'iseq',
	);
    
    protected static $tableName = 'albums';

    public function add($param) {      
        /*
        $user = (new Users(self::$db))->find(
            array(            
                'where' => array('user_id' => $param['user_id'])
            ), 
            self::RETURN_TYPE_ONE
        );         
        if (empty($user)) {
            self::errorNotExist('user_id');
            return false;
        }
        * 
        */
        $insert = array();
        if (isset($param['artist'])) {
            $insert['artist'] = $param['artist'];
        }
        if (isset($param['title'])) {
            $insert['title'] = $param['title'];
        }  
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['image_url'])) {
                $insert['image_url'] = $uploadResult['image_url'];
            }
        }
        $insert['created_at'] = self::now();
        return self::insert($insert);        
    }
    
    public function updateAlbum($param) {       
        $self = self::find(array(            
            'where' => array('album_id' => $param['album_id']),
            self::RETURN_TYPE_ONE
        ));   
        if (empty($self)) {
            self::errorNotExist('album_id');
            return false;
        } 
        /*
        if (isset($param['user_id'])) {
            $user = (new Users(self::$db))->find(
                array(            
                    'where' => array('user_id' => $param['user_id'])
                ), 
                self::RETURN_TYPE_ONE
            );
            if (empty($user)) {
                self::errorNotExist('user_id');
                return false;
            }
        }   
        * 
        */   
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['image_url'])) {
                $param['image_url'] = $uploadResult['image_url'];
            }
        }
        $set = array();
        if (isset($param['artist'])) {
            $set['artist'] = $param['artist'];
        }
        if (isset($param['title'])) {
            $set['title'] = $param['title'];
        }
        if (isset($param['user_id'])) {
            $set['user_id'] = $param['user_id'];
        }
        if (isset($param['image_url'])) {
            $set['image_url'] = $param['image_url'];
        }        
        return self::update(array(
            'set' => $set,
            'where' => array(
                'album_id' => $param['album_id']
            ),
        ));      
    } 
    
    public function getList($param) {
        $sql = new Sql(static::$db);
        $select = $sql->select()
            ->from(static::$tableName)  
            ->join(
                'users', 
                static::$tableName . '.user_id = users.user_id',
                array('email', 'name'),
                'left'
            );
        if (!empty($param['user_id'])) {            
            $select->where(array(static::$tableName . '.user_id' => $param['user_id']));
        }
        if (!empty($param['album_id'])) {            
            $select->where(array(static::$tableName . '.album_id' => $param['album_id']));
        }
        if (!empty($param['artist'])) {
            $select->where(new Expression(static::$tableName . ".artist LIKE '%{$param['artist']}%'"));
        }      
        if (!empty($param['title'])) {
            $select->where(new Expression(static::$tableName . ".title LIKE '%{$param['title']}%'"));
        }
        if (!empty($param['limit'])) {
            $select->limit($param['limit']);
            if (!empty($param['page'])) {
                $select->offset(static::getOffset($param['page'], $param['limit']));
            }
        }
        if (!empty($param['sort'])) {
            $sortExplode = explode('-', $param['sort']);
            if (count($sortExplode) == 2) {
                if (empty($sortExplode[1])) {
                    $sortExplode[1] = 'ASC';
                }
                $select->order(static::$tableName . '.' . $sortExplode[0] . ' ' . $sortExplode[1]);
            }
        } else {
            $select->order(static::$tableName . '.album_id DESC');
        }          
        $selectString = $sql->getSqlStringForSqlObject($select);
        return array(
            'count' => static::count($selectString),
            'limit' => $param['limit'],
            'data' => static::toArray(static::selectQuery($selectString)), 
        );    
    }
    
    public function deleteAlbum($param) {      
        $self = self::find($param);           
        if (empty($self)) {
            self::errorNotExist('album_id');
            return false;
        }       
        return parent::delete($param);        
    }
    
    public function updateIseq($param) {       
        if (empty($param['iseq'])) {
            return false;
        }        
        $param['iseq'] = \Zend\Json\Decoder::decode($param['iseq'], \Zend\Json\Json::TYPE_ARRAY);        
        foreach ($param['iseq'] as $albumId => $iseq) {
            if (!self::update(array(
                'set' => array('iseq' => $iseq),
                'where' => array(
                    'album_id' => $albumId
                ),
            ))) {
                return false;
            }  
        }
        return true;
    } 
    
}
