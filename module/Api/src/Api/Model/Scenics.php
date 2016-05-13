<?php
namespace Api\Model;
//use \Exception;
class Scenics extends AbstractModel 
{
    protected static $limit = 10;
    protected static $properties = array(
		'scenic_id',
        '_id',
		'is_locale',
		'lat',
		'lng',
		'compass',
		'distance_center',
		'distance_center_unit',
		'street',
		'city_code',
		'state_code',
		'country_code',
		'image_id',
		'count_image',
		'count_comment',
		'count_like',
		'count_share',
		'last_comment_id',
		'created_at',
		'modified_at',
		'gmt_time',
		'priority',
	);
    
    protected static $tableName = 'scenics';
    public function getList( $param ) {
            
    	$result = self::spQuery(
            'scenics_getall_a', 
            self::spParameter(
	            array(
	                'role'   => 0,
	                'status' => -1,
                    'condition_ext' => 
    	                self::spCondition( array(
    	                	'country_code',
    	                	'state_code'  ,
    	                	'city_code'   ,
    	                	'street'      ,
    	                ) , $param ),
	                'keyword' => '',
	                'orderby_field' => 'name-asc',
	                'page'    => 1,
	                'limit'   => self::$limit,
	            )
            ),    
            self::RETURN_TYPE_MULTIPLE_RESULTSET
        );
        return array(            
            'count'  => isset( $result[0][0]['foundRows']) ? 
                        $result[0][0]['foundRows'] : 0,
            'data'  => isset($result[1]) ? $result[1] : array(),
            'limit'  => isset( $param['limit'] ) ? 
                        (int)$param['limit']  : self::$limit,
        );
    }
    
    public function add( $param = array() ) 
    {
        
        $result = self::spQuery(
            'scenics_add', 
            self::spParameter(
                array(
                    'role'    => 0,
                    'lat'     => 0,
                    'long'    => 0,
                    'compass' => '',
                    'distance_center'      => 0,
                    'distance_center_unit' => '',
                    'street'               => '',
                    'city_code'            => '',
                    'state_code'           => '',
                    'country_code'         => '',
                    'name'                 => '',
                    'tag'                  => '',
                    'short'                => '',
                    'content'              => '',
                    'content_mobile'       => '',
                    'image_id'             => '',
                ), 
                $param
            ),
            self::RETURN_TYPE_ONE
        );
        if ( !empty($result['errCode']) ) {
            switch ($result['errCode']) {
                case 5:
                    self::errorNotExist('country_code');
                    return false;
                case 6:
                    self::errorNotExist('state_code');
                    return false;
            }
        }
        return !empty($result['_id']) ? $result['_id'] : '';
        
    }
    
    public function updateInfo($param = array() )
    { 
        
        if ($_FILES) {            
            $uploadResult = Util::uploadImage();
            if (isset($uploadResult['url_image'])) {
                if (self::$sm !== null) {                    
                    $image = new Images();
                    $image->setDb(self::$sm->get('db_images'));
                    $param['image_id'] = $image->add(array(
                        'src' => 'scenic',
                        'src_id' => $param['_id'],
                        'url_image' => $uploadResult['url_image'],
                        'is_main' => 0,
                    ));
                    $image->setDb(null);
                }            
            }
        }
        $result = self::spQuery(
            'scenics_update',
            self::spParameter(
                array(
                    'login_id' => 0,
                    //'role'   => isset($param['role']) ? $param['role'] : 1,
                    'scenic_id' => '',
                    'lat' => 0,
                    'long' => 0,
                    'compass' => '',
                    'distance_center' => 0,
                    'distance_center_unit' => '',
                    'street' => '',
                    'city_code' => '',
                    'state_code' => '',
                    'country_code' => '',
                    'name' => '',
                    'tag' => '',
                    'short' => '',
                    'content' => '',
                    'content_mobile' => '',
                    'image_id' => '',
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
            'scenics_addupdate_locale',
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
            'scenics_get',
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
