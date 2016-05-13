<?php

namespace Api\Model;

use Application\Lib\Util;

class PlaceCategories extends AbstractModel {
    
    protected static $properties = array(
		'category_id',
		'_id',
		'is_locale',
		'image_id',
		'co_hotel',
		'created_at',
		'modified_at',
		'iseq',
		'parent_id',
	);
    
    protected static $primaryKey = 'category_id';
    
    protected static $tableName = 'place_categories';
    
    public function getList($param)
    {
        $result = self::spQuery(
            'place_categories_getall_a',
            self::spParameter(
                array(
                    'role' => 1,
                    'status' => 1,
                    'condition_ext' =>
                        self::spCondition(array(
                            'name',                           
                            'parent_id',
                        ),
                        $param),
                    'sort' => '',
                    'page' => 1,
                    'limit' => 10
                ),
                $param
            ),
            self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        return array(
            'count' => isset($result[0][0]['foundRows']) ? $result[0][0]['foundRows'] : 0,
            'data'  => isset($result[1]) ? $result[1] : array(),
            'limit' => isset($param['limit']) ? $param['limit'] : 10,
        );
    }
    
    public function getAll($params){
        $result = self::spQuery(
            'place_categories_combox',
            self::spParameter(
                array(
                    'parent_id' =>  0,
                    'is_locale' =>  0,
                    'locale' => \Application\Module::getConfig('general.default_locale'),
                )
            ),
            self::RETURN_TYPE_ALL
        );
        return $result;
    }    
    
    public function add($param)
    {
        $_id = mongo_id();  // place_categories._id
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image']) && self::$sm !== null) {
                $image = new Images();
                $image->setDb(self::$sm->get('db_images'));
                $param['image_id'] = $image->add(array(
                    'src' => 'place_categories',
                    'src_id' => $_id,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
                $image->setDb(null);                    
            }          
        }
        $result = self::spQuery(
            'place_categories_add',
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => $_id,
                    'name' => '',
                    'short' => '',
                    'content' => '',
                    'iseq' => 0,
                    'image_id' => null,
                    'parent_id' => null,
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );

        // catch error code
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 3:
                    self::errorNotExist('parent_id');
                    return false;
            }
        }

        return $_id;
    }

    public function updateInfo($param)
    {
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image']) && self::$sm !== null) {
                $image = new Images();
                $image->setDb(self::$sm->get('db_images'));
                if (!empty($param['image_id'])) {
                    $image->update(array(
                        'login_id' => 0,
                        'src' => 'place_categories',
                        '_id' => $param['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'login_id' => 0,
                        'src' => 'place_categories',
                        'src_id' => $param['_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
                $image->setDb(null);
            }
        }
        $result = self::spQuery(
            'place_categories_update',
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => '',
                    'image_id' => 0,
                    'parent_id' => '',
                    'iseq' => 0,
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        
        // catch error code
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('parent_id');
                    return false;
            }
        }
        
        return empty($result['errCode']) ? true : false;
    }

    public function addUpdateLocale($param)
    {
        $result = self::spQuery(
            'place_categories_addupdate_locale',
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => '',
                    'locale' => null,
                    'name' => '',
                    'short' => '',
                    'content' => '',
                    'parent_id' => '',
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        return !empty($result['errCode']) ? false : true;
    }

    public function getDetail($param)
    {
        $result = self::spQuery(
            'place_categories_get',
            self::spParameter(
                array(
                    '_id' => '',
                    'locale' => \Application\Module::getConfig('general.default_locale')
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        return $result;
    }
    
    public function updateIseq($param) {  
        parent::$primaryKey = self::$primaryKey;
        parent::$properties = self::$properties;
        return parent::updateIseq($param);
    }
    
}
