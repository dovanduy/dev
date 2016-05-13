<?php

namespace Api\Model;

class NewsHasCategories extends AbstractModel {
    
    protected static $properties = array(
        'news_id',        
        'category_id',
    );
    
    protected static $primaryKey = array('news_id', 'category_id');
    
    protected static $tableName = 'news_has_categories';
    
    public function addUpdate($param)
    {        
        if (!is_array($param['category_id'])) {
            $param['category_id'] = array($param['category_id']);
        }
        $newsCategories = self::find(
            array(     
                'where' => array(
                    'news_id' => $param['news_id']
                )
            )
        );
        $categoryValues = array();                     
        foreach ($param['category_id'] as $categoryId) {                
            $categoryValues[] = array(
                'news_id' => $param['news_id'],
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
                                'news_id' => $param['news_id'],
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
