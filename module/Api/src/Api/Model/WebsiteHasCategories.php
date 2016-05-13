<?php

namespace Api\Model;

class WebsiteHasCategories extends AbstractModel {
    
    protected static $properties = array(
        'website_id',        
        'category_id',
    );
    
    protected static $primaryKey = array('website_id', 'category_id');
    
    protected static $tableName = 'website_has_categories';
    
    public function addUpdate($param)
    {        
        if (!is_array($param['category_id'])) {
            $param['category_id'] = array($param['category_id']);
        }
        $newsCategories = self::find(
            array(     
                'where' => array(
                    'website_id' => $param['website_id']
                )
            )
        );
        $categoryValues = array();                     
        foreach ($param['category_id'] as $categoryId) {                
            $categoryValues[] = array(
                'website_id' => $param['website_id'],
                'category_id' => $categoryId,
            );
            if (!self::batchInsert($categoryValues)) {
                return false;
            }
        }           
        if (!empty($newsCategories)) {
            foreach ($newsCategories as $category) {                
                if (!in_array($category['category_id'], $param['category_id'])) {
                    if (!self::delete(
                        array(
                            'where' => array(
                                'website_id' => $param['website_id'],
                                'category_id' => $category['category_id']
                            ),
                        )
                    )) {
                        return false;
                    }
                }
            }
        }
        return true;        
    }
    
}
