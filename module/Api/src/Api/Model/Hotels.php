<?php

namespace Api\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Expression;

class Hotels extends AbstractModel {

    protected static $properties = array(
        'hotel_id',
        'category_id',
        'star',
        'street',
        'city_code',
        'state_code',
        'country_code',
        'tel',
        'fax',
        'url_website',
        'hotline',
        'email',
        'created_at',
        'modified_at',
        'image_id',
        'co_read',
        'co_like',
        'co_comment',
        'co_rated',
        'co_rated_person',
        'last_comment_id',
        'is_locale',
        'lat',
        'lng',
        'is_verified',
        'expires_at'
    );

    protected static $tableName = 'hotels';

    public function add($param) {
       $result = self::spQuery(
            'hotels_add',
            array(
                'category_id' => $param['category_id'],
                'star' => $param['star'],
                'street' => $param['street'],
                'city_code' => $param['city_code'],
                'state_code' => $param['state_code'],
                'country_code' => $param['country_code'],
                'tel' => $param['tel'],
                'fax' => $param['fax'],
                'url_website' => $param['url_website'],
                'hotline' => $param['hotline'],
                'email' => $param['email'],
                'image_id' => $param['image_id'],
                'co_read' => $param['co_read'],
                'co_like' => $param['co_like'],
                'co_comment' => $param['co_comment'],
                'co_rated' => $param['co_rated'],
                'co_rated_person' => $param['co_rated_person'],
                'last_comment_id' => $param['last_comment_id'],
                'is_locale' => $param['is_locale'],
                'lat' => $param['lat'],
                'lng' => $param['lng'],
                'is_verified' => $param['is_verified'],
                'expires_at' => empty($param['expires_at']) ? $param['expires_at'] : null,
                'name' => $param['name'],
                'short' => $param['short'],
                'tag' => $param['tag'],
                'content' => $param['content'],
                'ft_search' => $param['ft_search'],
            ),
            self::RETURN_TYPE_ONE
        );
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('category_id');
                    return false;
                case 2:
                    self::errorNotExist('street');
                    return false;
                case 3:
                    self::errorNotExist('city_code');
                    return false;
                case 4:
                    self::errorNotExist('state_code');
                    return false;
                case 5:
                    self::errorNotExist('country_code');
                    return false;
                case 6:
                    self::errorNotExist('tel');
                    return false;
                case 7:
                    self::errorNotExist('fax');
                    return false;
                case 8:
                    self::errorNotExist('url_website');
                    return false;
                case 9:
                    self::errorNotExist('hotline');
                    return false;
                case 10:
                    self::errorNotExist('email');
                    return false;

                case 11:
                    self::errorNotExist('co_read');
                    return false;
                case 12:
                    self::errorNotExist('co_like');
                    return false;
                case 13:
                    self::errorNotExist('co_comment');
                    return false;
                case 14:
                    self::errorNotExist('co_rated');
                    return false;
                case 15:
                    self::errorNotExist('co_rated_person');
                    return false;
                case 16:
                    self::errorNotExist('last_comment_id');
                    return false;
                case 17:
                    self::errorNotExist('is_locale');
                    return false;
                case 18:
                    self::errorNotExist('lat');
                    return false;
                case 19:
                    self::errorNotExist('lng');
                    return false;
                case 20:
                    self::errorNotExist('is_verified');
                    return false;
                case 21:
                    self::errorNotExist('name');
                    return false;
                case 22:
                    self::errorNotExist('short');
                    return false;
                case 23:
                    self::errorNotExist('tag');
                    return false;
                case 24:
                    self::errorNotExist('content');
                    return false;
                case 25:
                    self::errorNotExist('ft_search');
                    return false;
            }
        }
        return !empty($result['hotel_id']) ? $result['hotel_id'] : 0;
    }
    
    public function save($param) {
        if (!isset($param['hotel_id'])) {
            return self::add($param);
        }
        $self = self::find(array(
                'where' => array('hotel_id' => $param['hotel_id']),
                self::RETURN_TYPE_ONE
        ));
        if (empty($self)) {
            self::errorNotExist('hotel_id');
            return false;
        }
        $result = self::spQuery ( 'hotels_update',
             array (
                'hotel_id' => isset ( $param ['hotel_id'] ) ? $param ['hotel_id'] : '',
                'category_id' => $param['category_id'],
                'star' => $param['star'],
                'street' => $param['street'],
                'city_code' => $param['city_code'],
                'state_code' => $param['state_code'],
                'country_code' => $param['country_code'],
                'tel' => $param['tel'],
                'fax' => $param['fax'],
                'url_website' => $param['url_website'],
                'hotline' => $param['hotline'],
                'email' => $param['email'],
                'image_id' => isset($param['image_id']) ? $param['image_id'] : null,
                'co_read' => $param['co_read'],
                'co_like' => $param['co_like'],
                'co_comment' => $param['co_comment'],
                'co_rated' => $param['co_rated'],
                'co_rated_person' => $param['co_rated_person'],
                'last_comment_id' => $param['last_comment_id'],
                'is_locale' => $param['is_locale'],
                'lat' => $param['lat'],
                'lng' => $param['lng'],
                'is_verified' => $param['is_verified'],
                'expires_at' => empty($param['expires_at']) ? $param['expires_at'] : null,
                'name' => $param['name'],
                'short' => $param['short'],
                'tag' => $param['tag'],
                'content' => $param['content'],
                'ft_search' => $param['ft_search']
        ), self::RETURN_TYPE_ONE );
        if (!empty($result['errCode'])) {
            switch ($result['errCode']) {
                case 1:
                    self::errorNotExist('service_id');
                    return false;
                case 2:
                    self::errorNotExist('tag');
                    return false;
                case 3:
                    self::errorNotExist('type');
                    return false;
                case 4:
                    self::errorNotExist('iseq');
                    return false;
                case 5:
                    self::errorNotExist('parent_id');
                    return false;
            }
        }
        return true;
    }
    
    public function getList($param) {
        $result = self::spQuery(
            'hotels_getall_a',
            array(
                'role'   => isset($param['role']) ? $param['role'] : 1,
                'status' => isset($param['status']) ? $param['status'] : 1,
                'porder_field' => isset($params['sort']) ? $params['sort'] : '',
                'pcondition_ext' => '',
                'page'   => isset($param['page']) ? $param['page'] : 1,
                'limit'  => isset($param['limit']) ? $param['limit'] : 10,
            ),
            self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        return array(
            'count' => isset($result[0][0]['foundRows']) ? $result[0][0]['foundRows'] : 0,
            'data'  => isset($result[1]) ? $result[1] : array(),
            'limit' => isset($param['limit']) ? $param['limit'] : 10
        );
    }
    
    public function getDetail($params) {
        $result = self::spQuery(
                'hotels_get',
                array(
                    'photel_id' => isset($params['hotel_id']) ? $params['hotel_id'] : 1,
                    'plocale' => isset($params['locale']) ? $params['locale'] : '',
                ),
                self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        if (isset($result[0][0])) {
            return $result[0][0];
        } else if (isset($result[1][0])) {
            return $result[1][0];
        }
        return array();
    }
}
