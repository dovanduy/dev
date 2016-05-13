<?php

namespace Api\Model;

class festivals extends AbstractModel
{

    protected static $properties = array(
        'festival_id',
        '_id',
        'is_locale',
        'street',
        'city_code',
        'state_code',
        'country_code',
        'starts_at',
        'starts_time',
        'ends_at',
        'ends_time',
        'weekly',
        'regularly',
        'created_at',
        'modified_at',
        'gmt_time',
        'lat',
        'lng',
        'image_id',
        'count_image',
        'count_read',
        'count_like',
        'count_share',
        'count_comment',
        'last_comment_id',
        'is_verified',
        'created_by',
        'modified_by',

    );

    protected static $tableName = 'festivals';

    public function add($param)
    {
        $_id = mongo_id();
        $result = self::spQuery(
            'festivals_add',
            array(
                'login_id' => isset($param['login_id']) ? $param['login_id'] : '',
                '_id' => $_id,
                'lat' => isset($param['lat']) ? $param['lat'] : 0,
                'lng' => isset($param['lng']) ? $param['lng'] : 0,
                'street' => isset($param['street']) ? $param['street'] : '',
                'city_code' => isset($param['city_code']) ? $param['city_code'] : '',
                'state_code' => isset($param['state_code']) ? $param['state_code'] : '',
                'country_code' => isset($param['country_code']) ? $param['country_code'] : '',
                'starts_at' => isset($param['starts_at']) ? $param['starts_at'] : date('Y-m-d h:i:s'),
                'starts_time' => isset($param['starts_time']) ? $param['starts_time'] : date('h:i:s'),
                'ends_at' => isset($param['ends_at']) ? $param['ends_at'] : date('Y-m-d h:i:s'),
                'ends_time' => isset($param['ends_time']) ? $param['ends_time'] : date('h:i:s'),
                'weekly' => isset($param['weekly']) ? $param['weekly'] : 0,
                'regularly' => isset($param['regularly']) ? $param['regularly'] : 0,
                'name' => isset($param['name']) ? $param['name'] : '',
                'tag' => isset($param['tag']) ? $param['tag'] : '',
                'short' => isset($param['short']) ? $param['short'] : '',
                'content' => isset($param['content']) ? $param['content'] : '',
                'content_mobile' => isset($param['content_mobile']) ? $param['content_mobile'] : '',
                'image_id' => isset($param['image_id']) ? $param['image_id'] : '',
                'count_image' => isset($param['count_image']) ? $param['count_image'] : 0,
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('state_code');
                    return false;
                case 2:
                    self::errorNotExist('country_code');
                    return false;
                case 3:
                    self::errorNotExist('name');
                    return false;
                case 4:
                    self::errorNotExist('content');
                    return false;
            }
        }
        return !empty($result['festival_id']) ? $result['festival_id'] : 0;
    }

    public function save($param)
    {
        if (!isset($param['festival_id'])) {
            return self::add($param);
        }
        $result = self::spQuery('festivals_update',
            array(
                'login_id' => isset($param['login_id']) ? $param['login_id'] : '',
                '_id' => isset($param['_id']) ? $param['_id'] : '',
                'lat' => isset($param['lat']) ? $param['lat'] : 0,
                'lng' => isset($param['lng']) ? $param['lng'] : 0,
                'street' => isset($param['street']) ? $param['street'] : '',
                'city_code' => isset($param['city_code']) ? $param['city_code'] : '',
                'state_code' => isset($param['state_code']) ? $param['state_code'] : '',
                'country_code' => isset($param['country_code']) ? $param['country_code'] : '',
                'starts_at' => isset($param['starts_at']) ? $param['starts_at'] : date('Y-m-d h:i:s'),
                'starts_time' => isset($param['starts_time']) ? $param['starts_time'] : date('h:i:s'),
                'ends_at' => isset($param['ends_at']) ? $param['ends_at'] : date('Y-m-d h:i:s'),
                'ends_time' => isset($param['ends_time']) ? $param['ends_time'] : date('h:i:s'),
                'weekly' => isset($param['weekly']) ? $param['weekly'] : 0,
                'regularly' => isset($param['regularly']) ? $param['regularly'] : 0,
                'imge_id' => isset($param['imge_id']) ? $param['imge_id'] : '',
                'count_image' => isset($param['count_image']) ? $param['count_image'] : 0,

            ), self::RETURN_TYPE_ONE);
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('festival_id');
                    return false;
                case 2:
                    self::errorNotExist('state_code');
                    return false;
                case 3:
                    self::errorNotExist('country_code');
                    return false;
            }
        }
        return true;
    }

    public function getList($param)
    {
        $result = self::spQuery(
            'festivals_getall_a',
            self::spParameter(
                array(
                    'role' => 1,
                    'status' => 1,
                    'condition_ext' =>
                        self::spCondition(array(
                            'street',
                            'city_code',
                            'state_code',
                            'country_code'
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
            'data' => isset($result[1]) ? $result[1] : array(),
            'limit' => isset($param['limit']) ? $param['limit'] : 10
        );
    }

    public function getDetail($params)
    {
        $result = self::spQuery(
            'festivals_get',
            self::spParameter(
                array(
                    '_id' => '',
                    'locale' => \Application\Module::getConfig('general.default_locale')
                ),
                $params
            ),
            self::RETURN_TYPE_ONE
        );
        return !empty($result) ? $result : array();
    }

    public function addUpdateLocale($param)
    {
        $result = self::spQuery(
            'festivals_addupdate_locale',
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

}
