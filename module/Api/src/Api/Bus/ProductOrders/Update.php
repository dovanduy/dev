<?php

namespace Api\Bus\ProductOrders;

use Zend\View\Model\ViewModel;
use Api\Bus\AbstractBus;

/**
 * update categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class Update extends AbstractBus {

    protected $_required = array(
        '_id',
    );
    
    public function operateDB($sm, $param) {
        try {   
            $model = $sm->get('ProductOrders');
            $this->_response = $model->updateInfo($param);
            if (empty($model->error()) && isset($param['send_email'])) { 
                if (!empty($param['is_shipping']) || !empty($param['is_done'])) {
                    if (isset($param['get_detail'])) {
                        $order = $this->_response;
                    } else {
                        $order = $model->getDetail(array(
                            'website_id' => $param['website_id'],
                            '_id' => $param['_id'],
                        )); 
                    }
                    if (!empty($order['user_email'])) {                  
                        $mail = $sm->get("Mail");        
                        $viewModel = new ViewModel(array('data' => $order));
                        if (!empty($param['is_shipping'])) {
                            $viewModel->setTemplate('email/order_shipping');
                            $mail->setSubject(sprintf('%s đang giao đơn hàng %s', $order['website_url'], $order['code']));
                        } else {
                            $viewModel->setTemplate('email/order_done');
                            $mail->setSubject(sprintf('%s đã giao hoàn tất đơn hàng %s', $order['website_url'], $order['code']));
                        }
                        $mail->setTo($order['user_email']); 
                        $mail->setBody($viewModel);
                        $mail->send();
                    }
                }                
            }
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
