<?php

namespace Api\Bus\Users;

use Zend\View\Model\ViewModel;
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
class Register extends AbstractBus {
    
    protected $_required = array(
        'website_id',
        'email',
        'password',
        'name',
        'mobile',        
        'country_code',
        'state_code',
        'city_code',
        'street',
        'address_name',
    );
	
	protected $_email_format = array(
        'email',
    );
    
    public function operateDB($sm, $param) {
        try {          
            $model = $sm->get('Users');
            $this->_response = $model->register($param, $userId);
            if (!empty($model->error())) {
                return $this->result($model->error());
            }
            if (!empty($userId) && isset($param['generate_voucher'])) {
                if (empty($param['voucher_min_total_money'])) {
                    $param['voucher_min_total_money'] = 0;
                }
                if (empty($param['voucher_type'])) {
                    $param['voucher_type'] = 0;
                }
                if (empty($param['expired'])) {
                    $param['expired'] = strtotime('+1 week');
                }
                $voucher = $sm->get('Vouchers');
                $_id = $voucher->add(array(
                    'website_id' => $param['website_id'],
                    'amount' => $param['voucher_amount'],
                    'type' => $param['voucher_type'],
                    'expired' => $param['voucher_expired'],
                    'min_total_money' => $param['voucher_min_total_money'],
                    'user_id' => $userId
                ));
                if (!empty($voucher->error())) {
                    return $this->result($voucher->error());
                }
                if (!empty($param['send_email'])) { 
                    $data = $voucher->getDetail(array(
                        'website_id' => $param['website_id'],
                        '_id' => $_id,
                    ));
                    if (!empty($data['user_email'])) {                  
                        $mail = $sm->get("Mail");        
                        $viewModel = new ViewModel(array('data' => $data));
                        $viewModel->setTemplate('email/register');
                        $mail->setTo($data['user_email']);                     
                        $mail->setSubject(sprintf('%s TANG BAN MA GIAM GIA %s', $data['website_url'], $data['code']));
                        $mail->setBody($viewModel);
                        $mail->send();
                    }
                }
            }
            return $this->result();
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
