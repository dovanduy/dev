<?php

namespace Api\Model;

use Application\Lib\Util;
class Categories extends AbstractModel {
    
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
    
    protected static $tableName = 'categories';
    public function getList($param)
    {
        $result = self::spQuery(
            'categories_getall_a',
            self::spParameter(
                array(
                    'role' => 1,
                    'status' => 1,
                    'condition_ext' =>
                        self::spCondition(array(
                            'name',
                            'category_id',
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
            'limit' => $param['limit']
        );
    }
    public function getAll($params){
        $result = self::spQuery(
            'categories_combox',
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
        $result = self::spQuery(
            'categories_add',
            self::spParameter(
                array(
                    'login_admin_id' => 0,
                    'name' => '',
                    'short' => '',
                    'content' => '',
                    'iseq' => 1,
                    'image_id' => '',
                    'parent_id' => '',
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
        $id = !empty($result['_id']) ? $result['_id'] : '';
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image']) && !empty($id)) {
                if (self::$sm !== null) {
                    $image = new Images();
                    $image->setDb(self::$sm->get('db_images'));
                    $image_id = $image->add(array(
                        'src' => 'categories',
                        'src_id' => $id,
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                    $image->setDb(null);
                    if (!self::update(array(
                        'set' => array('image_id' => $image_id),
                        'where' => array(
                            '_id' => $id
                        ),
                    ))) {
                        return false;
                    }
                }
            }
        }
        return $id;
    }

    public function updateInfo($param)
    {
        if ($_FILES) {
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image'])) {
                if (self::$sm !== null) {
                    $image = new Images();
                    $image->setDb(self::$sm->get('db_images'));
                    $param['image_id'] = $image->add(array(
                        'src' => 'categories',
                        'src_id' => $param['_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                    $image->setDb(null);
                }
            }
        }
        $result = self::spQuery(
            'categories_update',
            self::spParameter(
                array(
                    'login_admin_id' => 0,
                    '_id' => '',
                    'image_id' => '',
                    'parent_id' => '',
                    'iseq' => 1,
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        return empty($result['errCode']) ? true : false;
    }

    public function addUpdateLocale($param)
    {
        $result = self::spQuery(
            'categories_addupdate_locale',
            self::spParameter(
                array(
                    'login_admin_id' => 0,
                    '_id' => '',
                    'is_locale' => 0,
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
            'categories_get',
            self::spParameter(
                array(
                    '_id' => '',
                    'locale' => \Application\Module::getConfig('general.default_locale')
                ),
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        return !empty($result) ? $result : array();
    }
    
}
