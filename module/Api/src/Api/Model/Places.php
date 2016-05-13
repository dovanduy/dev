<?php

namespace Api\Model;

use Application\Lib\Util;

class Places extends AbstractModel 
{  
    protected static $properties = array(
		'place_id',
		'_id',
		'is_locale',
		'state_code',
		'country_code',
		'lat',
		'lng',
		'image_id',
		'url_website',
		'count_managed',
		'count_image',
		'count_read',
		'count_like',
		'count_comment',
		'count_rate',
		'count_rate_person',
		'count_follow',
		'count_favourite',
		'count_share',
		'last_comment_id',
		'is_verified',
		'created_at',
		'modified_at',
		'timezone',
		'priority',
	);
    
    protected static $tableName = 'places';
        
    public function getList($param) 
    {
        $result = self::spQuery(
            'places_getall_a', 
            self::spParameter(
                array(
                    'role' => 1,
                    'status' => 1,
                    'condition_ext' => 
                        self::spCondition(array(
                            'name', 
                            'country_code', 
                            'state_code', 
                            'is_verified'
                        ),
                        $param),
                    'keyword' => '',
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

    public function add($param)
    {
        $_id = mongo_id();  // places._id
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image']) && self::$sm !== null) {
                $image = new Images();
                $image->setDb(self::$sm->get('db_images'));
                $param['image_id'] = $image->add(array(
                    'src' => 'places',
                    'src_id' => $_id,
                    'url_image' => $uploadResult['url_image'],
                    'is_main' => 1,
                ));
                $image->setDb(null);                    
            }
        }
        $result = self::spQuery(
            'places_add', 
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => $_id,
                    'name' => '',
                    'tag' => '',
                    'short' => '',
                    'content' => '',
                    'content_mobile' => '',
                    'state_code' => '',
                    'country_code' => '',
                    'lat' => 0,
                    'lng' => 0,
                    'url_website' => '',
                    'image_id' => 0,
                    'count_image' => 0,
                    'is_verified' => 0,
                    'expired_at' => null,
                ), 
                $param
            ),
            self::RETURN_TYPE_ONE
        );        
        // catch error code
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 5:
                    self::errorNotExist('country_code');
                    return false;
                case 6:
                    self::errorNotExist('state_code');
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
                        'src' => 'places',
                        '_id' => $param['image_id'],
                        'url_image' => $uploadResult['url_image']
                    ));
                } else {
                    $param['image_id'] = $image->add(array(
                        'login_id' => 0,
                        'src' => 'places',
                        'src_id' => $param['_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 1,
                    ));
                }
                $image->setDb(null);
            }
        }
        $result = self::spQuery(
            'places_update',
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => '',
                    'lat' => 0,
                    'lng' => 0,
                    'state_code' => '',
                    'country_code' => '',
                    'is_verified' => 0,
                    'expired_at' => null,
                    'url_website' => '',
                    'priority' => 0,
                    'image_id' => null
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
            'places_addupdate_locale',
            self::spParameter(
                array(
                    'login_id' => 0,
                    '_id' => '',
                    'locale' => null,
                    'name' => '',
                    'tag' => '',
                    'short' => '',
                    'content' => '',
                    'content_mobile' => ''
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
            'places_get',
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
