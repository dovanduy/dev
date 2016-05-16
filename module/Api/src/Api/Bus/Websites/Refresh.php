<?php

namespace Api\Bus\Websites;

use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Refresh extends AbstractBus {
    
    protected $_required = array(
        'website_id',
    );
    
    public function operateDB($sm, $param) {
        try {          
			$productModel = $sm->get('Products'); 
            $this->_response = $productModel->updateNoUrlId($param);
            return $this->result($productModel->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
