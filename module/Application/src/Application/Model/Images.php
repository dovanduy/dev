<?php

namespace Application\Model;

use Application\Lib\Api;
use Application\Lib\Cache;

class Images {    
    
    public static function removeCache($id, $src = 'products')
    {
        $key = IMAGES_DETAIL . $src . '_' . $id;         
        Cache::remove($key);
    }
    
    public static function getUrl($id, $src = 'products', $force = false) {
        $key = IMAGES_DETAIL . $src . '_' . $id;     
        if (!($data = Cache::get($key)) || $force == true) {
            $data = Api::call('url_images_detail', array(
                'id' => $id,
                'src' => $src
            ));
            if (!empty($data)) {
                Cache::set($key, $data);
            }
        }        
        return !empty($data['url_image']) ? $data['url_image'] : '';
    }
	
	public static function getAll($srcId, $src = 'places') {
        $images = Api::call('url_images_all', array(
            'src_id' => $srcId,
            'src' => $src,
        ));
        $data = array(
            'url_image' => array(),
            'image_id' => array(),
        );
        foreach ($images as $i => $image) {
            $data['url_image']['url_image' . ($i+1)] = $image['url_image'];
            $data['image_id']['url_image' . ($i+1)] = $image['image_id'];
        }
        return $data;
    }
    
    public static function getAllHasColor($srcId, $src = 'products') {
        $images = Api::call('url_images_allhascolor', array(
            'src_id' => $srcId,
            'src' => $src,
        ));
        $data = array(
            'url_image' => array(),
            'image_id' => array(),
            'color_id' => array(),
            'is_main' => ''
        );
        foreach ($images as $i => $image) {
            $data['url_image']['url_image' . ($i+1)] = $image['url_image'];
            $data['image_id']['url_image' . ($i+1)] = $image['image_id'];
            $data['color_id']['url_image' . ($i+1)] = $image['color_id'];
            if ($image['is_main'] == 1) {
                $data['main'] = 'url_image' . ($i+1);
            }
        }
        return $data;
    }

}
